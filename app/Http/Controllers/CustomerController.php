<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::latest();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%");
        }
        
        $perPage = $request->input('per_page', 10);
        $customers = $query->paginate($perPage)->appends($request->query());
        return view('customer', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'opening_balance' => 'nullable|numeric',
            'start_date' => 'nullable|date',
        ]);

        Customer::create([
            'name' => $request->name,
            'contact_number' => $request->contact_number,
            'address' => $request->address,
            'opening_balance' => $request->opening_balance ?? 0.00,
            'start_date' => $request->start_date,
        ]);

        return redirect()->back()->with('success', 'Customer added successfully!');
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'opening_balance' => 'nullable|numeric',
            'start_date' => 'nullable|date',
        ]);

        $customer->update([
            'name' => $request->name,
            'contact_number' => $request->contact_number,
            'address' => $request->address,
            'opening_balance' => $request->opening_balance ?? 0.00,
            'start_date' => $request->start_date,
        ]);

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
        $openingBalance = 0;
        $asOfDate = '01/01/2000';
        $closingBalance = 0;

        if ($selectedCustomer) {
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');

            $initOpening = (float) $selectedCustomer->opening_balance;
            $initStartDate = $selectedCustomer->start_date ? \Carbon\Carbon::parse($selectedCustomer->start_date)->startOfDay() : null;

            $invoicesQuery = $selectedCustomer->invoices();
            $receiptsQuery = $selectedCustomer->receipts();

            if ($fromDate) {
                $parsedFrom = \Carbon\Carbon::parse($fromDate)->startOfDay();
                $asOfDate = $parsedFrom->format('d M Y');

                $opBal = 0;
                // If initial opening balance is from before from_date
                if (!$initStartDate || $initStartDate->lt($parsedFrom)) {
                    $opBal += $initOpening;
                }

                $invoicesBefore = (clone $invoicesQuery)->whereDate('service_date', '<', $fromDate)->sum('amount');
                $receiptsBefore = (clone $receiptsQuery)->whereDate('receipt_date', '<', $fromDate)->sum('amount');

                $openingBalance = $opBal + (float)$invoicesBefore - (float)$receiptsBefore;
            } else {
                $openingBalance = 0;
                $asOfDate = $initStartDate ? $initStartDate->format('d M Y') : '01 Jan 2000';
            }

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

            $txItems = [];

            foreach ($receipts as $rec) {
                $recDate = \Carbon\Carbon::parse($rec->receipt_date);
                $recNum = 'RV-' . str_pad($rec->id, 4, '0', STR_PAD_LEFT);
                $desc = 'Receipt Voucher #' . $recNum;
                if ($rec->payment_method) {
                    $desc .= ' (' . ucfirst($rec->payment_method) . ')';
                }
                if ($rec->reference_no) {
                    $desc .= ' - Ref: ' . $rec->reference_no;
                }
                if ($rec->remarks) {
                    $desc .= ' - ' . $rec->remarks;
                }
                $txItems[] = [
                    'date' => $recDate->format('Y-m-d'),
                    'formatted_date' => $recDate->format('d M Y'),
                    'type' => 'receipt',
                    'description' => $desc,
                    'debit' => null,
                    'credit' => (float) $rec->amount,
                    'url' => route('receipt.show', $rec->id),
                    'sort_order' => 1,
                    'id' => $rec->id,
                    'balance' => (float) $selectedCustomer->closing_balance,
                ];
            }

            // Sort by date, then id
            usort($txItems, function ($a, $b) {
                if ($a['date'] === $b['date']) {
                    return $a['id'] <=> $b['id'];
                }
                return strcmp($a['date'], $b['date']);
            });

            $transactions = $txItems;
            $closingBalance = (float) $selectedCustomer->closing_balance;
            $openingBalance = (float) $selectedCustomer->opening_balance;
        }

        return view('customer-ledger', compact(
            'customers',
            'selectedCustomer',
            'transactions',
            'openingBalance',
            'asOfDate',
            'closingBalance'
        ));
    }
}
