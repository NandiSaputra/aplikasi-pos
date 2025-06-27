<?php
namespace App\Livewire;

use App\Models\Categories;
use App\Models\Products;
use Livewire\Component;
use Livewire\WithDispatchesEvents;

class ProductByCategory extends Component
{
  
    public $categories;
    public $selectedCategory = null;
    public $search = ''; // Pastikan ini didefinisikan
    protected $listeners = ['refreshProducts' => '$refresh'];

    public function mount()
    {
        $this->categories = Categories::all();
    }

    public function updateCategory($categoryId)
    {
        // Jika klik ulang kategori yang sama, reset
        if ($this->selectedCategory === $categoryId) {
            $this->selectedCategory = null; // Reset kategori
        } else {
            $this->selectedCategory = $categoryId;
        }
    }

    // Hapus atau pastikan tidak ada metode updatedSearch() yang mereset kategori
    // public function updatedSearch()
    // {
    //     $this->selectedCategory = null; 
    // }
    public function addToCart($productId)
{
    $product = Products::findOrFail($productId);

    if ($product->stock <= 0) {
      
        $this->js('window.dispatchEvent(new CustomEvent("notify", {
            detail: {
                type: "error",
                message: "Stok Produk Habis!"
            }
        }))');
        return;
    }

    $cart = session()->get('cart', []);
    
    if (isset($cart[$productId])) {
        if ($cart[$productId]['quantity'] >= $product->stock) {
            $this->js('window.dispatchEvent(new CustomEvent("notify", {
                detail: {
                    type: "error",
                    message: "Stok Tidak Mencukupi!"
                }
            }))');
            return;
        }
        $cart[$productId]['quantity']++;
    } else {
        $cart[$productId] = [
            'name' => $product->name,
            'price' => $product->price,
            'image' => $product->image,
            'quantity' => 1,
        ];
    }

    session()->put('cart', $cart);
    $this->dispatch('cartUpdated');
    $this->js('window.dispatchEvent(new CustomEvent("notify", {
        detail: {
            type: "success",
            message: "Produk berhasil ditambahkan ke keranjang!"
        }
    }))');
}


    
    public function render()
    {
        $query = Products::with('category');

        // Logika untuk FILTER PENCARIAN
        if (!empty($this->search)) {
            // Pastikan kolom 'name' ada di tabel 'products' dan bisa dicari
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        // Logika untuk FILTER KATEGORI
        if (!is_null($this->selectedCategory)) {
            // Pastikan kolom 'category_id' ada di tabel 'products'
            $query->where('category_id', $this->selectedCategory);
        }

        $product = $query->get();

        return view('livewire.product-bycategory', [
            'product' => $product,
            'categories' => $this->categories,
            'selectedCategory' => $this->selectedCategory
        ]);
    }
}
