<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Loading Pembayaran...</h1>

    {{-- Snap JS --}}
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.clientKey') }}"></script>

    <script type="text/javascript">
        window.snap.pay("{{ $snapToken }}", {
            onSuccess: function(result) {
                console.log("Pembayaran sukses:", result);
                window.location.href = "/dashboard"; // ganti jika perlu
            },
          
            onError: function(result) {
                console.error("Gagal:", result);
                alert("Terjadi kesalahan saat memuat pembayaran.");
            },
            onClose: function() {
                alert("Pembayaran dibatalkan.");
                window.location.href = "/dashboard"; // redirect ke dashboard
            }
        });
    </script>
</body>
</html>
