@props(['items'])

@if($items->hasPages() || $items->total() > 0)
    <div class="px-6 py-4 border-t border-gray-100 bg-white" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center;" class="text-sm text-gray-500">
            <span class="mr-2">Show per page :</span>
            <form action="{{ request()->url() }}" method="GET" style="margin: 0;">
                @foreach(request()->except('per_page') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <select name="per_page" onchange="this.form.submit()" class="text-gray-700 text-sm rounded-md focus:ring-green-500 focus:border-green-500 py-1.5 pl-3 pr-8" style="border: 1px solid #a7f3d0; outline: none; border-radius: 0.375rem;">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
            </form>
        </div>
        
        <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
            <span class="text-sm text-gray-500">
                {{ $items->firstItem() ?? 0 }} - {{ $items->lastItem() ?? 0 }} of {{ $items->total() }} items
            </span>
            
            @if($items->hasPages())
            <div style="display: flex; gap: 0.25rem;">
                @foreach ($items->elements() as $element)
                    @if (is_string($element))
                        <span style="width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center; border-radius: 0.25rem; background-color: #64748b; opacity: 0.7; color: white; font-size: 0.875rem; font-weight: 500;">{{ $element }}</span>
                    @endif
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $items->currentPage())
                                <span style="width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center; border-radius: 0.25rem; background-color: #059669; color: white; font-size: 0.875rem; font-weight: 500;">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" style="width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center; border-radius: 0.25rem; background-color: #64748b; color: white; font-size: 0.875rem; font-weight: 500; text-decoration: none;">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>
            @endif
        </div>
    </div>
@endif
