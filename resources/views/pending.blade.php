<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran Pending</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h2>Pembayaran Tertunda</h2>
    <p>Invoice: <strong>{{ $invoice }}</strong></p>
    <p>Status: Menunggu pembayaran.</p>

    <button id="pay-button">🔁 Bayar Sekarang</button>

    <form method="POST" action="{{ route('payment.cancel') }}" style="margin-top: 20px;">
        @csrf
        <input type="hidden" name="invoice" value="{{ $invoice }}">
        <button type="submit" onclick="return confirm('Yakin ingin membatalkan transaksi ini?')">❌ Batalkan Pembayaran</button>
    </form>

    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.clientKey') }}"></script>
    <script>
        document.getElementById('pay-button').addEventListener('click', function () {
            window.snap.pay("{{ $snapToken }}", {
                onSuccess: function(result) {
                    console.log('success', result);
                    window.location.href = '/dashboard';
                },
                onPending: function(result) {
                    console.log('pending', result);
                    alert('Menunggu pembayaran diselesaikan.');
                },
                onError: function(result) {
                    console.error(result);
                    alert("Terjadi kesalahan saat proses pembayaran.");
                },
                onClose: function() {
                    alert("Anda menutup pembayaran tanpa menyelesaikannya.");
                }
            });
        });
    </script>
</body>
</html>
