<x-app-layout>
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container .select2-selection--single {
            height: 40px !important;
            border: 1px solid #e5e7eb !important; /* tailwind gray-200 */
            border-radius: 0.5rem !important;
            display: flex;
            align-items: center;
            background-color: #f9fafb !important; /* tailwind gray-50 */
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            padding-left: 0.5rem;
            font-size: 0.875rem; /* text-sm */
            color: #111827; /* gray-900 */
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px !important;
            right: 10px !important;
        }
        .select2-container--default .select2-selection--single:focus {
            outline: none;
            border-color: #3b82f6 !important; /* blue-500 */
            box-shadow: 0 0 0 1px #3b82f6 !important;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #3b82f6 !important; /* blue-500 */
        }
        .select2-container .select2-selection--multiple {
            min-height: 40px !important;
            border: 1px solid #e5e7eb !important; /* tailwind gray-200 */
            border-radius: 0.5rem !important;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            background-color: #f9fafb !important; /* tailwind gray-50 */
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            padding: 3px 6px !important;
            font-size: 0.875rem; /* text-sm */
            color: #111827; /* gray-900 */
        }
        .select2-container--default.select2-container--focus .select2-selection--multiple,
        .select2-container--default.select2-container--open .select2-selection--multiple {
            outline: none;
            border-color: #3b82f6 !important; /* blue-500 */
            box-shadow: 0 0 0 1px #3b82f6 !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 4px;
            padding: 0 !important;
            margin: 0 !important;
            width: 100%;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #eff6ff !important;
            border: 1px solid #bfdbfe !important;
            color: #1d4ed8 !important;
            border-radius: 0.375rem !important;
            padding: 2px 8px !important;
            font-size: 0.8125rem !important;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            margin: 2px 0 !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #3b82f6 !important;
            margin-right: 6px !important;
            font-weight: bold;
            border: none !important;
            padding: 0 !important;
            background: transparent !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #ef4444 !important;
            background: transparent !important;
        }
        .select2-container--default .select2-search--inline .select2-search__field {
            margin-top: 0 !important;
            margin-left: 4px !important;
            height: 28px !important;
            font-size: 0.875rem !important;
            color: #374151 !important;
            font-family: inherit !important;
            border: none !important;
            background: transparent !important;
            box-shadow: none !important;
        }
        .select2-dropdown {
            border-color: #e5e7eb !important;
            border-radius: 0.5rem !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        .select2-search__field {
            border-radius: 0.375rem !important;
            border-color: #d1d5db !important;
        }
    </style>

    <div class="py-4 bg-gray-50/50 min-h-screen">
        <div class="w-full" style="padding-left: 1.5rem; padding-right: 1.5rem;">
            <div class="bg-white overflow-hidden shadow-lg shadow-gray-200/50 rounded-2xl border border-gray-100">
                
                <!-- Form Header -->
                <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex justify-between items-center" style="padding: 0.75rem 2rem;">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Invoice Details</h3>
                        <p class="text-sm text-gray-500 mt-1">Update the customer and service details for this invoice.</p>
                    </div>
                    <a href="{{ route('create-bill') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-sm transition-colors">
                        &larr; Back to Bills
                    </a>
                </div>

                <div style="padding: 0.75rem 2rem;">
                    <form action="{{ route('invoice.update', $invoice->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Customer</label>
                                <select name="customer_id" class="searchable-select w-full" data-placeholder="Select a customer..." required>
                                    <option value=""></option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ $invoice->customer_id == $customer->id ? 'selected' : '' }}>{{ $customer->name }} - (Balance: Rs. {{ number_format($customer->opening_balance, 2) }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                <input type="text" name="category" value="{{ $invoice->category }}" placeholder="Enter category..." class="block w-full bg-gray-50 border-gray-200 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2 px-3">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                                <input type="text" name="type" value="{{ $invoice->type }}" placeholder="Enter type..." class="block w-full bg-gray-50 border-gray-200 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2 px-3">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Issue Date</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <input type="date" name="service_date" id="issue_date_input" value="{{ $invoice->service_date }}" value="{{ date('Y-m-d') }}" class="pl-10 block w-full bg-gray-50 border-gray-200 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2" required>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <input type="date" name="due_date" id="due_date_input" value="{{ $invoice->due_date }}" class="pl-10 block w-full bg-gray-50 border-gray-200 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2" required>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between items-center mb-3">
                            <h4 class="text-md font-semibold text-gray-700">Invoice Lines</h4>
                            <button type="button" id="add_row_btn" class="px-4 py-2 rounded-lg font-medium text-sm transition-all shadow-sm focus:outline-none flex items-center gap-2" style="background-color: #10b981; color: white;">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Row
                            </button>
                        </div>
                        <div class="overflow-x-auto border border-gray-200 rounded-lg mb-4">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 30%; min-width: 250px;">Service Rendered</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 13%; min-width: 130px;">Monthly Charges</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 13%; min-width: 130px;">Arrears</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 13%; min-width: 130px;">Late Surcharge</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 13%; min-width: 130px;">Total Bills</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 13%; min-width: 130px;">Total Payable</th>
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 5%; min-width: 60px;">Action</th>
                                    </tr>
                                </thead>
                                
                                <tbody class="bg-white divide-y divide-gray-200" id="invoice_tbody">
                                    @php
                                        $servicesList = $invoice->services->isNotEmpty() ? $invoice->services : collect([$invoice->service]);
                                    @endphp
                                    @foreach($servicesList as $s)
                                    <tr class="invoice-row">
                                        <td class="px-4 py-3">
                                            <select name="service_ids[]" class="service-select w-full" data-placeholder="Select service..." required>
                                                <option value=""></option>
                                                @foreach($services as $srv)
                                                    <option value="{{ $srv->id }}" {{ ($s && $srv->id == $s->id) ? 'selected' : '' }}>
                                                        {{ $srv->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" name="monthly_charges[]" value="{{ $s->pivot->monthly_charges ?? $s->amount ?? 0 }}" placeholder="0.00" step="0.01" class="monthly-charges block w-full bg-gray-50 border-gray-200 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" name="arrears[]" value="{{ $s->pivot->arrears ?? 0 }}" placeholder="0.00" step="0.01" class="arrears block w-full bg-gray-50 border-gray-200 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" name="late_payment_surcharge[]" value="{{ $s->pivot->late_surcharge ?? 0 }}" placeholder="0.00" step="0.01" class="late-surcharge block w-full bg-gray-50 border-gray-200 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" name="total_amount[]" value="{{ $s->pivot->total_bills ?? ($s->pivot->monthly_charges ?? $s->amount ?? 0) + ($s->pivot->arrears ?? 0) + ($s->pivot->late_surcharge ?? 0) }}" placeholder="0.00" step="0.01" class="total-amount block w-full bg-gray-50 border-gray-200 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-semibold text-gray-900 py-2" required readonly>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" name="total_payable[]" value="{{ $s->pivot->total_payable ?? ($s->pivot->total_bills ?? ($s->pivot->monthly_charges ?? $s->amount ?? 0) + ($s->pivot->arrears ?? 0) + ($s->pivot->late_surcharge ?? 0)) }}" placeholder="0.00" step="0.01" class="total-payable block w-full bg-gray-50 border-gray-200 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-semibold text-gray-900 py-2" required>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <button type="button" class="remove-row-btn rounded p-1.5 transition-colors" style="color: #ef4444; background-color: #fef2f2;">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>

                                <tfoot class="bg-gray-100 font-semibold text-gray-800 border-t border-gray-200">
                                    <tr>
                                        <td class="px-4 py-3 text-right">GRID TOTAL:</td>
                                        <td class="px-4 py-3">Rs. <span id="grid_total_monthly">0.00</span></td>
                                        <td class="px-4 py-3"></td>
                                        <td class="px-4 py-3"></td>
                                        <td class="px-4 py-3"></td>
                                        <td class="px-4 py-3">Rs. <span id="grid_total_payable">0.00</span></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <!-- Grand total hidden field to satisfy backend validation -->
                        <input type="hidden" name="amount" id="grand_amount" value="0">

                        <div class="flex justify-end gap-4 pt-3 mt-3 border-t border-gray-100">
                            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm transition-all shadow-md shadow-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                Update Invoice
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery, Select2, and SweetAlert2 JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            function initSelect2(element) {
                element.select2({
                    width: '100%',
                    placeholder: element.data('placeholder') || 'Select an option'
                });
            }

            $('.searchable-select').each(function() {
                initSelect2($(this));
            });
            $('.service-select').each(function() {
                initSelect2($(this));
            });

            function setDueDateFromIssue(issueVal) {
                setTimeout(calculateGrandTotal, 100);
                if (!issueVal) return;
                let d = new Date(issueVal);
                if (d.getDate() <= 10) {
                    d.setDate(10);
                } else {
                    d.setDate(d.getDate() + 10);
                }
                let y = d.getFullYear();
                let m = String(d.getMonth() + 1).padStart(2, '0');
                let day = String(d.getDate()).padStart(2, '0');
                $('#due_date_input').val(`${y}-${m}-${day}`);
            }

            $('#issue_date_input').on('change', function() {
                setDueDateFromIssue($(this).val());
            });

            if ($('#issue_date_input').val() && !$('#due_date_input').val()) {
                setDueDateFromIssue($('#issue_date_input').val());
            }

            function calculateRowTotal(row) {
                let monthlyCharges = parseFloat(row.find('.monthly-charges').val()) || 0;
                let arrears = parseFloat(row.find('.arrears').val()) || 0;
                let lateSurcharge = parseFloat(row.find('.late-surcharge').val()) || 0;
                
                let totalBills = monthlyCharges + arrears + lateSurcharge;
                row.find('.total-amount').val(totalBills.toFixed(2));
                row.find('.total-payable').val(totalBills.toFixed(2));
                
                calculateGrandTotal();
            }

            function calculateGrandTotal() {
                let grandMonthly = 0;
                let grandArrears = 0;
                let grandLate = 0;
                let grandBills = 0;
                let grandPayable = 0;
                
                $('.monthly-charges').each(function() { grandMonthly += parseFloat($(this).val()) || 0; });
                $('.arrears').each(function() { grandArrears += parseFloat($(this).val()) || 0; });
                $('.late-surcharge').each(function() { grandLate += parseFloat($(this).val()) || 0; });
                $('.total-amount').each(function() { grandBills += parseFloat($(this).val()) || 0; });
                $('.total-payable').each(function() { grandPayable += parseFloat($(this).val()) || 0; });

                $('#grid_total_monthly').text(grandMonthly.toFixed(2));
                $('#grid_total_arrears').text(grandArrears.toFixed(2));
                $('#grid_total_late').text(grandLate.toFixed(2));
                $('#grid_total_bills').text(grandBills.toFixed(2));
                $('#grid_total_payable').text(grandPayable.toFixed(2));

                $('#grand_amount').val(grandPayable.toFixed(2));
            }



            $(document).on('input change', '.total-amount, .monthly-charges, .arrears, .late-surcharge', function() {
                let row = $(this).closest('tr');
                calculateRowTotal(row);
            });

            $(document).on('input change', '.total-payable', function() {
                calculateGrandTotal();
            });

            $('#add_row_btn').on('click', function() {
                let firstRow = $('#invoice_tbody tr.invoice-row:first');
                let newRow = firstRow.clone();
                
                newRow.find('input').val('');
                newRow.find('select').val('');
                newRow.find('.select2-container').remove();
                newRow.find('select').removeClass('select2-hidden-accessible').removeAttr('data-select2-id tabindex aria-hidden');
                newRow.find('option').removeAttr('data-select2-id');
                
                $('#invoice_tbody').append(newRow);
                initSelect2(newRow.find('.service-select'));
            });

            // Prevent form submit on Enter and add new row instead if inside table
            $(document).on('keydown', '#invoice_tbody input, #invoice_tbody select', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    $('#add_row_btn').click();
                }
            });

            $(document).on('click', '.remove-row-btn', function() {
                if ($('#invoice_tbody tr.invoice-row').length > 1) {
                    $(this).closest('tr').remove();
                    calculateGrandTotal();
                } else {
                    Swal.fire('Warning', 'At least one row is required.', 'warning');
                }
            });

            @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
            @endif

            $('.delete-form').on('submit', function(e) {
                e.preventDefault();
                let form = this;

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this invoice deletion!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
</x-app-layout>
