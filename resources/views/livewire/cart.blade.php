<div
    class="w-full lg:w-96 bg-gray-50 p-8 flex flex-col justify-between rounded-bl-3xl lg:rounded-bl-none rounded-br-3xl lg:rounded-tr-3xl shadow-md">

    <h2 class="text-3xl font-bold text-gray-800 mb-6">🛒 Order</h2>

    {{-- Daftar Produk di Keranjang --}}
    <div class="flex-1 overflow-y-auto custom-scrollbar mb-6">
        @if (!empty($cart) && count($cart) > 0)
            @foreach ($cart as $id => $item)
                <div class="flex items-center justify-between py-4 border-b border-gray-200">
                    {{-- Gambar Produk --}}
                    <div class="w-16 h-16 flex-shrink-0 rounded overflow-hidden">
                        <img src="{{ asset('storage/' . ($item['image'] ?? 'placeholder.png')) }}"
                             alt="{{ $item['name'] }}"
                             class="w-full h-full object-cover rounded">
                    </div>

                    {{-- Nama dan Harga --}}
                    <div class="flex-1 ml-4">
                        <h3 class="font-semibold text-gray-900 text-sm">{{ $item['name'] }}</h3>
                        <p class="text-sm text-gray-600">Rp{{ number_format($item['price'], 0, ',', '.') }}</p>
                    </div>

                    {{-- Tombol Qty --}}
                    <div class="flex items-center space-x-2">
                        @if ($item['quantity'] <= 1)
                            <button wire:click="decrement({{ $id }})"
                                    class="w-8 h-8 flex items-center justify-center rounded bg-red-100 text-red-600 hover:bg-red-200 transition"
                                    title="Hapus Produk">
                                🗑
                            </button>
                        @else
                            <button wire:click="decrement({{ $id }})"
                                    class="w-8 h-8 flex items-center justify-center rounded bg-gray-200 text-gray-700 hover:bg-gray-300 transition"
                                    title="Kurangi Jumlah">
                                -
                            </button>
                        @endif

                        <span class="mx-1 font-semibold">{{ $item['quantity'] }}</span>

                        <button wire:click="increment({{ $id }})"
                                class="w-8 h-8 flex items-center justify-center rounded bg-orange-500 text-white hover:bg-orange-600 transition"
                                title="Tambah Jumlah">
                            +
                        </button>
                    </div>
                </div>
            @endforeach

            {{-- Reset Keranjang --}}
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

    {{-- Hitungan Total --}}
    @php
        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $ppn = $subtotal * 0.10;
        $total = $subtotal + $ppn;
        $change = max(0, floatval($paidAmount) - $total);
    @endphp

    <div class="space-y-4 mb-6 text-sm">
        <div class="flex justify-between text-gray-700">
            <span>Subtotal</span>
            <span class="font-semibold">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between text-gray-700">
            <span>PPN (10%)</span>
            <span class="font-semibold">Rp{{ number_format($ppn, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between text-lg font-bold text-gray-900 border-t pt-2 border-gray-300">
            <span>Total</span>
            <span class="text-orange-600">Rp{{ number_format($total, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Pilih Metode Pembayaran --}}
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
        @error('paymentMethod') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        <p class="text-sm text-gray-500 italic mt-2">Metode dipilih: {{ $paymentMethod ?? 'Belum dipilih' }}</p>
    </div>

    {{-- Input Uang Tunai --}}
    @if ($paymentMethod === 'cash')
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Jumlah Bayar (Rp)</label>
            <input type="number" wire:model.live="paidAmount" min="0"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:outline-none">
            @error('paidAmount') <span class="text-red-500 text-xs mt-1">{{$message}}</span> @enderror
        </div>
    @endif

    {{-- Ringkasan Pembayaran --}}
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
                @if ($paidAmount !== '' && $paymentMethod === 'cash')
                    Rp{{ number_format($change, 0, ',', '.') }}
                @else
                    Rp0
                @endif
            </span>
        </div>
        <div wire:loading wire:target="paidAmount" class="text-xs text-gray-400 italic">
            Menghitung kembalian...
        </div>
    </div>

    {{-- Tombol Submit --}}
    <form wire:submit.prevent="placeOrder">
        <button type="submit"
                class="w-full py-4 rounded-xl bg-orange-500 text-white text-xl font-bold shadow-lg hover:bg-orange-600 transition"
                wire:loading.attr="disabled"
                wire:target="placeOrder">
            <span wire:loading.remove wire:target="placeOrder">PLACE ORDER</span>
            <span wire:loading wire:target="placeOrder">Processing...</span>
        </button>
    </form>

    {{-- Midtrans JS --}}
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.clientKey') }}"></script>
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
                onError: function (result) {
                    alert("Terjadi kesalahan saat memuat pembayaran.");
                },
                onClose: function () {
                    alert("Pembayaran dibatalkan.");
                    window.location.href = "/transaksi";
                }
            });
        });
    </script>
</div>
