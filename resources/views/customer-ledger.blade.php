<x-app-layout>
    <!-- Select2 CSS for searchable customer dropdown -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container .select2-selection--single {
            height: 42px !important;
            border: 1px solid #d1d5db !important;
            border-radius: 0.5rem !important;
            display: flex;
            align-items: center;
            background-color: #ffffff !important;
            padding-left: 0.75rem;
            font-size: 0.875rem;
            color: #111827;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
            right: 10px !important;
        }
        .select2-container--default .select2-selection--single:focus,
        .select2-container--default.select2-container--open .select2-selection--single {
            outline: none;
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2) !important;
        }
        .select2-dropdown {
            border-color: #e5e7eb !important;
            border-radius: 0.5rem !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        /* Filter Row Layout */
        .ledger-filter-row {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            gap: 16px;
        }
        .ledger-col-customer {
            flex: 2;
            min-width: 250px;
        }
        .ledger-col-date {
            flex: 1;
            min-width: 170px;
        }
        .ledger-col-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .ledger-input {
            height: 42px !important;
            width: 100% !important;
            border: 1px solid #d1d5db !important;
            border-radius: 0.5rem !important;
            padding: 0 0.75rem !important;
            font-size: 0.875rem !important;
            color: #111827 !important;
            background-color: #ffffff !important;
        }
        .ledger-btn-filter {
            height: 42px !important;
            padding: 0 32px !important;
            background-color: #2563eb !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            font-size: 0.875rem !important;
            border-radius: 0.5rem !important;
            border: none !important;
            cursor: pointer !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: background-color 0.15s ease-in-out;
            white-space: nowrap !important;
        }
        .ledger-btn-filter:hover {
            background-color: #1d4ed8 !important;
        }
        .ledger-btn-icon {
            height: 42px !important;
            width: 42px !important;
            border: 1px solid #d1d5db !important;
            background-color: #ffffff !important;
            color: #4b5563 !important;
            border-radius: 0.5rem !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: background-color 0.15s ease-in-out;
        }
        .ledger-btn-icon:hover {
            background-color: #f9fafb !important;
        }

        /* Table Header Explicit Colors */
        .ledger-table-head {
            background-color: #1e2530 !important;
        }
        .ledger-table-head th {
            background-color: #1e2530 !important;
            color: #ffffff !important;
            font-size: 0.8125rem !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            padding: 14px 18px !important;
            border: none !important;
            vertical-align: middle !important;
        }

        @media print {
            nav, .no-print, button, a[href]:not(.print-keep) {
                display: none !important;
            }
            body {
                background: white !important;
            }
            .print-container {
                padding: 0 !important;
                margin: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }
            .print-header {
                display: block !important;
            }
        }
    </style>

    <div class="py-6 bg-gray-50 min-h-screen">
        <div class="w-full px-4 md:px-8 mx-auto print-container">
            
            <!-- Print Header (only visible when printing) -->
            <div class="hidden print-header mb-6 pb-4 border-b border-gray-300">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Service Management System</h1>
                        <p class="text-sm text-gray-600">Received Reports</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-gray-800">{{ $selectedCustomer ? $selectedCustomer->name . ($selectedCustomer->plot_size ? ' (' . $selectedCustomer->plot_size . ')' : '') : 'N/A' }}</p>
                        <p class="text-xs text-gray-500">Printed: {{ now()->format('d/m/Y h:i A') }}</p>
                    </div>
                </div>
            </div>

            <!-- Page Title Bar (no-print) -->
            <div class="mb-6 flex items-center justify-between no-print">
                <div class="flex items-center gap-3">
                    <a href="{{ route('customer') }}" class="text-gray-500 hover:text-gray-700 p-2 rounded-lg hover:bg-gray-100 transition-colors" title="Back to Customers">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </a>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Received Reports</h2>
                        <p class="text-xs text-gray-500">Track customer bills, receipts & running balances</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('receipt') }}" class="px-4 py-2 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg hover:bg-blue-100 font-medium text-sm flex items-center gap-2 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        New Receipt
                    </a>
                </div>
            </div>

            <!-- Top Filter Form Box (Enhanced Padding & Spacing) -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-8 no-print" style="padding: 26px 30px !important;">
                <form action="{{ route('customer.ledger') }}" method="GET" class="ledger-filter-row">
                    
                    <!-- Customer Selector -->
                    <div class="ledger-col-customer">
                        <label class="block text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wider">Select Customer</label>
                        <select name="customer_id" class="select2 w-full">
                            <option value="" {{ !$selectedCustomer ? 'selected' : '' }}>-- Select Customer --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ ($selectedCustomer && $selectedCustomer->id == $c->id) ? 'selected' : '' }}>
                                    {{ $c->name }}{{ $c->plot_size ? ' (' . $c->plot_size . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- From Date -->
                    <div class="ledger-col-date">
                        <label class="block text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wider">From Date</label>
                        <input type="date" name="from_date" value="{{ request('from_date') }}" class="ledger-input">
                    </div>

                    <!-- To Date -->
                    <div class="ledger-col-date">
                        <label class="block text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wider">To Date</label>
                        <input type="date" name="to_date" value="{{ request('to_date') }}" class="ledger-input">
                    </div>

                    <!-- Action Buttons -->
                    <div class="ledger-col-actions">
                        <button type="submit" class="ledger-btn-filter">
                            Filter
                        </button>
                        
                        @if(request('customer_id') || request('from_date') || request('to_date'))
                            <a href="{{ route('customer.ledger') }}" class="ledger-btn-icon" title="Reset Filters">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Ledger Transactions Table (Explicit black background & crisp white headers) -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse bg-white">
                        <thead class="ledger-table-head">
                            <tr>
                                <th style="width: 50px; text-align: center; padding: 14px 16px !important;">#</th>
                                <th style="min-width: 140px; padding: 14px 16px !important;">Customer Name</th>
                                <th style="min-width: 200px; padding: 14px 16px !important;">Description / Particulars</th>
                                <th style="min-width: 130px; text-align: center; padding: 14px 16px !important;">Bill Date</th>
                                <th style="min-width: 140px; text-align: right; padding: 14px 16px !important;">Bill Amount</th>
                                <th style="min-width: 130px; text-align: center; padding: 14px 16px !important;">Received Date</th>
                                <th style="min-width: 140px; text-align: right; padding: 14px 16px !important;">Received Amount</th>
                                <th style="min-width: 150px; text-align: right; padding: 14px 16px !important;">Outstanding</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-100">
                            @if(!$selectedCustomer)
                                <tr>
                                    <td colspan="8" class="px-6 py-16 text-center text-gray-500 font-medium">
                                        <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        Please select a customer from the dropdown above to view their received report.
                                    </td>
                                </tr>
                            @else
                                @forelse($transactions as $index => $row)
                                    <tr class="hover:bg-gray-50/70 transition-colors">
                                        <!-- Index -->
                                        <td style="padding: 14px 16px !important;" class="text-center text-gray-500 font-medium">
                                            {{ $index + 1 }}
                                        </td>
                                        
                                        <!-- Customer Name -->
                                        <td style="padding: 14px 16px !important;" class="text-gray-800 font-semibold whitespace-nowrap">
                                            {{ $row['customer_name'] ?? $selectedCustomer->name }}
                                        </td>
                                        
                                        <!-- Particulars / Description -->
                                        <td style="padding: 14px 16px !important;" class="text-gray-700">
                                            @if(!empty($row['url']))
                                                <a href="{{ $row['url'] }}" class="text-blue-600 hover:text-blue-800 hover:underline font-medium">
                                                    {{ $row['description'] }}
                                                </a>
                                            @else
                                                <span class="font-medium text-gray-800">{{ $row['description'] }}</span>
                                            @endif
                                        </td>
                                        
                                        <!-- Bill Date -->
                                        <td style="padding: 14px 16px !important;" class="text-center whitespace-nowrap">
                                            @if($row['bill_date'] !== '-')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                                    {{ $row['bill_date'] }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 font-medium">-</span>
                                            @endif
                                        </td>

                                        <!-- Bill Amount -->
                                        <td style="padding: 14px 16px !important;" class="text-right font-medium whitespace-nowrap">
                                            @if($row['bill_amount'] !== null)
                                                <span class="font-semibold text-gray-900">Rs. {{ number_format($row['bill_amount'], 2) }}</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>

                                        <!-- Received Date -->
                                        <td style="padding: 14px 16px !important;" class="text-center whitespace-nowrap">
                                            @if($row['received_date'] !== '-')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    {{ $row['received_date'] }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 font-medium">-</span>
                                            @endif
                                        </td>

                                        <!-- Received Amount -->
                                        <td style="padding: 14px 16px !important;" class="text-right font-medium whitespace-nowrap">
                                            @if($row['received_amount'] !== null)
                                                <span class="font-semibold text-emerald-600">Rs. {{ number_format($row['received_amount'], 2) }}</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>

                                        <!-- Outstanding Balance -->
                                        <td style="padding: 14px 16px !important;" class="text-right font-bold text-gray-900 whitespace-nowrap">
                                            Rs. {{ number_format(abs($row['balance']), 2) }} 
                                            <span class="text-xs font-semibold {{ $row['balance'] >= 0 ? 'text-red-500' : 'text-emerald-500' }}">{{ $row['balance'] >= 0 ? 'Dr' : 'Cr' }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-12 text-center text-gray-400 font-medium">
                                            <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            No bills or receipts found for this customer in selected period.
                                        </td>
                                    </tr>
                                @endforelse
                            @endif
                        </tbody>
                        @if($selectedCustomer && count($transactions) > 0)
                            <tfoot class="bg-gray-100 font-bold text-gray-900 border-t-2 border-gray-300">
                                <tr>
                                    <td colspan="4" style="padding: 14px 16px !important;" class="text-right uppercase tracking-wider text-xs text-gray-600">Total:</td>
                                    <td style="padding: 14px 16px !important;" class="text-right text-gray-900 font-bold">Rs. {{ number_format($totalBilled, 2) }}</td>
                                    <td></td>
                                    <td style="padding: 14px 16px !important;" class="text-right text-emerald-600 font-bold">Rs. {{ number_format($totalReceived, 2) }}</td>
                                    <td style="padding: 14px 16px !important;" class="text-right font-bold text-gray-900">
                                        Rs. {{ number_format(abs($finalOutstanding), 2) }} 
                                        <span class="text-xs font-semibold {{ $finalOutstanding >= 0 ? 'text-red-500' : 'text-emerald-500' }}">{{ $finalOutstanding >= 0 ? 'Dr' : 'Cr' }}</span>
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Select2 Initialization Script -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "-- Select Customer --",
                allowClear: true,
                width: '100%'
            });
            // Automatically submit when customer changes
            $('.select2').on('change', function() {
                if ($(this).val()) {
                    $(this).closest('form').submit();
                }
            });
        });
    </script>
</x-app-layout>
