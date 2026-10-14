<x-app-layout>
    <div class="py-8 bg-gray-50" x-data="{ showModal: false, showEditModal: false, editData: {}, viewRatesModal: false, viewRatesData: { name: '', rates: [] } }">
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
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <h3 class="text-xl font-bold text-gray-800">Available Categories</h3>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <form action="{{ route('service') }}" method="GET" style="display: flex; align-items: center; margin: 0;">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </div>
                                    <input type="text" name="search" value="{{ request('search') }}" oninput="clearTimeout(this.timer); this.timer = setTimeout(() => this.form.submit(), 600)" placeholder="Search..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-48 shadow-sm">
                                </div>
                            </form>
                            <button @click="showModal = true" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg shadow-sm transition-colors whitespace-nowrap">
                                + Add New Category
                            </button>
                        </div>
                    </div>

                    <!-- Service Table -->
                    @if(isset($services) && $services->count() > 0)
                        <div class="overflow-x-auto rounded-lg border border-gray-200">
                            <table class="w-full text-left border-collapse bg-white border border-gray-200">
                                <thead>
                                    <tr class="text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50/50">
                                        <th class="px-6 py-4 border border-gray-200">ID</th>
                                        <th class="px-6 py-4 border border-gray-200">Category</th>
                                        <th class="px-6 py-4 border border-gray-200">Services & Charges</th>
                                        <th class="px-6 py-4 text-center border border-gray-200">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm text-gray-700">
                                    @foreach($services as $service)
                                    <tr class="hover:bg-blue-50/30 transition-colors">
                                        <td class="px-6 py-4 text-gray-500 font-medium border border-gray-200">#{{ str_pad($service->id, 4, '0', STR_PAD_LEFT) }}</td>
                                        <td class="px-6 py-4 border border-gray-200">
                                            <span class="font-semibold text-gray-900">{{ $service->name }}</span>
                                        </td>
                                        <td class="px-6 py-4 border border-gray-200">
                                            @php
                                                $rates = [];
                                                if (is_array($service->details) && count($service->details) > 0) {
                                                    foreach ($service->details as $item) {
                                                        $s = trim($item['service'] ?? $item['marla'] ?? '');
                                                        $a = trim($item['amount'] ?? '');
                                                        if ($s !== '' || $a !== '') {
                                                            $rates[] = [
                                                                'service' => $s !== '' ? $s : '-',
                                                                'marla' => $s !== '' ? $s : '-',
                                                                'amount' => $a !== '' ? number_format((float)$a, 2) : '-'
                                                            ];
                                                        }
                                                    }
                                                } elseif (!empty($service->marla) || !empty($service->amount)) {
                                                    $rates[] = [
                                                        'service' => $service->marla ?: '-',
                                                        'marla' => $service->marla ?: '-',
                                                        'amount' => $service->amount ? number_format((float)$service->amount, 2) : '-'
                                                    ];
                                                }
                                            @endphp

                                            @if(count($rates) === 0)
                                                <span class="text-gray-400 font-medium">-</span>
                                            @else
                                                <button type="button" 
                                                        @click="viewRatesData = { name: '{{ addslashes($service->name) }}', rates: {{ json_encode($rates) }} }; viewRatesModal = true" 
                                                        class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white border border-blue-200 shadow-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1 group">
                                                    <svg class="w-4 h-4 text-blue-500 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                    @if(count($rates) === 1)
                                                        <span>Service: {{ $rates[0]['service'] }} &bull; Rs {{ $rates[0]['amount'] }}</span>
                                                    @else
                                                        <span>View Services ({{ count($rates) }})</span>
                                                    @endif
                                                </button>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center border border-gray-200">
                                            <div class="flex justify-center gap-2">
                                                <button @click="editData = {{ $service }}; if(!editData.details || !editData.details.length) { editData.details = [{amount: editData.amount || '', service: editData.marla || ''}]; } else { editData.details = editData.details.map(d => ({ amount: d.amount !== undefined ? d.amount : '', service: d.service || d.marla || '' })); } showEditModal = true" class="text-blue-500 hover:text-blue-700 p-1 rounded hover:bg-blue-50 transition" title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                </button>
                                                <form id="delete-service-{{ $service->id }}" action="{{ route('service.destroy', $service->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" onclick="confirmDelete('delete-service-{{ $service->id }}', 'this category')" class="text-red-500 hover:text-red-700 p-1 rounded hover:bg-red-50 transition" title="Delete">
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
                        @if($services->hasPages() || $services->total() > 0)
                            <x-pagination :items="$services" />
                        @endif
                    @else
                        <div class="text-center py-12 text-gray-500 border-2 border-dashed border-gray-200 rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <p class="font-medium">No services added yet.</p>
                            <p class="text-sm mt-1">Click the "+ Add New Category" button above to create one.</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>

        <!-- Add Service Modal -->
        <div x-show="showModal" 
             style="display: none;"
             class="fixed inset-0 z-50 overflow-y-auto" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true">
            
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
                                <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-4" id="modal-title">Add New Category</h3>
                                
                                <form action="{{ route('service.store') }}" method="POST" x-data="{ details: [{service: '', amount: ''}] }">
                                    @csrf
                                    
                                    <div class="mb-4">
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Category <span class="text-red-500">*</span></label>
                                        <input type="text" name="name" placeholder="e.g. Residential" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-colors">
                                    </div>
                                    
                                    <template x-for="(detail, index) in details" :key="index">
                                        <div class="flex gap-3 mb-3 items-end" style="display: flex; align-items: flex-end; gap: 0.75rem;">
                                            <div class="flex-1">
                                                <label class="block text-sm font-semibold text-gray-700 mb-1.5" x-show="index === 0">Service</label>
                                                <input type="text" :name="`details[${index}][service]`" x-model="detail.service" placeholder="e.g. Water" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-colors" style="height: 42px;">
                                            </div>

                                            <div class="flex-1">
                                                <label class="block text-sm font-semibold text-gray-700 mb-1.5" x-show="index === 0">Amount</label>
                                                <input type="number" step="0.01" :name="`details[${index}][amount]`" x-model="detail.amount" placeholder="e.g. 5000" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-colors" style="height: 42px;">
                                            </div>
                                            
                                            <div class="flex items-center gap-2" style="height: 42px; display: flex; align-items: center; gap: 0.5rem;">
                                                <button type="button" @click="details.push({service: '', amount: ''})" x-show="index === details.length - 1" class="px-3 py-1.5 bg-blue-100 text-blue-600 rounded-md hover:bg-blue-200 transition-colors font-semibold text-xs whitespace-nowrap shadow-sm">Add</button>
                                                <button type="button" @click="details.splice(index, 1)" x-show="details.length > 1" class="px-2 py-1.5 text-red-500 hover:text-red-700 font-semibold text-xs transition-colors" title="Delete">Del</button>
                                            </div>
                                        </div>
                                    </template>

                                    <div class="flex justify-end gap-3 mt-5 sm:mt-4">
                                        <button type="button" @click="showModal = false" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm transition-colors">
                                            Cancel
                                        </button>
                                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-sm font-medium text-sm transition-colors">
                                            Save Category
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Category Modal -->
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
                                <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-4">Edit Category</h3>
                                
                                <form :action="`/service/${editData.id}`" method="POST">
                                    @csrf
                                    @method('PUT')
                                    
                                    <div class="mb-4">
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Category <span class="text-red-500">*</span></label>
                                        <input type="text" name="name" x-model="editData.name" placeholder="e.g. Residential" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-colors">
                                    </div>

                                    <template x-for="(detail, index) in editData.details" :key="index">
                                        <div class="flex gap-3 mb-3 items-end" style="display: flex; align-items: flex-end; gap: 0.75rem;">
                                            <div class="flex-1">
                                                <label class="block text-sm font-semibold text-gray-700 mb-1.5" x-show="index === 0">Service</label>
                                                <input type="text" :name="`details[${index}][service]`" x-model="detail.service" placeholder="e.g. Water" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-colors" style="height: 42px;">
                                            </div>

                                            <div class="flex-1">
                                                <label class="block text-sm font-semibold text-gray-700 mb-1.5" x-show="index === 0">Amount</label>
                                                <input type="number" step="0.01" :name="`details[${index}][amount]`" x-model="detail.amount" placeholder="e.g. 5000" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm transition-colors" style="height: 42px;">
                                            </div>
                                            
                                            <div class="flex items-center gap-2" style="height: 42px; display: flex; align-items: center; gap: 0.5rem;">
                                                <button type="button" @click="editData.details.push({service: '', amount: ''})" x-show="index === editData.details.length - 1" class="px-3 py-1.5 bg-blue-100 text-blue-600 rounded-md hover:bg-blue-200 transition-colors font-semibold text-xs whitespace-nowrap shadow-sm">Add</button>
                                                <button type="button" @click="editData.details.splice(index, 1)" x-show="editData.details.length > 1" class="px-2 py-1.5 text-red-500 hover:text-red-700 font-semibold text-xs transition-colors" title="Delete">Del</button>
                                            </div>
                                        </div>
                                    </template>

                                    <div class="flex justify-end gap-3 mt-5 sm:mt-4">
                                        <button type="button" @click="showEditModal = false" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm transition-colors">
                                            Cancel
                                        </button>
                                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-sm font-medium text-sm transition-colors">
                                            Update Category
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Rates Modal -->
        <div x-show="viewRatesModal" 
             style="display: none;"
             class="fixed inset-0 z-50 overflow-y-auto" 
             role="dialog" 
             aria-modal="true">
            
            <div x-show="viewRatesModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" 
                 @click="viewRatesModal = false"></div>

            <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                <div x-show="viewRatesModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                    
                    <div class="bg-white px-5 pt-5 pb-4 sm:p-6">
                        <div class="flex items-center justify-between pb-3.5 border-b border-gray-100">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900" x-text="viewRatesData.name"></h3>
                                    <p class="text-xs text-gray-500">Services & Amount Charges</p>
                                </div>
                            </div>
                            <button @click="viewRatesModal = false" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-100 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div class="mt-4 max-h-80 overflow-y-auto rounded-lg border border-gray-200 shadow-2xs">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-gray-50 text-xs font-semibold text-gray-600 uppercase tracking-wider sticky top-0 z-10 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3 bg-gray-50">#</th>
                                        <th class="px-4 py-3 bg-gray-50">Service</th>
                                        <th class="px-4 py-3 bg-gray-50 text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="(rate, idx) in viewRatesData.rates" :key="idx">
                                        <tr class="hover:bg-blue-50/50 transition-colors">
                                            <td class="px-4 py-3 text-xs text-gray-400 font-medium" x-text="idx + 1"></td>
                                            <td class="px-4 py-3 font-semibold text-gray-800">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded bg-gray-100 text-gray-800 text-xs font-semibold" x-text="rate.service || rate.marla"></span>
                                            </td>
                                            <td class="px-4 py-3 font-bold text-blue-600 text-right whitespace-nowrap">
                                                Rs <span x-text="rate.amount"></span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-5 flex items-center justify-between pt-3 border-t border-gray-100">
                            <span class="text-xs text-gray-500 font-medium">
                                Total: <span class="font-bold text-gray-700" x-text="viewRatesData.rates.length"></span> service(s)
                            </span>
                            <button type="button" @click="viewRatesModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-lg text-sm font-semibold transition">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure you want to delete this category?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-service-' + id).submit();
                }
            })
        }
    </script>
</x-app-layout>
