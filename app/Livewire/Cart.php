<?php

namespace App\Livewire;

use App\Models\Products;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Str;
use Midtrans\Snap;

class Cart extends Component
{
    public $cart = [];
    public $paymentMethod = null;
    public $paidAmount = 0;
    public $snapToken;

    protected $rules = [
        'paymentMethod' => 'required|in:cash,online',
        'paidAmount' => 'nullable|numeric|min:0',
    ];

    protected $listeners = ['cartUpdated' => 'updateCart'];

    public function mount()
    {
        $this->cart = session()->get('cart', []);
    }

    public function updateCart()
    {
        $this->cart = session()->get('cart', []);
    }

    public function increment($productId)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        }
        session()->put('cart', $cart);
        $this->updateCart();
    }

    public function decrement($productId)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']--;

            if ($cart[$productId]['quantity'] <= 0) {
                unset($cart[$productId]);
            }
        }
        session()->put('cart', $cart);
        $this->updateCart();
    }

    public function placeOrder()
    {
        $cart = session()->get('cart', []);

        if (!$this->paymentMethod) {
            $this->js('window.dispatchEvent(new CustomEvent("notify", {
                detail: {
                    type: "warning",
                    message: "Pilih metode pembayaran terlebih dahulu!"
                }
            }))');
            return;
        }

        if (empty($cart)) {
            $this->js('window.dispatchEvent(new CustomEvent("notify", {
                detail: {
                    type: "error",
                    message: "Keranjang masih kosong."
                }
            }))');
            return;
        }

        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $ppn = $subtotal * 0.10;
        $total = $subtotal + $ppn;

        if ($this->paymentMethod === 'cash' && $this->paidAmount < $total) {
            $this->js('window.dispatchEvent(new CustomEvent("notify", {
                detail: {
                    type: "warning",
                    message: "Uang Tunai Kurang!"
                }
            }))');
            return;
        }

        $changeAmount = $this->paidAmount - $total;

        $transaksi = Transaksi::create([
            'user_id' => Auth::id() ?? 1,
            'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
            'total_price' => $total,
            'paid_amount' => $this->paymentMethod === 'cash' ? $this->paidAmount : 0,
            'change_amount' => $this->paymentMethod === 'cash' ? $changeAmount : 0,
            'payment_method' => $this->paymentMethod,
            'payment_status' => $this->paymentMethod === 'cash' ? 'success' : 'cancelled',
        ]);

        foreach ($cart as $productId => $item) {
            TransaksiDetail::create([
                'transaction_id' => $transaksi->id,
                'product_id' => $productId,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['price'] * $item['quantity'],
            ]);
        
            // ❗ Kurangi stok hanya jika cash
            if ($this->paymentMethod === 'cash') {
                $product = Products::find($productId);
                if ($product) {
                    $product->decrement('stock', $item['quantity']);
                }
            }
        }
        

        // Reset keranjang
        session()->forget('cart');
        $this->cart = [];
        $this->paidAmount = 0;
        $this->dispatch('cartUpdated');
        $this->dispatch('refreshProducts');

     //pembayaran online 
     if ($this->paymentMethod === 'online') {
        // Konfigurasi Midtrans
        \Midtrans\Config::$serverKey = config('midtrans.serverKey');
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;
    
        $params = [
            'transaction_details' => [
                'order_id' => $transaksi->invoice_number,
                'gross_amount' => $total,
            ],
            'customer_details' => [
                'first_name' => Auth::user()?->name ?? 'Guest',
                'email' => Auth::user()?->email ?? 'guest@example.com',
            ]
        ];
    
        $snapToken = Snap::getSnapToken($params);
    
        $this->js('window.dispatchEvent(new CustomEvent("midtrans:open", {
            detail: {
                snapToken: "' . $snapToken . '"
            }
        }))');
    
        return;
    }

        // Notifikasi hanya untuk pembayaran tunai (karena online akan redirect)
        $this->js('window.dispatchEvent(new CustomEvent("notify", {
            detail: {
                type: "success",
                message: "Transaksi berhasil disimpan!"
            }
        }))');
    }

    public function resetCart()
    {
        session()->forget('cart');
        $this->cart = [];
        $this->paidAmount = 0;
        $this->dispatch('cartUpdated');
    }

    public function render()
    {
        return view('livewire.cart');
    }
}
