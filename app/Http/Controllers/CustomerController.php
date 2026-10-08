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
        ]);

        Customer::create([
            'name' => $request->name,
            'contact_number' => $request->contact_number,
            'address' => $request->address,
            'opening_balance' => $request->opening_balance ?? 0.00,
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
        ]);

        $customer->update([
            'name' => $request->name,
            'contact_number' => $request->contact_number,
            'address' => $request->address,
            'opening_balance' => $request->opening_balance ?? 0.00,
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
        $query = Customer::latest();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%");
        }
        
        $perPage = $request->input('per_page', 10);
        $customers = $query->paginate($perPage)->appends($request->query());
        return view('customer-ledger', compact('customers'));
    }
}
