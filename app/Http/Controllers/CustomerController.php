<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Service;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::with('services')->latest();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%")
                  ->orWhere('plot_size', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('plot', 'like', "%{$search}%")
                  ->orWhere('street', 'like', "%{$search}%")
                  ->orWhere('sector', 'like', "%{$search}%")
                  ->orWhere('phase', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
        }
        
        $perPage = $request->input('per_page', 10);
        $customers = $query->paginate($perPage)->appends($request->query());
        $services = Service::where('is_category', true)->get();

        $suggestedPlotSizes = collect();
        foreach ($services as $svc) {
            if (!empty($svc->marla)) $suggestedPlotSizes->push($svc->marla);
            if (!empty($svc->details) && is_array($svc->details)) {
                foreach ($svc->details as $d) {
                    if (!empty($d['marla'])) $suggestedPlotSizes->push($d['marla']);
                }
            }
        }
        $suggestedPlotSizes = $suggestedPlotSizes->merge(Customer::whereNotNull('plot_size')->pluck('plot_size'))
            ->unique()
            ->filter()
            ->values();

        return view('customer', compact('customers', 'services', 'suggestedPlotSizes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:255',
            'plot_size' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'plot' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'sector' => 'nullable|string|max:255',
            'phase' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'opening_balance' => 'nullable|numeric',
            'start_date' => 'nullable|date',
            'services' => 'nullable|array',
        ]);

        $address = $request->address;
        if (empty($address)) {
            $parts = [];
            if ($request->filled('plot')) $parts[] = 'Plot ' . $request->plot;
            if ($request->filled('street')) $parts[] = 'Street ' . $request->street;
            if ($request->filled('sector')) $parts[] = 'Sector ' . $request->sector;
            if ($request->filled('phase')) $parts[] = 'Phase ' . $request->phase;
            $address = implode(' ', $parts);
        }

        $customer = Customer::create([
            'name' => $request->name,
            'contact_number' => $request->contact_number,
            'plot_size' => $request->plot_size,
            'type' => $request->type,
            'plot' => $request->plot,
            'street' => $request->street,
            'sector' => $request->sector,
            'phase' => $request->phase,
            'address' => $address,
            'opening_balance' => $request->opening_balance ?? 0.00,
            'start_date' => $request->start_date,
        ]);

        if ($request->has('services') && is_array($request->services)) {
            $syncData = [];
            foreach ($request->services as $serviceId => $item) {
                if (!empty($item['selected'])) {
                    $syncData[$serviceId] = [
                        'amount' => isset($item['amount']) && $item['amount'] !== '' ? (float)$item['amount'] : 0.00,
                        'marla' => $item['marla'] ?? $request->plot_size,
                    ];
                }
            }
            $customer->services()->sync($syncData);
        }

        return redirect()->back()->with('success', 'Customer added successfully!');
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:255',
            'plot_size' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'plot' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'sector' => 'nullable|string|max:255',
            'phase' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'opening_balance' => 'nullable|numeric',
            'start_date' => 'nullable|date',
            'services' => 'nullable|array',
        ]);

        $address = $request->address;
        if (empty($address)) {
            $parts = [];
            if ($request->filled('plot')) $parts[] = 'Plot ' . $request->plot;
            if ($request->filled('street')) $parts[] = 'Street ' . $request->street;
            if ($request->filled('sector')) $parts[] = 'Sector ' . $request->sector;
            if ($request->filled('phase')) $parts[] = 'Phase ' . $request->phase;
            $address = implode(' ', $parts);
        }

        $customer->update([
            'name' => $request->name,
            'contact_number' => $request->contact_number,
            'plot_size' => $request->plot_size,
            'type' => $request->type,
            'plot' => $request->plot,
            'street' => $request->street,
            'sector' => $request->sector,
            'phase' => $request->phase,
            'address' => $address,
            'opening_balance' => $request->opening_balance ?? 0.00,
            'start_date' => $request->start_date,
        ]);

        if ($request->has('services') && is_array($request->services)) {
            $syncData = [];
            foreach ($request->services as $serviceId => $item) {
                if (!empty($item['selected'])) {
                    $syncData[$serviceId] = [
                        'amount' => isset($item['amount']) && $item['amount'] !== '' ? (float)$item['amount'] : 0.00,
                        'marla' => $item['marla'] ?? $request->plot_size,
                    ];
                }
            }
            $customer->services()->sync($syncData);
        }

        return redirect()->back()->with('success', 'Customer updated successfully!');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->back()->with('success', 'Customer deleted successfully!');
    }

    public function ledger(Request $request)
    {
        $customers = Customer::orderBy('name')->get();
        
        $selectedCustomer = null;
        if ($request->filled('customer_id')) {
            $selectedCustomer = Customer::with(['invoices', 'receipts'])->find($request->customer_id);
        }

        $transactions = [];
        $totalBilled = 0;
        $totalReceived = 0;
        $finalOutstanding = 0;

        if ($selectedCustomer) {
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');

            $invoicesQuery = $selectedCustomer->invoices();
            $receiptsQuery = $selectedCustomer->receipts();

            // Period Invoices
            $periodInvoicesQuery = clone $invoicesQuery;
            if ($fromDate) {
                $periodInvoicesQuery->whereDate('service_date', '>=', $fromDate);
            }
            if ($toDate) {
                $periodInvoicesQuery->whereDate('service_date', '<=', $toDate);
            }
            $invoices = $periodInvoicesQuery->orderBy('service_date')->orderBy('id')->get();

            // Period Receipts
            $periodReceiptsQuery = clone $receiptsQuery;
            if ($fromDate) {
                $periodReceiptsQuery->whereDate('receipt_date', '>=', $fromDate);
            }
            if ($toDate) {
                $periodReceiptsQuery->whereDate('receipt_date', '<=', $toDate);
            }
            $receipts = $periodReceiptsQuery->orderBy('receipt_date')->orderBy('id')->get();

            $unassignedReceipts = [];
            $invoiceReceipts = [];

            foreach ($receipts as $rec) {
                if ($rec->invoice_id) {
                    $invoiceReceipts[$rec->invoice_id][] = $rec;
                } else {
                    $unassignedReceipts[] = $rec;
                }
            }

            $rows = [];

            // Opening Balance row if applicable
            if ($fromDate) {
                $initOpening = (float) $selectedCustomer->opening_balance;
                $initStartDate = $selectedCustomer->start_date ? \Carbon\Carbon::parse($selectedCustomer->start_date)->startOfDay() : null;
                $parsedFrom = \Carbon\Carbon::parse($fromDate)->startOfDay();

                $opBal = 0;
                if (!$initStartDate || $initStartDate->lt($parsedFrom)) {
                    $opBal += $initOpening;
                }
                $invoicesBefore = (clone $invoicesQuery)->whereDate('service_date', '<', $fromDate)->sum('amount');
                $receiptsBefore = (clone $receiptsQuery)->whereDate('receipt_date', '<', $fromDate)->sum('amount');
                $startingBalance = $opBal + (float)$invoicesBefore - (float)$receiptsBefore;

                if ($startingBalance != 0) {
                    $rows[] = [
                        'customer_name' => $selectedCustomer->name,
                        'description' => 'Balance b/f (As of ' . $parsedFrom->format('d M Y') . ')',
                        'bill_date' => $parsedFrom->format('d M Y'),
                        'bill_amount' => $startingBalance > 0 ? $startingBalance : null,
                        'received_date' => $startingBalance < 0 ? $parsedFrom->format('d M Y') : '-',
                        'received_amount' => $startingBalance < 0 ? abs($startingBalance) : null,
                        'outstanding' => $startingBalance,
                        'url' => null,
                    ];
                }
            } elseif ((float)$selectedCustomer->opening_balance > 0) {
                $opDate = $selectedCustomer->start_date ? \Carbon\Carbon::parse($selectedCustomer->start_date)->format('d M Y') : '-';
                $rows[] = [
                    'customer_name' => $selectedCustomer->name,
                    'description' => 'Opening Balance',
                    'bill_date' => $opDate,
                    'bill_amount' => (float)$selectedCustomer->opening_balance,
                    'received_date' => '-',
                    'received_amount' => null,
                    'outstanding' => (float)$selectedCustomer->opening_balance,
                    'balance' => (float)$selectedCustomer->opening_balance,
                    'url' => null,
                ];
            }

            // Pair invoices with their receipts on the same line
            $unassignedIndex = 0;
            foreach ($invoices as $inv) {
                $invDate = \Carbon\Carbon::parse($inv->service_date ?? $inv->created_at);
                $desc = 'Invoice #INV-' . str_pad($inv->id, 4, '0', STR_PAD_LEFT);
                if (!empty($inv->category)) {
                    $desc .= ' (' . $inv->category . ')';
                }

                $matchedRecs = [];
                if (!empty($invoiceReceipts[$inv->id])) {
                    $matchedRecs = $invoiceReceipts[$inv->id];
                } elseif ($unassignedIndex < count($unassignedReceipts)) {
                    $matchedRecs = [$unassignedReceipts[$unassignedIndex]];
                    $unassignedIndex++;
                }

                $recAmount = null;
                $recDateStr = '-';
                if (!empty($matchedRecs)) {
                    $recAmount = 0;
                    $dates = [];
                    foreach ($matchedRecs as $mr) {
                        $recAmount += (float)$mr->amount;
                        $dates[] = \Carbon\Carbon::parse($mr->receipt_date)->format('d M Y');
                    }
                    $recDateStr = implode(', ', array_unique($dates));
                }

                $bAmt = (float)$inv->amount;
                $outAmt = $bAmt - ($recAmount ?? 0);

                $rows[] = [
                    'customer_name' => $selectedCustomer->name,
                    'description' => $desc,
                    'bill_date' => $invDate->format('d M Y'),
                    'bill_amount' => $bAmt,
                    'received_date' => $recDateStr,
                    'received_amount' => $recAmount,
                    'outstanding' => $outAmt,
                    'balance' => $outAmt,
                    'url' => route('invoice.show', $inv->id),
                ];
            }

            // Any remaining unassigned receipts that exceed invoices
            while ($unassignedIndex < count($unassignedReceipts)) {
                $mr = $unassignedReceipts[$unassignedIndex];
                $recDate = \Carbon\Carbon::parse($mr->receipt_date);
                $recNum = 'RV-' . str_pad($mr->id, 4, '0', STR_PAD_LEFT);
                $desc = 'Receipt Voucher #' . $recNum;
                if ($mr->payment_method) {
                    $desc .= ' (' . ucfirst($mr->payment_method) . ')';
                }
                $rAmt = (float)$mr->amount;
                $rows[] = [
                    'customer_name' => $selectedCustomer->name,
                    'description' => $desc,
                    'bill_date' => '-',
                    'bill_amount' => null,
                    'received_date' => $recDate->format('d M Y'),
                    'received_amount' => $rAmt,
                    'outstanding' => -$rAmt,
                    'balance' => -$rAmt,
                    'url' => route('receipt.show', $mr->id),
                ];
                $unassignedIndex++;
            }

            // Calculate totals
            foreach ($rows as $r) {
                $totalBilled += ($r['bill_amount'] ?? 0);
                $totalReceived += ($r['received_amount'] ?? 0);
            }
            $finalOutstanding = $totalBilled - $totalReceived;

            $transactions = $rows;
        }

        return view('customer-ledger', compact(
            'customers',
            'selectedCustomer',
            'transactions',
            'totalBilled',
            'totalReceived',
            'finalOutstanding'
        ));
    }
}
