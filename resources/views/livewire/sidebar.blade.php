<div>
    @php $current = Route::currentRouteName(); @endphp
    @foreach ($menu as $item)
        <a href="{{ route($item['route']) }}"
           class="group w-12 h-12 flex items-center justify-center rounded-xl transition-all duration-200
           {{ $current === $item['route'] ? 'bg-orange-100 text-orange-600 shadow-md' : 'text-gray-400 hover:text-orange-500 hover:bg-orange-50' }}"
           title="{{ $item['label'] }}">
            <i class="{{ $item['icon'] }} text-xl group-hover:scale-110 w-6 text-center transition-transform"></i>
        </a>
    @endforeach
</div>
