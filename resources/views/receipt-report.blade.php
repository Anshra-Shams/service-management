<x-app-layout>
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container .select2-selection--single {
            height: 40px !important;
            border: 1px solid #d1d5db !important; /* tailwind gray-300 */
            border-radius: 0.5rem !important;
            display: flex;
            align-items: center;
            background-color: #ffffff !important;
            padding-left: 0.5rem;
            font-size: 0.875rem; /* text-sm */
            color: #111827; /* gray-900 */
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px !important;
            right: 10px !important;
        }
        .select2-container--default .select2-selection--single:focus,
        .select2-container--default.select2-container--open .select2-selection--single {
            outline: none;
            border-color: #3b82f6 !important; /* blue-500 */
            box-shadow: 0 0 0 1px #3b82f6 !important;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #3b82f6 !important; /* blue-500 */
        }
        .select2-dropdown {
            border-color: #e5e7eb !important;
            border-radius: 0.5rem !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .select2-search__field {
            border-radius: 0.375rem !important;
            border: 1px solid #d1d5db !important;
            padding: 4px 8px !important;
        }
        .select2-search__field:focus {
            outline: none;
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 1px #3b82f6 !important;
        }
    </style>
    <div class="py-2 bg-gray-50">
        <div class="w-full px-2 mx-auto">
            
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                <div class="p-4 text-gray-900">
                    <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Receipt Report</h3>
                            <p class="text-sm text-gray-500 mt-1">Consolidated Payment & Outstanding Report</p>
                        </div>

                        <!-- Filter Form -->
                        <form method="GET" action="{{ route('receipt.report') }}" class="flex flex-wrap items-start gap-4">
                            <div style="min-width: 250px; width: 300px; max-width: 100%;">
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Select Customer</label>
                                <select name="customer_id" class="w-full select2 px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option></option> <!-- Required for Select2 placeholder -->
                                    @if(isset($customers))
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }} - (Balance: Rs. {{ number_format($customer->closing_balance ?? 0, 2) }})
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div>
                                <!-- Invisible label to force perfect baseline alignment with the select box -->
                                <label class="block text-xs font-semibold text-transparent mb-1 pointer-events-none select-none">&nbsp;</label>
                                <div class="flex gap-2">
                                    <button type="submit" style="height: 40px; line-height: 1;" class="px-5 flex items-center justify-center bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors shadow-sm">
                                        Search
                                    </button>
                                    @if(request('customer_id'))
                                        <a href="{{ route('receipt.report') }}" style="height: 40px; line-height: 1;" class="px-5 flex items-center justify-center border border-gray-300 bg-white text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors shadow-sm">
                                            Clear
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Report Table -->
                    <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm" style="max-height: 70vh;">
                        <table class="w-full text-center border-collapse whitespace-nowrap text-sm">
                            <thead>
                                <tr class="text-xs font-semibold text-gray-700 uppercase tracking-wider bg-gray-100">
                                    <th class="border border-gray-300 px-4 py-3 min-w-[120px]">Customer Name</th>
                                    <th class="border border-gray-300 px-4 py-3 min-w-[120px]">Phone</th>
                                    <th class="border border-gray-300 px-4 py-3 min-w-[120px]">Bungalow Date</th>
                                    <th class="border border-gray-300 px-4 py-3 min-w-[120px]">Water Date</th>
                                    <th class="border border-gray-300 px-4 py-3 min-w-[120px]">Sector</th>
                                    <th class="border border-gray-300 px-4 py-3 min-w-[120px]">Street</th>
                                    <th class="border border-gray-300 px-4 py-3 min-w-[120px]">Plot</th>
                                    <th class="border border-gray-300 px-4 py-3 min-w-[120px]">Type</th>
                                    <th class="border border-gray-300 px-4 py-3 min-w-[120px]">Size of Plot</th>
                                    <th class="border border-gray-300 px-4 py-3 min-w-[120px]">Total Months Of</th>
                                    <th class="border border-gray-300 px-4 py-3 min-w-[120px]">Challan Date</th>
                                    <th class="border border-gray-300 px-4 py-3 min-w-[120px]" style="background-color: #60a5fa; color: white;">Water Arreares</th>
                                    <th class="border border-gray-300 px-4 py-3 min-w-[120px]">IF Paid Water Arreares</th>
                                    <th class="border border-gray-300 px-4 py-3 min-w-[120px]">Payment Date</th>
                                    <th class="border border-gray-300 px-4 py-3 min-w-[120px]" style="background-color: #22c55e; color: white;">Total Received</th>
                                    <th class="border border-gray-300 px-4 py-3 min-w-[120px]">Payable of House</th>
                                    <th class="border border-gray-300 px-4 py-3 min-w-[120px]">Payable water</th>
                                    <th class="border border-gray-300 px-4 py-3 min-w-[120px]" style="background-color: #facc15; color: #1f2937;">Total Payable</th>
                                    <th class="border border-gray-300 px-4 py-3 min-w-[120px]" style="background-color: #ef4444; color: white;">Outstanding</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                @if(isset($reportCustomers) && count($reportCustomers) > 0)
                                    @foreach($reportCustomers as $customer)
                                        @php
                                            $totalReceived = $customer->receipts->sum('amount');
                                            $latestReceipt = $customer->receipts->sortByDesc('receipt_date')->first();
                                            $paymentDate = $latestReceipt ? \Carbon\Carbon::parse($latestReceipt->receipt_date)->format('d-M-Y') : '-';
                                            
                                            $totalWaterArrears = 0;
                                            $totalPayableWater = 0;
                                            $bungalowDate = '-';
                                            $waterDate = '-';
                                            $type = '-';
                                            $sizeOfPlot = '-';
                                            
                                            $sortedInvoices = $customer->invoices->sortByDesc('service_date');
                                            if ($sortedInvoices->count() > 0) {
                                                $latestInvoice = $sortedInvoices->first();
                                                // Temporarily keeping these blank as per user request
                                                $type = '-'; 
                                                $sizeOfPlot = '-'; 
                                            }

                                            foreach ($sortedInvoices as $inv) {
                                                $wService = $inv->services->firstWhere(function($s) { return stripos($s->name, 'water') !== false; });
                                                if ($wService) {
                                                    $totalWaterArrears += $wService->pivot->arrears;
                                                    $totalPayableWater += $wService->pivot->total_payable;
                                                    if ($waterDate === '-') {
                                                        $waterDate = \Carbon\Carbon::parse($inv->service_date)->format('d-M-Y');
                                                    }
                                                } else {
                                                    // Bungalow Date temporarily left blank as per user request
                                                }
                                            }
                                            
                                            $totalPayable = $customer->invoices->sum('amount');
                                            $payableHouse = $totalPayable; // Set to be same as Total Payable per user request
                                            $outstanding = $customer->closing_balance;
                                            
                                            $phone = $customer->contact_number ?: '-';
                                            $rawAddress = trim($customer->address ?? '');
                                            $plot = '-'; $sector = '-'; $street = '-';
                                            if (preg_match('/(plot|house|h\.no|p\.no)\s*[:#\-]?\s*([a-zA-Z0-9\-\/]+)/i', $rawAddress, $m)) $plot = $m[2];
                                            if (preg_match('/sector\s*[:#\-]?\s*([a-zA-Z0-9]+)/i', $rawAddress, $m)) $sector = $m[1];
                                            if (preg_match('/street\s*[:#\-]?\s*([a-zA-Z0-9]+)/i', $rawAddress, $m)) $street = $m[1];
                                        @endphp
                                        <tr class="hover:bg-gray-50">
                                            <td class="border border-gray-300 px-4 py-2 font-semibold text-left">{{ $customer->name }}</td>
                                            <td class="border border-gray-300 px-4 py-2">{{ $phone }}</td>
                                            <td class="border border-gray-300 px-4 py-2">{{ $bungalowDate }}</td>
                                            <td class="border border-gray-300 px-4 py-2">{{ $waterDate }}</td>
                                            <td class="border border-gray-300 px-4 py-2">{{ $sector }}</td>
                                            <td class="border border-gray-300 px-4 py-2">{{ $street }}</td>
                                            <td class="border border-gray-300 px-4 py-2">{{ $plot }}</td>
                                            <td class="border border-gray-300 px-4 py-2">{{ $type }}</td>
                                            <td class="border border-gray-300 px-4 py-2">{{ $sizeOfPlot }}</td>
                                            <td class="border border-gray-300 px-4 py-2">-</td>
                                            
                                            <td class="border border-gray-300 px-4 py-2">-</td>
                                            <td class="border border-gray-300 px-4 py-2">{{ $totalWaterArrears > 0 ? number_format($totalWaterArrears, 2) : '-' }}</td>
                                            <td class="border border-gray-300 px-4 py-2">-</td>
                                            <td class="border border-gray-300 px-4 py-2">{{ $paymentDate }}</td>
                                            <td class="border border-gray-300 px-4 py-2 font-semibold bg-green-50 text-green-700">
                                                {{ number_format($totalReceived, 2) }}
                                            </td>
                                            <td class="border border-gray-300 px-4 py-2">{{ $payableHouse > 0 ? number_format($payableHouse, 2) : '-' }}</td>
                                            <td class="border border-gray-300 px-4 py-2">{{ $totalPayableWater > 0 ? number_format($totalPayableWater, 2) : '-' }}</td>
                                            <td class="border border-gray-300 px-4 py-2 font-bold bg-yellow-50 text-yellow-800">{{ number_format($totalPayable, 2) }}</td>
                                            <td class="border border-gray-300 px-4 py-2 font-bold bg-red-50 text-red-700">{{ number_format($outstanding, 2) }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="19" class="border border-gray-300 px-4 py-8 text-center text-gray-500">
                                            No data found.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                    @if(isset($reportCustomers))
                        <div class="mt-4">
                            <x-pagination :items="$reportCustomers" />
                        </div>
                    @endif
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>

<!-- jQuery and Select2 JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Select a customer...",
            allowClear: true,
            width: '100%'
        });
    });
</script>
