<x-app-layout>
    <div class="py-8 bg-gray-50" x-data="customerManager()">
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
                <div class="p-4 sm:p-6 text-gray-900">
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                        <h3 class="text-xl font-bold text-gray-800">Customer List</h3>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <form action="{{ route('customer') }}" method="GET" style="display: flex; align-items: center; margin: 0;">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </div>
                                    <input type="text" name="search" value="{{ request('search') }}" oninput="clearTimeout(this.timer); this.timer = setTimeout(() => this.form.submit(), 600)" placeholder="Search..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-48 shadow-sm">
                                </div>
                            </form>
                            <a href="{{ route('customer.ledger') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors whitespace-nowrap">
                                Received Reports
                            </a>
                            <button @click="showModal = true" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg shadow-sm transition-colors whitespace-nowrap">
                                + Add New Customer
                            </button>
                        </div>
                    </div>

                    <!-- Customer Table -->
                    @if(isset($customers) && $customers->count() > 0)
                        <div class="overflow-x-auto rounded-lg border border-gray-300 shadow-sm">
                            <table class="w-full text-left border-collapse bg-white text-sm border border-gray-300">
                                <thead>
                                    <tr class="text-xs font-bold text-gray-700 uppercase tracking-wider bg-gray-100 border-b border-gray-300">
                                        <th class="px-3 py-3 border border-gray-300 whitespace-nowrap">ID</th>
                                        <th class="px-3 py-3 border border-gray-300 whitespace-nowrap">Customer</th>
                                        <th class="px-3 py-3 border border-gray-300 whitespace-nowrap">Contact</th>
                                        <th class="px-3 py-3 border border-gray-300 text-center whitespace-nowrap">Category</th>
                                        <th class="px-3 py-3 border border-gray-300 text-center whitespace-nowrap">Type</th>
                                        <th class="px-2 py-3 border border-gray-300 text-center whitespace-nowrap">Plot</th>
                                        <th class="px-2 py-3 border border-gray-300 text-center whitespace-nowrap">Street</th>
                                        <th class="px-2 py-3 border border-gray-300 text-center whitespace-nowrap">Sector</th>
                                        <th class="px-2 py-3 border border-gray-300 text-center whitespace-nowrap">Phase</th>
                                        <th class="px-3 py-3 border border-gray-300 whitespace-nowrap">Address</th>
                                        <th class="px-3 py-3 border border-gray-300 text-right whitespace-nowrap">Balance</th>
                                        <th class="px-3 py-3 border border-gray-300 text-center whitespace-nowrap">Start Date</th>
                                        <th class="px-3 py-3 border border-gray-300 text-center whitespace-nowrap">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm text-gray-800 divide-y divide-gray-300">
                                    @foreach($customers as $customer)
                                    <tr class="hover:bg-blue-50/40 transition-colors">
                                        <td class="px-3 py-2.5 font-mono text-gray-500 border border-gray-300 whitespace-nowrap">#{{ str_pad($customer->id, 4, '0', STR_PAD_LEFT) }}</td>
                                        <td class="px-3 py-2.5 border border-gray-300 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-7 w-7 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs mr-2 shrink-0">
                                                    {{ substr($customer->name, 0, 1) }}
                                                </div>
                                                <span class="font-semibold text-gray-900" title="{{ $customer->name }}">{{ $customer->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-2.5 text-gray-700 border border-gray-300 whitespace-nowrap">{{ $customer->contact_number ?: '-' }}</td>
                                        <td class="px-3 py-2.5 border border-gray-300 font-semibold text-gray-900 text-center whitespace-nowrap">{{ $customer->plot_size ?: '-' }}</td>
                                        <td class="px-3 py-2.5 border border-gray-300 text-center whitespace-nowrap">
                                            @if($customer->type)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700 border border-gray-300">
                                                    {{ $customer->type }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-2 py-2.5 text-center border border-gray-300 font-medium text-gray-800 whitespace-nowrap">{{ $customer->plot ?: '-' }}</td>
                                        <td class="px-2 py-2.5 text-center border border-gray-300 font-medium text-gray-800 whitespace-nowrap">{{ $customer->street ?: '-' }}</td>
                                        <td class="px-2 py-2.5 text-center border border-gray-300 font-medium text-gray-800 whitespace-nowrap">{{ $customer->sector ?: '-' }}</td>
                                        <td class="px-2 py-2.5 text-center border border-gray-300 font-medium text-gray-800 whitespace-nowrap">{{ $customer->phase ?: '-' }}</td>
                                        <td class="px-3 py-2.5 text-gray-600 border border-gray-300 max-w-[180px] truncate" title="{{ $customer->address }}">{{ $customer->address ?: '-' }}</td>
                                        <td class="px-3 py-2.5 font-bold text-right text-gray-900 border border-gray-300 whitespace-nowrap">
                                            Rs {{ number_format($customer->opening_balance, 2) }}
                                        </td>
                                        <td class="px-3 py-2.5 text-center text-gray-700 border border-gray-300 whitespace-nowrap">
                                            {{ $customer->start_date ? \Carbon\Carbon::parse($customer->start_date)->format('d M, Y') : '-' }}
                                        </td>
                                        <td class="px-3 py-2.5 text-center border border-gray-300 whitespace-nowrap">
                                            <div class="flex justify-center items-center gap-1.5">
                                                <button @click="openEdit({{ $customer }})" class="text-blue-500 hover:text-blue-700 p-1.5 rounded hover:bg-blue-50 transition" title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                </button>
                                                <form id="delete-customer-{{ $customer->id }}" action="{{ route('customer.destroy', $customer->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" onclick="confirmDelete('delete-customer-{{ $customer->id }}', 'this customer')" class="text-red-500 hover:text-red-700 p-1.5 rounded hover:bg-red-50 transition" title="Delete">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($customers->hasPages() || $customers->total() > 0)
                            <x-pagination :items="$customers" />
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

            <div class="flex min-h-screen items-center justify-center p-3 sm:p-4 text-center">
                <!-- Modal Panel -->
                <div x-show="showModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl">
                    
                    <div class="bg-white px-5 pt-5 pb-4 sm:p-6">
                        <div class="w-full">
                            <div class="flex items-center justify-between pb-3 mb-3 border-b border-gray-100">
                                <h3 class="text-base font-bold text-gray-900" id="modal-title">Add New Customer</h3>
                                <button type="button" @click="showModal = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                            
                            <form action="{{ route('customer.store') }}" method="POST">
                                @csrf
                                
                                <!-- Row 1: Name & Contact Number -->
                                <div class="grid grid-cols-2 gap-3 mb-3" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                                        <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. John Doe" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                                        <input type="text" name="contact_number" value="{{ old('contact_number') }}" placeholder="e.g. 0300 1234567" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                        @error('contact_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <!-- Row 2: Category & Type -->
                                <div class="grid grid-cols-2 gap-3 mb-3" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                        <input type="text" 
                                               name="plot_size"
                                               value="{{ old('plot_size') }}"
                                               autocomplete="off"
                                               placeholder="e.g. 2 marhala" 
                                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                        @error('plot_size') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                        <input type="text" 
                                               name="type" 
                                               value="{{ old('type') }}"
                                               placeholder="e.g. Residential / Regular" 
                                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                        @error('type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <!-- Row 3: Plot, Street, Sector, Phase -->
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-3" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem;">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Plot</label>
                                        <input type="text" name="plot" value="{{ old('plot') }}" placeholder="e.g. 12" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                        @error('plot') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Street</label>
                                        <input type="text" name="street" value="{{ old('street') }}" placeholder="e.g. 4" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                        @error('street') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Sector</label>
                                        <input type="text" name="sector" value="{{ old('sector') }}" placeholder="e.g. B" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                        @error('sector') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phase</label>
                                        <input type="text" name="phase" value="{{ old('phase') }}" placeholder="e.g. 2" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                        @error('phase') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <!-- Row 4: Address -->
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                    <input type="text" 
                                           name="address" 
                                           value="{{ old('address') }}"
                                           placeholder="e.g. House address / Complete address" 
                                           class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                    @error('address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <!-- Row 5: Opening Balance & Start Date -->
                                <div class="grid grid-cols-2 gap-3 mb-4" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Opening Balance</label>
                                        <div class="relative">
                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <span class="text-gray-400 font-medium text-xs">Rs</span>
                                            </div>
                                            <input type="number" step="0.01" name="opening_balance" value="{{ old('opening_balance', '0.00') }}" placeholder="0.00" class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                        </div>
                                        @error('opening_balance') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                        <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                        @error('start_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="flex justify-end gap-2.5 pt-2 border-t border-gray-100">
                                    <button type="button" @click="showModal = false" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm transition-colors">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm shadow-sm transition-colors">
                                        Save Customer
                                    </button>
                                </div>
                            </form>

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

            <div class="flex min-h-screen items-center justify-center p-3 sm:p-4 text-center">
                <div x-show="showEditModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl">
                    
                    <div class="bg-white px-5 pt-5 pb-4 sm:p-6">
                        <div class="w-full">
                            <div class="flex items-center justify-between pb-3 mb-3 border-b border-gray-100">
                                <h3 class="text-base font-bold text-gray-900">Edit Customer</h3>
                                <button type="button" @click="showEditModal = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                            
                            <form :action="`/customer/${editData.id}`" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="is_edit" value="1">
                                
                                <!-- Row 1: Name & Contact Number -->
                                <div class="grid grid-cols-2 gap-3 mb-3" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                                        <input type="text" name="name" x-model="editData.name" placeholder="e.g. John Doe" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                                        <input type="text" name="contact_number" x-model="editData.contact_number" placeholder="e.g. 0300 1234567" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                    </div>
                                </div>

                                <!-- Row 2: Category & Type -->
                                <div class="grid grid-cols-2 gap-3 mb-3" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                        <input type="text" 
                                               name="plot_size" 
                                               x-model="editData.plot_size"
                                               autocomplete="off"
                                               placeholder="e.g. 2 marhala" 
                                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                        <input type="text" 
                                               name="type" 
                                               x-model="editData.type" 
                                               placeholder="e.g. Residential / Regular" 
                                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                    </div>
                                </div>

                                <!-- Row 3: Plot, Street, Sector, Phase -->
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-3" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem;">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Plot</label>
                                        <input type="text" name="plot" x-model="editData.plot" placeholder="e.g. 12" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Street</label>
                                        <input type="text" name="street" x-model="editData.street" placeholder="e.g. 4" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Sector</label>
                                        <input type="text" name="sector" x-model="editData.sector" placeholder="e.g. B" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phase</label>
                                        <input type="text" name="phase" x-model="editData.phase" placeholder="e.g. 2" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                    </div>
                                </div>

                                <!-- Row 4: Address -->
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                    <input type="text" 
                                           name="address" 
                                           x-model="editData.address" 
                                           placeholder="e.g. House address / Complete address" 
                                           class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                </div>

                                <!-- Row 5: Opening Balance & Start Date -->
                                <div class="grid grid-cols-2 gap-3 mb-4" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Opening Balance</label>
                                        <div class="relative">
                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <span class="text-gray-400 font-medium text-xs">Rs</span>
                                            </div>
                                            <input type="number" step="0.01" name="opening_balance" x-model="editData.opening_balance" placeholder="0.00" class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                        <input type="date" name="start_date" x-model="editData.start_date" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    </div>
                                </div>

                                <div class="flex justify-end gap-2.5 pt-2 border-t border-gray-100">
                                    <button type="button" @click="showEditModal = false" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm transition-colors">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm shadow-sm transition-colors">
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

    <script>
        function customerManager() {
            return {
                showModal: {{ $errors->any() && !old('is_edit') ? 'true' : 'false' }},
                showEditModal: {{ $errors->any() && old('is_edit') ? 'true' : 'false' }},
                editData: {},

                openEdit(cust) {
                    this.editData = {
                        id: cust.id,
                        name: cust.name || '',
                        contact_number: cust.contact_number || '',
                        plot_size: cust.plot_size || '',
                        type: cust.type || '',
                        plot: cust.plot || '',
                        street: cust.street || '',
                        sector: cust.sector || '',
                        phase: cust.phase || '',
                        address: cust.address || '',
                        opening_balance: cust.opening_balance || '0.00',
                        start_date: cust.start_date ? cust.start_date.substring(0, 10) : ''
                    };
                    this.showEditModal = true;
                }
            };
        }
    </script>
</x-app-layout>
