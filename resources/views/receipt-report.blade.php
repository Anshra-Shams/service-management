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

                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('receipt.report') }}" class="flex flex-wrap items-start gap-4 mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
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
                                    Search / Filter
                                </button>
                                @if(request('customer_id'))
                                    <a href="{{ route('receipt.report') }}" style="height: 40px; line-height: 1;" class="px-5 flex items-center justify-center border border-gray-300 bg-white text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors shadow-sm">
                                        Clear
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>

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
