<?php

namespace App\Livewire;

use App\Models\Coupon;
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
    public $couponCode = '';
    public $discountAmount = 0;
    public $appliedCoupon = null;

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
            $this->dispatchNotify('warning', 'Pilih metode pembayaran terlebih dahulu!');
            return;
        }

        if (empty($cart)) {
            $this->dispatchNotify('error', 'Keranjang masih kosong.');
            return;
        }

        // Validasi stok
        foreach ($cart as $productId => $item) {
            $product = Products::find($productId);
            if (!$product || $item['quantity'] > $product->stock) {
                $this->dispatchNotify('error', "Stok untuk produk {$item['name']} hanya tersedia {$product->stock} item.");
                return;
            }
        }

       $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

$total = max(0, $subtotal - $this->discountAmount);

        if ($total < 0) $total = 0;

        if ($this->paymentMethod === 'cash' && $this->paidAmount < $total) {
            $this->dispatchNotify('warning', 'Uang Tunai Kurang!');
            return;
        }

        $changeAmount = $this->paidAmount - $total;

        $transaksi = Transaksi::create([
            'user_id'               => Auth::id() ?? 1,
            'invoice_number'        => 'INV-' . strtoupper(Str::random(8)),
            'total_price'           => $total, // Sudah termasuk diskon produk dan kupon
            'paid_amount'           => $this->paymentMethod === 'cash' ? $this->paidAmount : 0,
            'change_amount'         => $this->paymentMethod === 'cash' ? $changeAmount : 0,
            'payment_method'        => $this->paymentMethod,
            'payment_status'        => $this->paymentMethod === 'cash' ? 'success' : 'pending',
            'coupon_code'           => $this->appliedCoupon?->code,
            'discount_amount'       => $this->discountAmount ?? 0,
           'product_discount_total' => collect($cart)->sum(fn($item) => ($item['discount'] ?? 0) * $item['quantity']),
        ]);
        

        if ($this->appliedCoupon) {
            $this->appliedCoupon->increment('used_count');
        }

        foreach ($cart as $productId => $item) {
            $product = Products::find($productId);
            if (!$product) continue;
        
            $quantity = $item['quantity'];
            $originalPrice = $item['original_price'] ?? $product->price; // Harga sebelum diskon
            $discountPerItem = $item['discount'] ?? 0;
        
            $priceAfterDiscount = $originalPrice - $discountPerItem;
            $subtotal = $priceAfterDiscount * $quantity;
        
            TransaksiDetail::create([
                'transaction_id' => $transaksi->id,
                'product_id'     => $productId,
                'quantity'       => $quantity,
                'price'          => $originalPrice,        // Simpan harga asli
                'buy_price'      => $product->buy_price,
                'discount'       => $discountPerItem,      // Diskon per item
                'subtotal'       => $subtotal,             // Harga setelah diskon × qty
            ]);
        


            if ($this->paymentMethod === 'cash') {
                $product->decrement('stock', $item['quantity']);
            }
        }

        session()->forget('cart');
        $this->cart = [];
        $this->paidAmount = 0;
        $this->dispatch('cartUpdated');
        $this->dispatch('refreshProducts');
        $this->dispatch('toggle-cart');

        if ($this->paymentMethod === 'online') {
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

            if ($transaksi->snap_token && $transaksi->payment_status === 'pending') {
                $snapToken = $transaksi->snap_token;
            } else {
                $snapToken = Snap::getSnapToken($params);
                $transaksi->update([
                    'payment_status' => 'pending',
                    'snap_token' => $snapToken,
                ]);
            }

            $this->js('window.dispatchEvent(new CustomEvent("midtrans:open", {
                detail: { snapToken: "' . $snapToken . '" }
            }))');

            return;
        }

        $this->js(<<<JS
            Swal.fire({
                icon: 'success',
                title: 'Transaksi berhasil!',
                text: 'Pembayaran tunai diterima.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = "/transaksi";
            });
        JS);
    }

    public function resetCart()
    {
        session()->forget('cart');
        $this->cart = [];
        $this->paidAmount = 0;
        $this->dispatch('cartUpdated');
    }

    public function applyCoupon()
    {
        $code = strtoupper(trim($this->couponCode));
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon || !$coupon->isValid()) {
            $this->discountAmount = 0;
            $this->appliedCoupon = null;
            $this->dispatchNotify('error', 'Kupon tidak valid atau telah kadaluarsa.');
            return;
        }

        $subtotal = collect($this->cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        if ($coupon->min_purchase && $subtotal < $coupon->min_purchase) {
            $this->dispatchNotify('warning', 'Minimal belanja Rp' . number_format($coupon->min_purchase) . ' untuk kupon ini.');
            return;
        }

        $this->discountAmount = $coupon->calculateDiscount($subtotal);
        $this->appliedCoupon = $coupon;
        $this->dispatchNotify('success', 'Kupon berhasil diterapkan!');
    }

    public function resetCoupon()
    {
        $this->couponCode = '';
        $this->discountAmount = 0;
        $this->appliedCoupon = null;
        $this->dispatchNotify('info', 'Kupon dibatalkan.');
    }

    public function dispatchNotify($type, $message)
    {
        $this->js("window.dispatchEvent(new CustomEvent('notify', {
            detail: { type: '{$type}', message: '{$message}' }
        }))");
    }

    public function render()
    {
        return view('livewire.cart');
    }
}
