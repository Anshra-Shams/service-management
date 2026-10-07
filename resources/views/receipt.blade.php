<x-app-layout>
    <div class="py-8 bg-gray-50"
         x-data="{
            showModal: false,
            isEdit: false,
            form: {},
            invoices: {{ Js::from($invoices) }},
            blank() {
                return { id: null, customer_id: '', invoice_id: '', receipt_date: '{{ now()->toDateString() }}', amount: '', payment_method: 'cash', reference_no: '', remarks: '' };
            },
            openAdd() { this.form = this.blank(); this.isEdit = false; this.showModal = true; },
            openEdit(r) {
                this.form = { ...r, invoice_id: r.invoice_id ?? '', reference_no: r.reference_no ?? '', remarks: r.remarks ?? '', receipt_date: (r.receipt_date || '').substring(0, 10) };
                this.isEdit = true; this.showModal = true;
            },
            get customerInvoices() { return this.invoices.filter(i => i.customer_id == this.form.customer_id); },
            pickInvoice() {
                const inv = this.invoices.find(i => i.id == this.form.invoice_id);
                if (inv && !this.form.amount) this.form.amount = inv.amount;
            }
         }"
         x-init="form = blank(); @if($errors->any()) form = { ...blank(), ...{{ Js::from(old()) }} }; isEdit = !!form.id; showModal = true; @endif">
        <div class="w-full px-4 md:px-8 mx-auto">

            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                     class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm flex items-center justify-between" role="alert">
                    <span class="font-medium">{{ session('success') }}</span>
                    <button @click="show = false" class="text-green-500 hover:text-green-700">&times;</button>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                <div class="p-8 text-gray-900">

                    <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Receipt Vouchers</h3>
                            <p class="text-sm text-gray-500 mt-1">Total Received: <span class="font-semibold text-green-600">Rs. {{ number_format($totalReceived, 2) }}</span></p>
                        </div>
                        <button @click="openAdd()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg shadow-sm transition-colors">
                            + New Receipt Voucher
                        </button>
                    </div>

                    <!-- Filters -->
                    <form method="GET" action="{{ route('receipt') }}" style="align-items: flex-end;" class="flex flex-wrap gap-3 mb-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Search</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Receipt #, customer, ref no" class="px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">From</label>
                            <input type="date" name="from_date" value="{{ request('from_date') }}" class="px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">To</label>
                            <input type="date" name="to_date" value="{{ request('to_date') }}" class="px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <button type="submit" style="height: 38px;" class="px-4 bg-gray-800 text-white rounded-lg text-sm font-medium hover:bg-gray-900">Filter</button>
                        @if(request()->hasAny(['search', 'from_date', 'to_date']))
                            <a href="{{ route('receipt') }}" style="height: 38px;" class="px-4 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 flex items-center justify-center">Reset</a>
                        @endif
                    </form>

                    @if($receipts->count() > 0)
                        <div class="overflow-x-auto rounded-lg border border-gray-200">
                            <table class="w-full text-left border-collapse bg-white">
                                <thead>
                                    <tr class="text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-200 bg-gray-50/50">
                                        <th class="px-6 py-4">Receipt #</th>
                                        <th class="px-6 py-4">Date</th>
                                        <th class="px-6 py-4">Customer</th>
                                        <th class="px-6 py-4 text-right">Amount</th>
                                        <th class="px-6 py-4 text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                                    @foreach($receipts as $receipt)
                                    <tr class="hover:bg-blue-50/30 transition-colors">
                                        <td class="px-6 py-4 text-gray-500 font-medium">RV-{{ str_pad($receipt->id, 4, '0', STR_PAD_LEFT) }}</td>
                                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($receipt->receipt_date)->format('d M Y') }}</td>
                                        <td class="px-6 py-4 font-semibold text-gray-900">{{ $receipt->customer->name ?? '-' }}</td>
                                        <td class="px-6 py-4 text-right font-semibold text-green-600">Rs. {{ number_format($receipt->amount, 2) }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex justify-center gap-2">
                                                <a href="{{ route('receipt.show', $receipt->id) }}" class="text-gray-500 hover:text-gray-700 p-1 rounded hover:bg-gray-100" title="View / Print">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                                </a>
                                                <button @click="openEdit({{ Js::from($receipt->only(['id','customer_id','invoice_id','receipt_date','amount','payment_method','reference_no','remarks'])) }})" class="text-blue-500 hover:text-blue-700 p-1 rounded hover:bg-blue-50" title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                </button>
                                                <form id="delete-receipt-{{ $receipt->id }}" action="{{ route('receipt.destroy', $receipt->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" onclick="confirmDelete('{{ $receipt->id }}')" class="text-red-500 hover:text-red-700 p-1 rounded hover:bg-red-50" title="Delete">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($receipts->hasPages())
                            <div class="px-6 py-4 border-t border-gray-100">{{ $receipts->links() }}</div>
                        @endif
                    @else
                        <div class="text-center py-12 text-gray-500 border-2 border-dashed border-gray-200 rounded-lg">
                            <p class="font-medium">No receipt vouchers found.</p>
                            <p class="text-sm mt-1">Click "+ New Receipt Voucher" to record a payment.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Add / Edit Modal -->
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" @click="showModal = false"></div>
            <div class="flex min-h-screen items-center justify-center p-4">
                <div x-show="showModal" x-transition class="relative w-full max-w-md rounded-xl bg-white shadow-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4" x-text="isEdit ? 'Edit Receipt Voucher' : 'New Receipt Voucher'"></h3>

                    @if($errors->any())
                        <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded text-sm">
                            @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                        </div>
                    @endif

                    <form :action="isEdit ? `{{ url('receipt') }}/${form.id}` : '{{ route('receipt.store') }}'" method="POST">
                        @csrf
                        <template x-if="isEdit"><input type="hidden" name="_method" value="PUT"></template>
                        <input type="hidden" name="id" :value="form.id">
                        <input type="hidden" name="payment_method" value="cash">

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Customer <span class="text-red-500">*</span></label>
                                <select name="customer_id" x-model="form.customer_id" @change="form.invoice_id = ''" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }} (Balance: Rs. {{ number_format($customer->closing_balance, 2) }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Receipt Date <span class="text-red-500">*</span></label>
                                <input type="date" name="receipt_date" x-model="form.receipt_date" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Amount (Rs.) <span class="text-red-500">*</span></label>
                                <input type="number" step="0.01" min="0.01" name="amount" x-model="form.amount" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Remarks</label>
                                <textarea name="remarks" x-model="form.remarks" rows="2" placeholder="Any additional notes..." class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="showModal = false" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-sm font-medium text-sm" x-text="isEdit ? 'Update Receipt' : 'Save Receipt'"></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Delete this receipt voucher?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-receipt-' + id).submit();
                }
            })
        }
    </script>
</x-app-layout>
