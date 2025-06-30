<x-app-layout><div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
 <div class="bg-white p-8 rounded-xl shadow-md text-center max-w-lg w-full">
     <h1 class="text-2xl font-bold text-red-600 mb-4">Transaksi Belum Selesai 😕</h1>
     <p class="text-gray-700 mb-6">Kamu belum menyelesaikan pembayaran. Silakan coba lagi atau pilih metode pembayaran lain.</p>
     <a href="{{ route('kasir') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg transition">
         🔄 Kembali ke Kasir
     </a>
 </div>
</div></x-app-layout> {{-- atau layouts lain sesuai kasirmu --}}


