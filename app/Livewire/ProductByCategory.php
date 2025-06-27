<?php

namespace App\Livewire;

use App\Models\Categories;
use App\Models\Products;
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
        $product = Products::select('id', 'name', 'price', 'image', 'stock')->findOrFail($productId);

        if ($product->stock <= 0) {
            return $this->js('window.dispatchEvent(new CustomEvent("notify", {
                detail: { type: "error", message: "Stok Produk Habis!" }
            }))');
        }

        $cart = session()->get('cart', []);

        // Jika sudah ada produk di keranjang
        if (isset($cart[$productId])) {
            if ($cart[$productId]['quantity'] >= $product->stock) {
                return $this->js('window.dispatchEvent(new CustomEvent("notify", {
                    detail: { type: "error", message: "Stok Tidak Mencukupi!" }
                }))');
            }
            $cart[$productId]['quantity']++;
        } else {
            // Produk baru
            $cart[$productId] = [
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image,
                'quantity' => 1,
            ];
        }

        session()->put('cart', $cart);

        // Update komponen keranjang
        $this->dispatch('cartUpdated');

        // Notifikasi sukses
        $this->js('window.dispatchEvent(new CustomEvent("notify", {
            detail: { type: "success", message: "Produk berhasil ditambahkan ke keranjang!" }
        }))');
    }

    public function render()
    {
        $query = Products::with('category')
            ->select('id', 'name', 'price', 'stock', 'image', 'category_id');

        // Filter berdasarkan pencarian
        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        // Filter berdasarkan kategori
        if (!is_null($this->selectedCategory)) {
            $query->where('category_id', $this->selectedCategory);
        }

        $product = $query->paginate($this->perPage);

        return view('livewire.product-bycategory', [
            'product' => $product,
            'categories' => $this->categories,
            'selectedCategory' => $this->selectedCategory,
        ]);
    }
}
