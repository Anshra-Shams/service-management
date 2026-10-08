<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $totalCustomers = \App\Models\Customer::count();
    $totalServices = \App\Models\Service::count();
    $totalBills = \App\Models\Invoice::count();
    $recentBills = \App\Models\Invoice::with('customer')->latest()->take(5)->get();
    return view('dashboard', compact('totalCustomers', 'totalServices', 'totalBills', 'recentBills'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/customer', [\App\Http\Controllers\CustomerController::class, 'index'])->name('customer');
    Route::get('/customer/ledger', [\App\Http\Controllers\CustomerController::class, 'ledger'])->name('customer.ledger');
    Route::post('/customer', [\App\Http\Controllers\CustomerController::class, 'store'])->name('customer.store');
    Route::put('/customer/{customer}', [\App\Http\Controllers\CustomerController::class, 'update'])->name('customer.update');
    Route::delete('/customer/{customer}', [\App\Http\Controllers\CustomerController::class, 'destroy'])->name('customer.destroy');
    Route::get('/service', [\App\Http\Controllers\ServiceController::class, 'index'])->name('service');
    Route::post('/service', [\App\Http\Controllers\ServiceController::class, 'store'])->name('service.store');
    Route::put('/service/{service}', [\App\Http\Controllers\ServiceController::class, 'update'])->name('service.update');
    Route::delete('/service/{service}', [\App\Http\Controllers\ServiceController::class, 'destroy'])->name('service.destroy');

    Route::get('/receipt', [\App\Http\Controllers\ReceiptController::class, 'index'])->name('receipt');
    Route::get('/receipt/report', [\App\Http\Controllers\ReceiptController::class, 'report'])->name('receipt.report');
    Route::post('/receipt', [\App\Http\Controllers\ReceiptController::class, 'store'])->name('receipt.store');
    Route::get('/receipt/{receipt}', [\App\Http\Controllers\ReceiptController::class, 'show'])->name('receipt.show');
    Route::put('/receipt/{receipt}', [\App\Http\Controllers\ReceiptController::class, 'update'])->name('receipt.update');
    Route::delete('/receipt/{receipt}', [\App\Http\Controllers\ReceiptController::class, 'destroy'])->name('receipt.destroy');
    Route::get('/create-bill', function (Illuminate\Http\Request $request) {
        $customers = \App\Models\Customer::all();
        $services = \App\Models\Service::all();
        
        $query = \App\Models\Invoice::with(['customer', 'services', 'service']);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('services', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('service', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($request->filled('from_date')) {
            $query->whereDate('service_date', '>=', $request->from_date);
        }
        
        if ($request->filled('to_date')) {
            $query->whereDate('service_date', '<=', $request->to_date);
        }
        
        $perPage = $request->input('per_page', 10);
        $invoices = $query->latest()->paginate($perPage)->appends($request->query());
        
        return view('create-bill', compact('customers', 'services', 'invoices'));
    })->name('create-bill');

    Route::get('/add-bill', function () {
        $customers = \App\Models\Customer::all();
        $services = \App\Models\Service::all();
        return view('add-bill', compact('customers', 'services'));
    })->name('add-bill');
    
    Route::post('/generate-bill', function (Illuminate\Http\Request $request) {
        $serviceIds = $request->input('service_ids');
        if (empty($serviceIds) && $request->filled('service_id')) {
            $serviceIds = (array) $request->service_id;
        }
        $request->merge(['service_ids' => $serviceIds]);

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'service_ids' => 'required|array|min:1',
            'service_ids.*' => 'exists:services,id',
            'service_date' => 'required|date',
            'due_date' => 'nullable|date',
            'amount' => 'required|numeric'
        ]);

        $invoice = \App\Models\Invoice::create([
            'customer_id' => $request->customer_id,
            'service_id' => $serviceIds[0] ?? null,
            'service_date' => $request->service_date,
            'due_date' => $request->due_date,
            'amount' => $request->amount,
            'category' => $request->category,
            'type' => $request->type
        ]);

        $serviceRecords = [];
        foreach ($serviceIds as $index => $svcId) {
            $serviceRecords[$svcId] = [
                'amount' => $request->total_amount[$index] ?? 0,
                'monthly_charges' => $request->monthly_charges[$index] ?? 0,
                'arrears' => $request->arrears[$index] ?? 0,
                'late_surcharge' => $request->late_payment_surcharge[$index] ?? 0,
                'total_bills' => $request->total_amount[$index] ?? 0,
                'total_payable' => $request->total_payable[$index] ?? 0,
            ];
        }
        $invoice->services()->sync($serviceRecords);
        
        return redirect()->route('invoice.show', $invoice->id)->with('success', 'Bill generated successfully!');
    })->name('generate-bill');

    Route::get('/invoice/{id}', function ($id) {
        $invoice = \App\Models\Invoice::with(['customer', 'services', 'service'])->findOrFail($id);
        return view('invoice', [
            'invoice' => $invoice,
            'customer' => $invoice->customer,
            'service' => $invoice->service,
            'services' => $invoice->services,
            'service_date' => $invoice->service_date,
            'due_date' => $invoice->due_date,
            'amount' => $invoice->amount
        ]);
    })->name('invoice.show');

    Route::get('/invoice/{id}/edit', function ($id) {
        $invoice = \App\Models\Invoice::with('services')->findOrFail($id);
        $customers = \App\Models\Customer::all();
        $services = \App\Models\Service::all();
        return view('edit-bill', compact('invoice', 'customers', 'services'));
    })->name('invoice.edit');

    Route::put('/invoice/{id}', function (Illuminate\Http\Request $request, $id) {
        $serviceIds = $request->input('service_ids');
        if (empty($serviceIds) && $request->filled('service_id')) {
            $serviceIds = (array) $request->service_id;
        }
        $request->merge(['service_ids' => $serviceIds]);

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'service_ids' => 'required|array|min:1',
            'service_ids.*' => 'exists:services,id',
            'service_date' => 'required|date',
            'due_date' => 'nullable|date',
            'amount' => 'required|numeric'
        ]);

        $invoice = \App\Models\Invoice::findOrFail($id);
        $invoice->update([
            'customer_id' => $request->customer_id,
            'service_id' => $serviceIds[0] ?? null,
            'service_date' => $request->service_date,
            'due_date' => $request->due_date,
            'amount' => $request->amount,
            'category' => $request->category,
            'type' => $request->type
        ]);

        $serviceRecords = [];
        $selectedServices = \App\Models\Service::whereIn('id', $serviceIds)->get();
        foreach ($selectedServices as $svc) {
            $serviceRecords[$svc->id] = ['amount' => $svc->amount];
        }
        $invoice->services()->sync($serviceRecords);

        return redirect()->route('create-bill')->with('success', 'Invoice updated successfully!');
    })->name('invoice.update');

    Route::delete('/invoice/{id}', function ($id) {
        \App\Models\Invoice::findOrFail($id)->delete();
        return back()->with('success', 'Invoice deleted successfully!');
    })->name('invoice.destroy');
});

