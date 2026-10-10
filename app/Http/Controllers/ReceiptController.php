<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Receipt;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    public function index(Request $request)
    {
        $query = Receipt::with(['customer', 'invoice']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('reference_no', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('receipt_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('receipt_date', '<=', $request->to_date);
        }

        $totalReceived = (clone $query)->sum('amount');
        $perPage = $request->input('per_page', 10);
        $receipts = $query->latest()->paginate($perPage)->appends($request->query());
        $customers = Customer::orderBy('name')->get();
        $invoices = Invoice::select('id', 'customer_id', 'amount', 'service_date')->latest()->get();

        return view('receipt', compact('receipts', 'customers', 'invoices', 'totalReceived'));
    }

    public function report(Request $request)
    {
        $query = Customer::with(['receipts', 'invoices.services'])->orderBy('name');
        
        if ($request->filled('customer_id')) {
            $query->where('id', $request->customer_id);
        } else {
            // Only show customers that have at least one receipt
            $query->whereHas('receipts');
        }
        
        $perPage = $request->input('per_page', 10);
        $reportCustomers = $query->paginate($perPage)->appends($request->query());
        $customers = Customer::orderBy('name')->get();
        
        return view('receipt-report', compact('reportCustomers', 'customers'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $receipt = Receipt::create($data);

        return redirect()->route('receipt.show', $receipt->id)->with('success', 'Receipt voucher created successfully.');
    }

    public function show(Receipt $receipt)
    {
        $receipt->load(['customer', 'invoice']);
        return view('receipt-voucher', compact('receipt'));
    }

    public function update(Request $request, Receipt $receipt)
    {
        $receipt->update($this->validateData($request));
        return redirect()->back()->with('success', 'Receipt voucher updated successfully.');
    }

    public function destroy(Receipt $receipt)
    {
        $receipt->delete();
        return redirect()->back()->with('success', 'Receipt voucher deleted successfully.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'customer_id'    => 'required|exists:customers,id',
            'invoice_id'     => 'nullable|exists:invoices,id',
            'receipt_date'   => 'required|date',
            'amount'         => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank,cheque,online',
            'reference_no'   => 'nullable|string|max:255',
            'remarks'        => 'nullable|string|max:1000',
        ]);
    }
}
