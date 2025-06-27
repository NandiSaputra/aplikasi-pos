<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
    <title>Kasir Restoran Modern</title>

    <!-- Tailwind CSS & JS via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Livewire Styles -->
    @livewireStyles

    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom Style -->
    <style>
        html, body {
            height: 100%;
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        @media (max-width: 1023px) {
            .custom-scrollbar {
                overflow-x: auto;
            }
            .custom-scrollbar::-webkit-scrollbar {
                height: 8px;
            }
        }
    </style>
</head>
<body class="h-full">
    <div class="flex h-full w-full">
      
       
      <!-- Sidebar -->
<div class="w-20 bg-white h-screen flex flex-col justify-between items-center py-6 shadow-md border-r border-gray-200">

    <!-- Logo -->
    <div>
        <a href="{{ route('dashboard') }}" class="text-orange-500 text-3xl">
            <i class="fas fa-utensils"></i>
        </a>
    </div>

    <!-- Menu Tengah -->
    <nav class="flex flex-col items-center space-y-6 mt-10">
        <livewire:sidebar />
    </nav>

    <!-- Menu Bawah -->
    <div class="flex flex-col items-center space-y-6">

        @php
            $isProfileActive = Route::currentRouteName() === 'profile.edit';
        @endphp

        <!-- Profile -->
        <a href="{{ route('profile.edit') }}"
           class="p-3 rounded-xl transition-all duration-200
                  {{ $isProfileActive ? 'bg-orange-100 text-orange-600 shadow' : 'text-gray-400 hover:text-orange-600' }}"
           title="Pengaturan Akun">
            <i class="fas fa-user-cog text-xl"></i>
        </a>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="p-3 rounded-xl text-gray-400 hover:text-red-600 transition-all duration-200"
                title="Keluar">
                <i class="fas fa-sign-out-alt text-xl"></i>
            </button>
        </form>
    </div>
</div>



        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto custom-scrollbar p-6 bg-gray-50">
            {{ $slot }}
        </div>
    </div>

    <!-- Livewire Scripts -->
    @livewireScripts

    <!-- SweetAlert Notifikasi -->
    <script>
        window.addEventListener('notify', event => {
            const { type, message } = event.detail;
            Swal.fire({
                icon: type ?? 'info',
            title: message ?? 'Notifikasi',
                toast: true,
                position: 'top-end',
                timer: 3000,
                showConfirmButton: false,
            });
        });
    </script>
   
  <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.clientKey') }}"></script>
  <script>
      window.addEventListener('open-snap', function(event) {
          const snapToken = event.detail.snapToken;
  
          window.snap.pay(snapToken, {
              onSuccess: function(result) {
                  window.dispatchEvent(new CustomEvent('notify', {
                      detail: {
                          type: 'success',
                          message: 'Pembayaran berhasil! Silakan tunggu konfirmasi.'
                      }
                  }));
                  // Optional: Refresh halaman atau redirect
                  location.reload();
              },
              onPending: function(result) {
                  console.log('Pending:', result);
              },
              onError: function(result) {
                  window.dispatchEvent(new CustomEvent('notify', {
                      detail: {
                          type: 'error',
                          message: 'Pembayaran gagal. Coba lagi.'
                      }
                  }));
              },
              onClose: function() {
                  console.log('Popup ditutup tanpa menyelesaikan pembayaran');
              }
          });
      });
  </script>
   @stack('scripts')
</body>
</html>