require __DIR__.'/auth.php';

Route::post('/api/whatsapp/send/{id}', function (\Illuminate\Http\Request $request, $id) {
    $invoice = \App\Models\Invoice::with('customer', 'services')->findOrFail($id);
    
    if (!$invoice->customer || !$invoice->customer->contact_number) {
        return response()->json(['success' => false, 'error' => 'Customer phone number is missing']);
    }

    $phone = $invoice->customer->contact_number;
    
    $servicesList = $invoice->services->pluck('name')->implode(', ');
    if (empty($servicesList)) {
        $servicesList = $invoice->service ? $invoice->service->name : 'General Services';
    }

    $message = "*INVOICE #INV-" . str_pad($invoice->id, 4, '0', STR_PAD_LEFT) . "*\n\n";
    $message .= "Dear {$invoice->customer->name},\n";
    $message .= "Here are your invoice details:\n";
    $message .= "- *Services:* {$servicesList}\n";
    $message .= "- *Issue Date:* {$invoice->service_date}\n";
    $message .= "- *Due Date:* {$invoice->due_date}\n";
    $message .= "- *Total Amount:* Rs. {$invoice->amount}\n\n";
    $message .= "Thank you for your business!";

    try {
        $response = \Illuminate\Support\Facades\Http::post('http://localhost:3000/send-message', [
            'phone' => $phone,
            'message' => $message
        ]);
        
        return $response->json();
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => 'Could not connect to WhatsApp Server']);
    }
});
