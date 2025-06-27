<div>
    <nav class="flex flex-col space-y-4">
        @php
        $current = Route::currentRouteName();
    @endphp
    
    @php
    $current = Route::currentRouteName();
@endphp

@foreach ($menu as $item)
    <a href="{{ route($item['route']) }}"
       class="flex items-center justify-center w-12 h-12 rounded-xl transition-all duration-200 
              {{ $current === $item['route'] ? 'bg-orange-100 text-orange-600 shadow' : 'text-gray-400 hover:text-orange-600' }}"
       title="{{ $item['label'] }}">
        <i class="{{ $item['icon'] }} text-xl w-6 text-center"></i>
    </a>
@endforeach

    </nav>
</div>
