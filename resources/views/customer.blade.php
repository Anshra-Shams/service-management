<x-app-layout>
    <div class="py-8 bg-gray-50" x-data="{ showModal: {{ $errors->any() && !old('is_edit') ? 'true' : 'false' }}, showEditModal: {{ $errors->any() && old('is_edit') ? 'true' : 'false' }}, editData: {} }">
        <div class="w-full px-4 md:px-8 mx-auto">
            
            @if(session('success'))
                <div x-data="{ show: true }" 
                     x-show="show" 
                     x-init="setTimeout(() => show = false, 3000)"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform -translate-y-2"
                     class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm flex items-center justify-between" role="alert">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <span class="block sm:inline font-medium">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-green-500 hover:text-green-700 focus:outline-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                <div class="p-8 text-gray-900">
                    
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-800">Customer List</h3>
                        <div class="flex gap-2">
                            <a href="{{ route('customer.ledger') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                                Ledger
                            </a>
                            <button @click="showModal = true" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg shadow-sm transition-colors">
                                + Add New Customer
                            </button>
                        </div>
                    </div>

                    <!-- Customer Table -->
                    @if(isset($customers) && $customers->count() > 0)
                        <div class="overflow-x-auto rounded-lg border border-gray-200">
                            <table class="w-full text-left border-collapse bg-white">
                                <thead>
                                    <tr class="text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-200 bg-gray-50/50">
                                        <th class="px-6 py-4">ID</th>
                                        <th class="px-6 py-4">Customer Info</th>
                                        <th class="px-6 py-4">Contact</th>
                                        <th class="px-6 py-4">Address</th>
                                        <th class="px-6 py-4 text-right">Opening Balance</th>
                                        <th class="px-6 py-4 text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                                    @foreach($customers as $customer)
                                    <tr class="hover:bg-blue-50/30 transition-colors">
                                        <td class="px-6 py-4 text-gray-500 font-medium">#{{ str_pad($customer->id, 4, '0', STR_PAD_LEFT) }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="h-9 w-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm mr-3">
                                                    {{ substr($customer->name, 0, 1) }}
                                                </div>
                                                <span class="font-semibold text-gray-900">{{ $customer->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600">{{ $customer->contact_number ?: 'N/A' }}</td>
                                        <td class="px-6 py-4 text-gray-500 truncate max-w-[200px]" title="{{ $customer->address }}">{{ $customer->address ?: 'N/A' }}</td>
                                        <td class="px-6 py-4 font-bold text-right text-gray-900">
                                            Rs {{ number_format($customer->opening_balance, 2) }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex justify-center gap-2">
                                                <button @click="editData = {{ $customer }}; showEditModal = true" class="text-blue-500 hover:text-blue-700 p-1 rounded hover:bg-blue-50 transition" title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                </button>
                                                <form id="delete-customer-{{ $customer->id }}" action="{{ route('customer.destroy', $customer->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" onclick="confirmDelete('delete-customer-{{ $customer->id }}', 'this customer')" class="text-red-500 hover:text-red-700 p-1 rounded hover:bg-red-50 transition" title="Delete">
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
                        @if($customers->hasPages())
                            <div class="px-6 py-4 border-t border-gray-100">
                                {{ $customers->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-12 text-gray-500 border-2 border-dashed border-gray-200 rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <p class="font-medium">No customers added yet.</p>
                            <p class="text-sm mt-1">Click the "+ Add New Customer" button above to create one.</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>

        <!-- Add Customer Modal -->
        <div x-show="showModal" 
             style="display: none;"
             class="fixed inset-0 z-50 overflow-y-auto" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true">
            
            <!-- Backdrop -->
            <div x-show="showModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" 
                 @click="showModal = false"></div>

            <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                <!-- Modal Panel -->
                <div x-show="showModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-4" id="modal-title">Add New Customer</h3>
                                
                                <form action="{{ route('customer.store') }}" method="POST">
                                    @csrf
                                    
                                    <div class="mb-5">
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Name <span class="text-red-500">*</span></label>
                                        <input type="text" name="name" placeholder="e.g. John Doe" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-colors">
                                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="mb-5">
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Contact Number</label>
                                        <input type="text" name="contact_number" placeholder="e.g. 0300 1234567" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-colors">
                                        @error('contact_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="mb-5">
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Address</label>
                                        <textarea name="address" rows="3" placeholder="(Plot/House, Sector, Street No, Phase)" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-colors"></textarea>
                                        @error('address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="mb-8">
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Opening Balance</label>
                                        <div class="relative">
                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                                <span class="text-gray-500 font-medium sm:text-sm">Rs</span>
                                            </div>
                                            <input type="number" step="0.01" name="opening_balance" value="0.00" placeholder="0.00" class="w-full px-4 py-2.5 pl-10 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-colors">
                                        </div>
                                        @error('opening_balance') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="flex justify-end gap-3 mt-5 sm:mt-4">
                                        <button type="button" @click="showModal = false" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm transition-colors">
                                            Cancel
                                        </button>
                                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-sm font-medium text-sm transition-colors">
                                            Save Customer
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Customer Modal -->
        <div x-show="showEditModal" 
             style="display: none;"
             class="fixed inset-0 z-50 overflow-y-auto" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true">
            
            <div x-show="showEditModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" 
                 @click="showEditModal = false"></div>

            <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                <div x-show="showEditModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-4">Edit Customer</h3>
                                
                                <form :action="`/customer/${editData.id}`" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="is_edit" value="1">
                                    
                                    <div class="mb-5">
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Name <span class="text-red-500">*</span></label>
                                        <input type="text" name="name" x-model="editData.name" placeholder="e.g. John Doe" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-colors">
                                    </div>

                                    <div class="mb-5">
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Contact Number</label>
                                        <input type="text" name="contact_number" x-model="editData.contact_number" placeholder="e.g. 0300 1234567" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-colors">
                                    </div>

                                    <div class="mb-5">
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Address</label>
                                        <textarea name="address" rows="3" x-model="editData.address" placeholder="(Plot/House, Sector, Street No, Phase)" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-colors"></textarea>
                                    </div>

                                    <div class="mb-8">
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Opening Balance</label>
                                        <div class="relative">
                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                                <span class="text-gray-500 font-medium sm:text-sm">Rs</span>
                                            </div>
                                            <input type="number" step="0.01" name="opening_balance" x-model="editData.opening_balance" placeholder="0.00" class="w-full px-4 py-2.5 pl-10 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-colors">
                                        </div>
                                    </div>

                                    <div class="flex justify-end gap-3 mt-5 sm:mt-4">
                                        <button type="button" @click="showEditModal = false" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm transition-colors">
                                            Cancel
                                        </button>
                                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-sm font-medium text-sm transition-colors">
                                            Update Customer
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
