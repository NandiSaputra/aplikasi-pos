<div>

{{-- CART WRAPPER --}}
<div id="cartPanel"
     class="hidden lg:block cart-panel w-full lg:w-96 bg-white shadow-md border-t lg:border-none
        rounded-t-2xl lg:rounded-none fixed bottom-0 left-0 right-0 z-40
        lg:sticky lg:top-0 max-h-[90dvh] lg:max-h-[100dvh] overflow-y-auto
        p-6 custom-scrollbar transition-transform duration-300 transform lg:translate-y-0">

    
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">🛒 Order</h2>

    {{-- DAFTAR PRODUK CART --}}
    <div class="mb-6 {{ count($cart) > 4 ? 'max-h-[400px] overflow-y-auto custom-scrollbar' : '' }}">
        @if (!empty($cart) && count($cart) > 0)
            @foreach ($cart as $id => $item)
                <div class="flex items-center justify-between py-4 border-b border-gray-200">
                    <div class="w-16 h-16 rounded overflow-hidden flex-shrink-0">
                        <img src="{{ asset('storage/' . ($item['image'] ?? 'placeholder.png')) }}"
                             alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                    </div>
                    
                    <div class="flex-1 ml-4">
                        <h3 class="font-semibold text-gray-900 text-sm">{{ $item['name'] }}</h3>
                        <p class="text-sm text-gray-600">Rp{{ number_format($item['price'], 0, ',', '.') }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if ($item['quantity'] <= 1)
                            <button wire:click="decrement({{ $id }})"
                                    class="w-8 h-8 flex items-center justify-center rounded bg-red-100 text-red-600 hover:bg-red-200 transition">🗑</button>
                        @else
                            <button wire:click="decrement({{ $id }})"
                                    class="w-8 h-8 flex items-center justify-center rounded bg-gray-200 text-gray-700 hover:bg-gray-300 transition">-</button>
                        @endif
                        <span class="font-semibold">{{ $item['quantity'] }}</span>
                        <button wire:click="increment({{ $id }})"
                                class="w-8 h-8 flex items-center justify-center rounded bg-orange-500 text-white hover:bg-orange-600 transition">+</button>
                    </div>
                </div>
            @endforeach

            <div class="mt-4 text-right">
                <button wire:click="resetCart"
                        class="bg-red-100 hover:bg-red-200 text-red-700 text-sm font-semibold px-4 py-2 rounded-lg transition shadow">
                    🔄 Reset Keranjang
                </button>
            </div>
        @else
            <p class="text-gray-500 text-center">Keranjang kosong.</p>
        @endif
    </div>

    {{-- TOTAL & PEMBAYARAN --}}
    @php
        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $ppn = $subtotal * 0.10;
        $totalBeforeDiscount = $subtotal + $ppn;
        $total = max(0, $totalBeforeDiscount - $discountAmount);
        $change = $paymentMethod === 'cash' ? max(0, floatval($paidAmount) - $total) : 0;
    @endphp

    <div class="space-y-3 mb-6 text-sm">
        <div class="flex justify-between text-gray-700">
            <span>Subtotal</span>
            <span class="font-semibold">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between text-gray-700">
            <span>PPN (10%)</span>
            <span class="font-semibold">Rp{{ number_format($ppn, 0, ',', '.') }}</span>
        </div>
        @if ($discountAmount > 0)
            <div class="flex justify-between text-green-600 font-semibold">
                <span>Diskon</span>
                <span>- Rp{{ number_format($discountAmount, 0, ',', '.') }}</span>
            </div>
        @endif
        <div class="flex justify-between text-lg font-bold text-gray-900 border-t pt-2 border-gray-300">
            <span>Total</span>
            <span class="text-orange-600">Rp{{ number_format($total, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- KUPON --}}
    <div class="mb-4">
        <label for="coupon" class="block text-sm font-medium text-gray-700">Kode Kupon</label>
        <div class="flex mt-1">
            <input type="text" wire:model.lazy="couponCode"
                   placeholder="Masukkan kode kupon"
                   class="flex-1 rounded-l-lg border-gray-300 focus:ring-orange-500 focus:border-orange-500">
            <button wire:click="applyCoupon"
                    class="px-4 bg-orange-500 text-white rounded-r-lg hover:bg-orange-600">
                Gunakan
            </button>
        </div>
        @if ($appliedCoupon)
            <div class="flex justify-between items-center mt-2">
                <p class="text-green-600 text-sm">
                    Kupon <strong>{{ $appliedCoupon->code }}</strong> aktif. Diskon Rp{{ number_format($discountAmount, 0, ',', '.') }}
                </p>
                <button wire:click="resetCoupon" class="text-sm text-red-500 hover:underline">Batalkan</button>
            </div>
        @endif
    </div>

    {{-- METODE PEMBAYARAN --}}
    <div class="mb-4">
        <label class="block text-gray-700 font-medium mb-2">Metode Pembayaran</label>
        <div class="space-x-4">
            <label class="inline-flex items-center cursor-pointer">
                <input type="radio" name="payment_method" value="cash"
                       wire:model.live="paymentMethod"
                       class="form-radio text-orange-500">
                <span class="ml-2">Tunai</span>
            </label>
            <label class="inline-flex items-center cursor-pointer">
                <input type="radio" name="payment_method" value="online"
                       wire:model.live="paymentMethod"
                       class="form-radio text-orange-500">
                <span class="ml-2">Online</span>
            </label>
        </div>
        @error('paymentMethod') <span class="text-red-500 text-xs mt-1">{{$message}}</span> @enderror
    </div>

    {{-- INPUT BAYAR --}}
    @if ($paymentMethod === 'cash')
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Jumlah Bayar (Rp)</label>
            <input type="number"
                   wire:model.live="paidAmount"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:outline-none" />
            @error('paidAmount') <span class="text-red-500 text-xs mt-1">{{$message}}</span> @enderror
        </div>
    @endif

    {{-- KEMBALIAN --}}
    <div class="mb-4 space-y-2 text-sm">
        <div class="flex justify-between">
            <span class="text-gray-700">Dibayar</span>
            <span class="font-semibold text-gray-800">
                Rp{{ number_format(floatval($paidAmount), 0, ',', '.') }}
            </span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-700">Kembalian</span>
            <span class="font-semibold text-gray-800">
                Rp{{ number_format($change, 0, ',', '.') }}
            </span>
        </div>
    </div>

    {{-- SUBMIT --}}
    <form wire:submit.prevent="placeOrder">
        <button type="submit"
                class="w-full py-4 rounded-xl bg-orange-500 text-white text-lg font-bold shadow hover:bg-orange-600 transition">
            PLACE ORDER
        </button>
    </form>

    {{-- MIDTRANS --}}
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('midtrans.clientKey') }}"></script>
    <script>
        window.addEventListener('midtrans:open', function (event) {
            const snapToken = event.detail.snapToken;
            window.snap.pay(snapToken, {
                onSuccess: function (result) {
                    window.location.href = "/transaksi";
                },
                onPending: function (result) {
                    window.location.href = "/pending?invoice=" + result.order_id;
                },
                onError: function () {
                    alert("Terjadi kesalahan saat memuat pembayaran.");
                },
                onClose: function () {
                    window.location.href = "/midtrans/unfinish";
                }
            });
        });
    </script>
</div>

{{-- TOMBOL KERANJANG (MOBILE) --}}
<div class="lg:hidden fixed bottom-6 right-6 z-50">
    <button onclick="toggleCartPanel()"
            class="bg-orange-500 text-white w-14 h-14 rounded-full shadow-lg flex items-center justify-center text-2xl hover:bg-orange-600 transition"
            title="Buka/Tutup Keranjang">
        🛒
    </button>
</div>


{{-- SCRIPT TOGGLE --}}
<script>
    function toggleCartPanel() {
        const cart = document.getElementById('cartPanel');
        if (cart.classList.contains('hidden')) {
            cart.classList.remove('hidden');
            cart.classList.remove('translate-y-full');
        } else {
            cart.classList.add('translate-y-full');
            setTimeout(() => {
                cart.classList.add('hidden');
            }, 300); // tunggu animasi selesai
        }
    }
</script>

</div>