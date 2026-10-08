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
        <div class="w-full mt-4" style="padding-left: 1.5rem; padding-right: 1.5rem;">
            <!-- Page Header (outside table card) -->
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Recent Invoices</h3>
                    <p class="text-sm text-gray-500 mt-1">A list of all generated invoices.</p>
                </div>
                <a href="{{ route('add-bill') }}" class="font-medium text-sm transition-all shadow-md inline-flex items-center gap-2 hover:opacity-90" style="background-color: #2563eb; color: white; padding: 0.5rem 1.25rem; border-radius: 0.5rem; text-decoration: none; white-space: nowrap;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    Add Bills
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-lg shadow-gray-200/50 rounded-2xl border border-gray-100">
                <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex justify-start items-center" style="padding: 0.75rem 2rem;">
                        <form method="GET" action="{{ route('create-bill') }}" class="flex items-center gap-3">
                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search invoice, customer..." class="border-gray-200 rounded-lg shadow-sm text-sm py-1.5 px-3 focus:border-blue-500 focus:ring-blue-500 w-64">
                            </div>
                            <div class="flex items-center gap-2">
                                <label class="text-sm font-medium text-gray-600">To:</label>
                                <input type="date" name="to_date" value="{{ request('to_date') }}" class="border-gray-200 rounded-lg shadow-sm text-sm py-1.5 focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div class="flex items-center gap-2">
                                <label class="text-sm font-medium text-gray-600">From:</label>
                                <input type="date" name="from_date" value="{{ request('from_date') }}" class="border-gray-200 rounded-lg shadow-sm text-sm py-1.5 focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <button type="submit" class="font-medium text-sm transition-all shadow-sm" style="background-color: #059669; color: white; padding: 0.375rem 1rem; border-radius: 0.5rem; border: none; cursor: pointer;">
                                Filter
                            </button>
                            @if(request()->hasAny(['search', 'from_date', 'to_date']))
                                <a href="{{ route('create-bill') }}" class="font-medium text-sm transition-all shadow-sm" style="background-color: #e5e7eb; color: #374151; padding: 0.375rem 1rem; border-radius: 0.5rem; text-decoration: none; border: none;">Clear</a>
                            @endif
                        </form>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Invoice #</th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Customer</th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Service</th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Issue Date</th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Due Date</th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($invoices as $invoice)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">INV-{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">
                                            {{ substr($invoice->customer->name ?? 'N', 0, 1) }}
                                        </div>
                                        <span>{{ $invoice->customer->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <div class="flex flex-wrap gap-1">
                                        @if($invoice->services->isNotEmpty())
                                            @foreach($invoice->services as $srv)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                                    {{ $srv->name }}
                                                </span>
                                            @endforeach
                                        @elseif($invoice->service)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $invoice->service->name }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 text-xs">N/A</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        {{ \Carbon\Carbon::parse($invoice->service_date)->format('M d, Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($invoice->due_date)
                                        <div class="flex items-center gap-2 text-amber-700 font-medium">
                                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}
                                        </div>
                                    @else
                                        <span class="text-gray-400">&mdash;</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                    Rs. {{ number_format($invoice->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        
                                        <button type="button" onclick="sendWhatsApp({{ $invoice->id }})" class="p-1.5 text-green-600 bg-green-50 hover:bg-green-100 rounded-lg transition-colors" title="Send via WhatsApp">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                        </button>
                                        <a href="{{ route('invoice.show', $invoice->id) }}" target="_blank" class="p-1.5 text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors" title="View Invoice">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                        <a href="{{ route('invoice.edit', $invoice->id) }}" class="p-1.5 text-amber-600 bg-amber-50 hover:bg-amber-100 rounded-lg transition-colors" title="Edit Invoice">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <form action="{{ route('invoice.destroy', $invoice->id) }}" method="POST" class="delete-form inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors" title="Delete Invoice">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <p class="text-base font-medium text-gray-500">No invoices generated yet</p>
                                        <p class="text-sm mt-1">Click the "Add Bills" button above to create your first bill.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($invoices->hasPages() || $invoices->total() > 0)
                    <x-pagination :items="$invoices" />
                @endif

            </div>
        </div>
    </div>

    <!-- jQuery, Select2, and SweetAlert2 JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('.searchable-select').each(function() {
                var placeholder = $(this).data('placeholder') || 'Select an option';
                $(this).select2({
                    width: '100%',
                    placeholder: placeholder
                });
            });

            function setDueDateFromIssue(issueVal) {
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

            $('#service_select').on('change', function() {
                let total = 0;
                let selected = $(this).find('option:selected');
                selected.each(function() {
                    let amt = parseFloat($(this).data('amount')) || 0;
                    total += amt;
                });
                if (selected.length > 0) {
                    $('#total_amount').val(total.toFixed(2));
                } else {
                    $('#total_amount').val('');
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

    
    <script>
        function sendWhatsApp(invoiceId) {
            Swal.fire({
                title: 'Sending WhatsApp Message...',
                text: 'Please wait...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });

            fetch('/api/whatsapp/send/' + invoiceId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(async response => {
                const text = await response.text();
                try {
                    return JSON.parse(text);
                } catch (e) {
                    throw new Error('Invalid JSON: ' + text.substring(0, 100));
                }
            })
            .then(data => {
                if(data.success) {
                    Swal.fire('Success!', 'Invoice sent via WhatsApp.', 'success');
                } else {
                    Swal.fire('Error!', data.error || 'Failed to send message.', 'error');
                }
            })
            .catch(err => {
                Swal.fire('Error!', err.message, 'error');
            });
        }
    </script>
</x-app-layout>
