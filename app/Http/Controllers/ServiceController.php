<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::where('is_category', true)->latest();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
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
            'details.*.service' => 'nullable|string|max:255',
            'details.*.marla' => 'nullable|string|max:255',
        ]);
        
        $cleanDetails = [];
        if (!empty($request->details) && is_array($request->details)) {
            foreach ($request->details as $d) {
                $serviceName = trim($d['service'] ?? $d['marla'] ?? '');
                $amount = $d['amount'] ?? null;
                if ($serviceName !== '' || ($amount !== null && $amount !== '')) {
                    $cleanDetails[] = [
                        'service' => $serviceName,
                        'marla' => $serviceName,
                        'amount' => $amount,
                    ];
                }
            }
        }

        $serviceData = [
            'name' => $request->name,
            'details' => $cleanDetails,
            'amount' => $cleanDetails[0]['amount'] ?? null,
            'marla' => $cleanDetails[0]['service'] ?? null,
            'is_category' => true,
        ];

        Service::create($serviceData);
        return redirect()->back()->with('success', 'Category added successfully.');
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'details' => 'nullable|array',
            'details.*.amount' => 'nullable|numeric',
            'details.*.service' => 'nullable|string|max:255',
            'details.*.marla' => 'nullable|string|max:255',
        ]);
        
        $cleanDetails = [];
        if (!empty($request->details) && is_array($request->details)) {
            foreach ($request->details as $d) {
                $serviceName = trim($d['service'] ?? $d['marla'] ?? '');
                $amount = $d['amount'] ?? null;
                if ($serviceName !== '' || ($amount !== null && $amount !== '')) {
                    $cleanDetails[] = [
                        'service' => $serviceName,
                        'marla' => $serviceName,
                        'amount' => $amount,
                    ];
                }
            }
        }

        $serviceData = [
            'name' => $request->name,
            'details' => $cleanDetails,
            'amount' => $cleanDetails[0]['amount'] ?? null,
            'marla' => $cleanDetails[0]['service'] ?? null,
            'is_category' => true,
        ];

        $service->update($serviceData);
        return redirect()->back()->with('success', 'Category updated successfully.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->back()->with('success', 'Service deleted successfully.');
    }
}
