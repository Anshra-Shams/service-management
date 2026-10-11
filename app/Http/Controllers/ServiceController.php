<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::latest();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
        }

        $perPage = $request->input('per_page', 10);
        $services = $query->paginate($perPage)->appends($request->query());
        return view('service', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'details' => 'nullable|array',
            'details.*.amount' => 'nullable|numeric',
            'details.*.marla' => 'nullable|string|max:255',
        ]);
        
        $serviceData = [
            'name' => $request->name,
            'details' => $request->details ?? [],
        ];
        
        if (!empty($request->details) && count($request->details) > 0) {
            $serviceData['amount'] = $request->details[0]['amount'] ?? null;
            $serviceData['marla'] = $request->details[0]['marla'] ?? null;
        }

        Service::create($serviceData);
        return redirect()->back()->with('success', 'Category added successfully.');
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'details' => 'nullable|array',
            'details.*.amount' => 'nullable|numeric',
            'details.*.marla' => 'nullable|string|max:255',
        ]);
        
        $serviceData = [
            'name' => $request->name,
            'details' => $request->details ?? [],
        ];
        
        if (!empty($request->details) && count($request->details) > 0) {
            $serviceData['amount'] = $request->details[0]['amount'] ?? null;
            $serviceData['marla'] = $request->details[0]['marla'] ?? null;
        } else {
            $serviceData['amount'] = null;
            $serviceData['marla'] = null;
        }

        $service->update($serviceData);
        return redirect()->back()->with('success', 'Category updated successfully.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->back()->with('success', 'Service deleted successfully.');
    }
}
