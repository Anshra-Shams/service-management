<x-app-layout>
    <div class="py-8 bg-gray-50">
        <div class="w-full px-4 md:px-8 mx-auto">
            
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                <div class="p-8 text-gray-900">
                    <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Receipt Report</h3>
                            <p class="text-sm text-gray-500 mt-1">Consolidated Payment & Outstanding Report</p>
                        </div>
                    </div>

                    <!-- Report Table -->
                    <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm" style="max-height: 70vh;">
                        <table class="w-full text-center border-collapse whitespace-nowrap text-sm">
                            <thead>
                                <tr class="text-xs font-semibold text-gray-700 uppercase tracking-wider bg-gray-100">
                                    <th class="border border-gray-300 px-4 py-3 min-w-[120px]">Payment_19...</th>
                                    <th class="border border-gray-300 px-4 py-3 min-w-[120px]">Challan Date</th>
                                    <th class="border border-gray-300 px-4 py-3 min-w-[120px]">Payment_200...</th>
                                    <th class="border border-gray-300 px-4 py-3 bg-blue-400 text-white min-w-[120px]">Water Arreares</th>
                                    <th class="border border-gray-300 px-4 py-3 min-w-[120px]">IF Paid Water Arreares</th>
                                    <th class="border border-gray-300 px-4 py-3 min-w-[120px]">Payment Date</th>
                                    <th class="border border-gray-300 px-4 py-3 bg-green-500 text-white min-w-[120px]">Total Received</th>
                                    <th class="border border-gray-300 px-4 py-3 min-w-[120px]">Payable of House</th>
                                    <th class="border border-gray-300 px-4 py-3 min-w-[120px]">Payable water</th>
                                    <th class="border border-gray-300 px-4 py-3 bg-yellow-400 text-gray-800 min-w-[120px]">Total Payable</th>
                                    <th class="border border-gray-300 px-4 py-3 bg-red-500 text-white min-w-[120px]">Outstanding</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                @if(isset($receipts) && count($receipts) > 0)
                                    @foreach($receipts as $receipt)
                                        @php
                                            $invoice = $receipt->invoice;
                                            $waterService = $invoice ? $invoice->services->firstWhere(function($s) { return stripos($s->name, 'water') !== false; }) : null;
                                            
                                            $waterArrears = $waterService ? $waterService->pivot->arrears : 0;
                                            $payableWater = $waterService ? $waterService->pivot->total_payable : 0;
                                            $totalPayable = $invoice ? $invoice->amount : 0;
                                            $payableHouse = $totalPayable; // Set to be same as Total Payable per user request
                                            $outstanding = $totalPayable - $receipt->amount;
                                        @endphp
                                        <tr class="hover:bg-gray-50">
                                            <td class="border border-gray-300 px-4 py-2"></td>
                                            <td class="border border-gray-300 px-4 py-2">{{ $invoice ? \Carbon\Carbon::parse($invoice->service_date)->format('d-M-Y') : '-' }}</td>
                                            <td class="border border-gray-300 px-4 py-2"></td>
                                            
                                            <!-- Water Arrears -->
                                            <td class="border border-gray-300 px-4 py-2">{{ $waterArrears > 0 ? number_format($waterArrears, 2) : '-' }}</td>
                                            
                                            <!-- IF Paid Water Arrears -->
                                            <td class="border border-gray-300 px-4 py-2">-</td>
                                            
                                            <!-- Payment Date -->
                                            <td class="border border-gray-300 px-4 py-2">{{ \Carbon\Carbon::parse($receipt->receipt_date)->format('d-M-Y') }}</td>
                                            
                                            <!-- Total Received -->
                                            <td class="border border-gray-300 px-4 py-2 font-semibold bg-green-50 text-green-700">
                                                {{ number_format($receipt->amount, 2) }}
                                            </td>
                                            
                                            <!-- Payable of House -->
                                            <td class="border border-gray-300 px-4 py-2">
                                                {{ $payableHouse > 0 ? number_format($payableHouse, 2) : '-' }}
                                            </td>
                                            
                                            <!-- Payable water -->
                                            <td class="border border-gray-300 px-4 py-2">
                                                {{ $payableWater > 0 ? number_format($payableWater, 2) : '-' }}
                                            </td>
                                            
                                            <!-- Total Payable -->
                                            <td class="border border-gray-300 px-4 py-2 font-bold bg-yellow-50 text-yellow-800">
                                                {{ number_format($totalPayable, 2) }}
                                            </td>
                                            
                                            <!-- Outstanding -->
                                            <td class="border border-gray-300 px-4 py-2 font-bold bg-red-50 text-red-700">
                                                {{ number_format($outstanding, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="11" class="border border-gray-300 px-4 py-8 text-center text-gray-500">
                                            No data found.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>
