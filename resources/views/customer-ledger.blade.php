<x-app-layout>
    <div class="py-8 bg-gray-50">
        <div class="w-full px-4 md:px-8 mx-auto">
            
            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('customer') }}" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </a>
                    <h2 class="text-2xl font-bold text-gray-800">Customer Ledger</h2>
                </div>
                
                <form action="{{ route('customer.ledger') }}" method="GET" class="flex items-center">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" oninput="clearTimeout(this.timer); this.timer = setTimeout(() => this.form.submit(), 600)" placeholder="Search customer..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-64 shadow-sm">
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                <div class="p-0 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse bg-white">
                            <thead>
                                <tr class="text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-200 bg-gray-50/50">
                                    <th class="px-6 py-4 flex items-center gap-1">CUSTOMER ID <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path></svg></th>
                                    <th class="px-6 py-4">
                                        <div class="flex items-center gap-1">CUSTOMER NAME <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path></svg></div>
                                    </th>
                                    <th class="px-6 py-4">
                                        <div class="flex items-center gap-1">OPENING BALANCE <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path></svg></div>
                                    </th>
                                    <th class="px-6 py-4">
                                        <div class="flex items-center gap-1">PREVIOUS BALANCE <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path></svg></div>
                                    </th>
                                    <th class="px-6 py-4">
                                        <div class="flex items-center gap-1">CLOSING BALANCE <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path></svg></div>
                                    </th>
                                    <th class="px-6 py-4">
                                        <div class="flex items-center gap-1">LAST UPDATED <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path></svg></div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                                @foreach($customers as $customer)
                                <tr class="hover:bg-gray-50/50 transition-colors {{ $loop->even ? 'bg-gray-100/50' : 'bg-white' }}">
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded text-xs font-bold" style="background-color: #059669; color: white;">
                                            {{ $customer->id }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full flex items-center justify-center font-bold text-sm mr-3" style="background-color: #059669; color: white;">
                                                {{ strtoupper(substr($customer->name, 0, 1)) }}
                                            </div>
                                            <span class="font-medium text-gray-700">{{ $customer->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">PKR {{ number_format($customer->opening_balance, 0) }}</td>
                                    <td class="px-6 py-4 text-gray-600">PKR {{ number_format($customer->opening_balance, 0) }}</td>
                                    <td class="px-6 py-4 font-medium {{ $customer->closing_balance > 0 ? 'text-red-500' : 'text-gray-600' }}">
                                        PKR {{ number_format($customer->closing_balance, 0) }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ $customer->updated_at->format('d M Y h:i A') }}
                                    </td>
                                </tr>
                                @endforeach
                                @if($customers->isEmpty())
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 font-medium">
                                        No customers found.
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    <x-pagination :items="$customers" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
