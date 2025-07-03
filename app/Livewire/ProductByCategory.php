<?php

namespace App\Livewire;

use App\Models\Categories;
use App\Models\Products;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ProductByCategory extends Component
{
    use WithPagination;

    public $categories;
    public $selectedCategory = null;
    public $search = '';
    public $perPage = 9;

    protected $paginationTheme = 'tailwind';

    // Untuk me-refresh list produk dari komponen luar
    protected $listeners = ['refreshProducts' => '$refresh'];
    public $hasLowStockProducts = false;
    public $lowStockCount = 0;
    public $outOfStockCount = 0;
    public $hasOutOfStockProducts = false;
    public $bestSellerIds = [];
    public $sortBy = 'default'; // pilihan: default, terbaru, terlaris


    public function mount()
    {
        // Ambil semua kategori di awal
        $this->categories = Categories::all();
    }

    // Reset pagination saat search berubah
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Reset pagination saat filter kategori berubah
    public function updatingSelectedCategory()
    {
        $this->resetPage();
    }

    // Klik kategori untuk filter
    public function updateCategory($categoryId)
    {
        // Toggle kategori
        $this->selectedCategory = ($this->selectedCategory === $categoryId) ? null : $categoryId;
    }

    // Tambah ke keranjang
    public function addToCart($productId)
    {
        $product = Products::select('id', 'name', 'price', 'image', 'stock', 'discount')->findOrFail($productId);
    
        if ($product->stock <= 0) {
            return $this->js('window.dispatchEvent(new CustomEvent("notify", {
                detail: { type: "error", message: "Stok Produk Habis!" }
            }))');
        }
    
        $cart = session()->get('cart', []);
    
        if (isset($cart[$productId])) {
            if ($cart[$productId]['quantity'] >= $product->stock) {
                return $this->js('window.dispatchEvent(new CustomEvent("notify", {
                    detail: { type: "error", message: "Stok Tidak Mencukupi!" }
                }))');
            }
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                'name' => $product->name,
                'price' => $product->discounted_price, // Gunakan harga diskon
                'image' => $product->image,
                'quantity' => 1,
            ];
        }
    
        session()->put('cart', $cart);
    
        $this->dispatch('cartUpdated');
    
        $this->js('window.dispatchEvent(new CustomEvent("notify", {
            detail: { type: "success", message: "Produk berhasil ditambahkan ke keranjang!" }
        }))');
    }
    

    public function render()
    {
        $query = Products::with('category')
            ->select('id', 'name', 'price', 'stock', 'image', 'category_id', 'discount', 'created_at');
    
        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }
    
        if (!is_null($this->selectedCategory)) {
            $query->where('category_id', $this->selectedCategory);
        }
    
        // Ambil produk terlaris
        $this->bestSellerIds = DB::table('transaction_details')
            ->select('product_id')
            ->groupBy('product_id')
            ->orderByRaw('SUM(quantity) DESC')
            ->pluck('product_id')
            ->toArray();
    
        // Ambil semua produk untuk sorting manual
        $allProducts = $query->get();
    
        // Sorting manual berdasarkan pilihan
        $sorted = match ($this->sortBy) {
            'terbaru' => $allProducts->sortByDesc('created_at'),
            'terlaris' => $allProducts->sortBy(function ($item) {
                $index = array_search($item->id, $this->bestSellerIds);
                return $index !== false ? $index : PHP_INT_MAX;
            }),
            'stok_habis' => $allProducts->sortBy(function ($item) {
                return $item->stock <= 0 ? 1 : 0; // stok habis di akhir
            }),
            default => $allProducts->sortByDesc('stock') // default: stok terbanyak dulu
        };
    
        // Pagination manual
        $currentPage = $this->page ?? 1;
        $perPage = $this->perPage;
        $paged = $sorted->values()->forPage($currentPage, $perPage);
    
        $product = new \Illuminate\Pagination\LengthAwarePaginator(
            $paged,
            $sorted->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    
        // Hitung status stok
        $this->lowStockCount = $allProducts->filter(fn($item) => $item->stock <= 10 && $item->stock > 0)->count();
        $this->hasLowStockProducts = $this->lowStockCount > 0;
        $this->outOfStockCount = $allProducts->filter(fn($item) => $item->stock <= 0)->count();
        $this->hasOutOfStockProducts = $this->outOfStockCount > 0;
    
        return view('livewire.product-bycategory', [
            'product' => $product,
            'categories' => $this->categories,
            'selectedCategory' => $this->selectedCategory,
            'bestSellerIds' => $this->bestSellerIds,
        ]);
    }
    
    
    
    
}
