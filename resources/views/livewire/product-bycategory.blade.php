<div class="px-2 sm:px-4 md:px-6 lg:px-8">
    <!-- Judul -->
    <h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4">Menu Kategori</h1>

    <!-- Alert stok -->
    @if ($hasLowStockProducts)
        <div class="mb-3 p-3 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 rounded-md shadow text-sm">
            ⚠️ Stok hampir habis! Ada {{ $lowStockCount }} produk dengan stok kurang dari 10.
        </div>
    @endif

    @if ($hasOutOfStockProducts)
        <div class="mb-3 p-3 bg-red-100 border-l-4 border-red-500 text-red-800 rounded-md shadow text-sm">
            ⚠️ Ada {{ $outOfStockCount }} produk dengan stok habis.
        </div>
    @endif

    <!-- Search dan Filter -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-5">
        <!-- Search -->
        <div class="relative w-full md:max-w-sm">
            <input type="text" wire:model.live="search" placeholder="Cari produk..."
                class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-300 text-sm sm:text-base">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" width="20" height="20"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
        </div>

        <!-- Dropdown Urutan -->
        <div>
            <label for="sort" class="text-sm font-medium text-gray-700 mr-2">Urutkan:</label>
            <select wire:model.live="sortBy" id="sort"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-orange-300 focus:border-orange-400">
                <option value="default">📦 Stok Terbanyak</option>
                <option value="terbaru">🆕 Terbaru</option>
                <option value="terlaris">🔥 Terlaris</option>
             
            </select>
        </div>
    </div>

    <!-- Kategori -->
    <div class="flex flex-wrap gap-2 mb-6">
        @foreach ($categories as $category)
            <button wire:click="updateCategory({{ $category->id }})"
                class="px-3 py-1.5 rounded-xl text-xs sm:text-sm font-medium transition
                    {{ $selectedCategory == $category->id ? 'bg-orange-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-orange-100' }}">
                {{ $category->name }}
            </button>
        @endforeach
    </div>

    <!-- List Produk -->
    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4 md:gap-6">
        @forelse ($product as $item)
            <div class="bg-white rounded-2xl shadow p-3 hover:shadow-md transition flex flex-col w-full relative">
                <!-- Gambar -->
                <div class="relative w-full h-40 rounded-xl overflow-hidden mb-3">
                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}"
                        class="object-cover w-full h-full">

                    @if (in_array($item->id, $bestSellerIds))
                    <span class="absolute top-2 left-2 bg-indigo-600 text-white text-[10px] sm:text-xs px-1.5 sm:px-2 py-0.5 rounded-full shadow">
                        🔥 Terlaris
                    </span>
                    @endif

                    @if ($item->stock <= 0)
                    <span class="absolute top-2 right-2 bg-red-500 text-white text-[10px] sm:text-xs px-1.5 sm:px-2 py-0.5 rounded-full shadow">
                        Habis
                    </span>
                    @elseif ($item->stock <= 10)
                    <span class="absolute top-2 right-2 bg-yellow-400 text-white text-[10px] sm:text-xs px-1.5 sm:px-2 py-0.5 rounded-full shadow">
                        Sisa {{ $item->stock }}
                    </span>
                    @else
                    <span class="absolute top-2 right-2 bg-green-500 text-white text-[10px] sm:text-xs px-1.5 sm:px-2 py-0.5 rounded-full shadow">
                        Stok {{ $item->stock }}
                    </span>
                    @endif
                </div>

                <!-- Info Produk -->
                <div class="flex-1 flex flex-col">
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="text-sm sm:text-base font-semibold text-gray-800 leading-tight line-clamp-2">
                            {{ $item->name }}
                        </h3>
                        @if (\Carbon\Carbon::parse($item->created_at)->greaterThanOrEqualTo(now()->subDays(3)))
                        <span class="bg-blue-600 text-white text-[10px] sm:text-xs px-1.5 sm:px-2 py-0.5 rounded-full shadow">
                            🆕 Baru
                        </span>
                        @endif
                    </div>

                    <p class="text-xs text-gray-500 mb-2">Kategori: {{ $item->category->name }}</p>

                    <!-- Harga -->
                    <div class="mb-3">
                        @php
                        $activeDiscount = $item->getActiveDiscount();
                    @endphp
                    
                    @if ($activeDiscount)
                        <div>
                            <span class="text-red-500 text-xs line-through">
                                Rp{{ number_format($item->price, 0, ',', '.') }}
                            </span><br>
                            <span class="text-green-600 font-bold text-base">
                                Rp{{ number_format($item->discounted_price, 0, ',', '.') }}
                            </span>
                            <div class="text-xs text-gray-500">
                                @if($activeDiscount->type === 'percentage')
                                    Diskon {{ $activeDiscount->value }}%
                                @else
                                    Potongan Rp{{ number_format($activeDiscount->value, 0, ',', '.') }}
                                @endif
                            </div>
                        </div>
                    @else
                        <span class="text-orange-600 font-bold text-base">
                            Rp{{ number_format($item->price, 0, ',', '.') }}
                        </span>
                    @endif
                    
                    </div>

                    <!-- Tombol -->
                    <div class="mt-auto">
                        @if ($item->stock > 0)
                            <button wire:click="addToCart({{ $item->id }})"
                                wire:loading.attr="disabled"
                                wire:target="addToCart"
                                class="w-full bg-orange-500 text-white py-2 rounded-lg font-semibold text-xs sm:text-sm hover:bg-orange-600 transition">
                                <span wire:loading.remove wire:target="addToCart">+ Masukkan Keranjang</span>
                                <span wire:loading wire:target="addToCart">Menambahkan...</span>
                            </button>
                        @else
                            <button disabled
                                class="w-full bg-gray-300 text-gray-600 py-2 rounded-lg font-semibold text-xs sm:text-sm cursor-not-allowed">
                                Stok Habis
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center bg-gray-100 p-8 rounded-xl shadow text-center">
                <img src="{{ asset('storage/noproduct.png') }}" alt="No products" class="w-28 h-28 mb-4 opacity-60">
                <h3 class="text-base font-semibold text-gray-600 mb-1">Tidak ada produk ditemukan</h3>
                <p class="text-sm text-gray-500">Silakan pilih kategori lain atau ubah pencarian.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $product->links() }}
    </div>
</div>
