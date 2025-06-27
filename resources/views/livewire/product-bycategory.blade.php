<div>
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Menu Category</h1>

    {{-- Search --}}
    <div class="relative mb-4">
        <input type="text" wire:model.live="search"
            placeholder="Cari produk..."
            class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-300 text-lg">
        <svg class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" width="24" height="24" viewBox="0 0 24 24"
            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
    </div>

    {{-- Category Filter --}}
    <div class="flex flex-wrap gap-4 mb-6">
        @foreach ($categories as $category)
            <button wire:click="updateCategory({{ $category->id }})" wire:key="category-{{ $category->id }}"
                class="px-4 py-2 rounded-xl transition font-medium
                    {{ $selectedCategory == $category->id 
                        ? 'bg-orange-500 text-white' 
                        : 'bg-gray-200 text-gray-700 hover:bg-orange-100' }}">
                {{ $category->name }}
            </button>
        @endforeach
    </div>

    {{-- Produk List --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($product as $item)
            <div wire:key="product-{{ $item->id }}" class="bg-white rounded-xl shadow-md p-4 flex flex-col justify-between hover:shadow-lg transition">
                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}"
                    class="w-full h-40 object-cover rounded-md mb-4">

                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-800 mb-1">{{ $item->name }}</h3>
                    <p class="text-sm text-gray-600 mb-2">Kategori: {{ $item->category->name }}</p>

                    <div class="flex justify-between items-center mb-2">
                        <span class="text-orange-600 font-bold text-lg">Rp{{ number_format($item->price, 0, ',', '.') }}</span>
                        <span class="text-sm text-gray-500">Stok: {{ $item->stock }}</span>
                    </div>
                </div>

                <div class="mt-4">
                    @if ($item->stock > 0)
                        <button wire:click="addToCart({{ $item->id }})"
                                wire:loading.attr="disabled"
                                wire:target="addToCart"
                                class="w-full bg-orange-500 text-white py-2 rounded-lg font-semibold hover:bg-orange-600 transition">
                            <span wire:loading.remove wire:target="addToCart">Masukkan Keranjang</span>
                            <span wire:loading wire:target="addToCart">Menambahkan...</span>
                        </button>
                    @else
                        <button disabled
                            class="w-full bg-gray-300 text-gray-600 py-2 rounded-lg font-semibold cursor-not-allowed">
                            Stok Habis
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center bg-gray-100 p-8 rounded-xl shadow text-center">
                <img src="https://www.svgrepo.com/show/428431/box-package-empty.svg" 
                    alt="No products" 
                    class="w-40 h-40 mb-6 opacity-60">
                <h3 class="text-xl font-semibold text-gray-600 mb-2">Tidak ada produk ditemukan</h3>
                <p class="text-gray-500">Silakan pilih kategori lain atau ubah pencarian.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $product->links() }}
    </div>
</div>
