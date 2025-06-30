<x-app-layout>


    <div class="text-center py-20">
        <h2 class="text-2xl font-bold text-yellow-600">Pembayaran Belum Selesai</h2>
        <p class="mt-2 text-gray-600">Invoice: {{ $invoice }}</p>
        <p class="mt-2">Silakan klik tombol di bawah untuk menyelesaikan pembayaran:</p>
    
        @php
            $trx = \App\Models\Transaksi::where('invoice_number', $invoice)->first();
        @endphp
    
        @if ($trx && $trx->snap_token)
            <button onclick="payWithMidtrans('{{ $trx->snap_token }}')" 
                class="mt-4 px-6 py-3 bg-orange-500 text-white rounded-xl hover:bg-orange-600">
                Lanjutkan Pembayaran
            </button>
        @else
            <p class="text-red-500 mt-4">Transaksi tidak ditemukan.</p>
        @endif
    </div>
    
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('midtrans.clientKey') }}"></script>
    <script>
        function payWithMidtrans(snapToken) {
            window.snap.pay(snapToken, {
                onSuccess: function () {
                    window.location.href = "/transaksi";
                },
                onPending: function () {
                    window.location.href = "/transaksi";
                },
                onError: function () {
                    alert("Gagal menyelesaikan pembayaran.");
                },
                onClose: function () {
                    window.location.href = "/transaksi";
                }
            });
        }
    </script>
  
    </x-app-layout>