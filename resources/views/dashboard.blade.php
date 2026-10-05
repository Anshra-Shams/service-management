<x-app-layout>
    <div class="py-8 bg-gray-50">
        <div class="w-full px-4 md:px-8 mx-auto">
            
            <!-- Welcome Banner -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-lg shadow-lg mb-8 p-6 text-white flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-bold mb-1">Welcome back, {{ Auth::user()->name }}! 👋</h3>
                    <p class="text-blue-100 text-sm">Here is what's happening with your service management today.</p>
                </div>
                <div class="hidden sm:block">
                    <svg class="w-16 h-16 text-blue-200 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                
                <!-- Customers Stat Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center hover:shadow-md transition-shadow">
                    <div class="p-4 rounded-full bg-blue-50 text-blue-600 mr-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Total Customers</p>
                        <h4 class="text-2xl font-bold text-gray-800">120</h4>
                    </div>
                </div>

                <!-- Services Stat Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center hover:shadow-md transition-shadow">
                    <div class="p-4 rounded-full bg-indigo-50 text-indigo-600 mr-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Active Services</p>
                        <h4 class="text-2xl font-bold text-gray-800">15</h4>
                    </div>
                </div>

                <!-- Bills Stat Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center hover:shadow-md transition-shadow">
                    <div class="p-4 rounded-full bg-emerald-50 text-emerald-600 mr-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Generated Bills</p>
                        <h4 class="text-2xl font-bold text-gray-800">342</h4>
                    </div>
                </div>
            </div>

            <!-- Quick Actions & Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('customer') }}" class="block w-full text-center py-3 px-4 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 font-medium transition-colors">
                            + Add New Customer
                        </a>
                        <a href="{{ route('create-bill') }}" class="block w-full text-center py-3 px-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors shadow-sm shadow-blue-200">
                            Create New Bill
                        </a>
                    </div>
                </div>

                <!-- Recent Activity Table (Mock data) -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Recent Bills</h3>
                        <a href="#" class="text-sm text-blue-600 hover:underline">View All</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-sm text-gray-500 border-b border-gray-100">
                                    <th class="pb-3 font-medium">Bill #</th>
                                    <th class="pb-3 font-medium">Customer</th>
                                    <th class="pb-3 font-medium">Amount</th>
                                    <th class="pb-3 font-medium">Status</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm text-gray-700">
                                <tr class="border-b border-gray-50 hover:bg-gray-50">
                                    <td class="py-3 font-medium">#INV-001</td>
                                    <td class="py-3">Ali Khan</td>
                                    <td class="py-3 font-medium text-gray-900">Rs 5,000</td>
                                    <td class="py-3"><span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Paid</span></td>
                                </tr>
                                <tr class="border-b border-gray-50 hover:bg-gray-50">
                                    <td class="py-3 font-medium">#INV-002</td>
                                    <td class="py-3">Hassan Ahmed</td>
                                    <td class="py-3 font-medium text-gray-900">Rs 12,500</td>
                                    <td class="py-3"><span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">Pending</span></td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 font-medium">#INV-003</td>
                                    <td class="py-3">Sara Ali</td>
                                    <td class="py-3 font-medium text-gray-900">Rs 3,200</td>
                                    <td class="py-3"><span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Paid</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
