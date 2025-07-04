<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
    <title>Kasir Restoran Modern</title>

    <!-- Tailwind CSS & JS via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  
 
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
            width: 0px;
            height: 0px;
        }
        .custom-scrollbar {
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE/Edge */
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
    <style>
        [x-cloak] { display: none !important; }
    </style>
    
</head>
<body class="h-full" >

      <!-- Navbar Mobile -->
<header class="lg:hidden flex justify-between items-center px-4 py-3 bg-white border-b shadow">
    <button onclick="toggleSidebar()">
        <i class="fas fa-bars text-xl text-gray-700"></i>
    </button>
    <a href="{{ route('dashboard') }}" class="text-orange-500 text-2xl">
        <i class="fas fa-utensils"></i>
    </a>
</header>

    
    <div class="flex h-full w-full">

       

  <!-- Sidebar -->
  <aside
  id="sidebar"
  class="fixed inset-y-0 left-0 w-64 bg-white border-r border-gray-200 z-30 transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out lg:static lg:w-20 shadow-lg"
>
<!-- Tombol Close (khusus mobile) -->
<button onclick="toggleSidebar()" class="absolute top-4 right-4 lg:hidden text-gray-500 hover:text-red-500">
    <i class="fas fa-times text-xl"></i>
</button>
  <div class="h-full flex flex-col justify-between items-center py-6">
      <!-- Logo -->
      <a href="{{ route('dashboard') }}" class="text-orange-500 text-3xl">
          <i class="fas fa-utensils"></i>
      </a>

      <!-- Menu Tengah -->
      <nav class="flex flex-col items-center space-y-6 mt-10">
          <livewire:sidebar />
      </nav>

      <!-- Menu Bawah -->
      <div class="flex flex-col items-center space-y-6">
          @php
              $isProfileActive = Route::currentRouteName() === 'profile.edit';
          @endphp

          <a href="{{ route('profile.edit') }}"
             class="p-3 rounded-xl {{ $isProfileActive ? 'bg-orange-100 text-orange-600 shadow' : 'text-gray-400 hover:text-orange-600' }}"
             title="Pengaturan Akun">
              <i class="fas fa-user-cog text-xl"></i>
          </a>

          <form method="POST" action="{{ route('logout') }}" id="logoutForm">
            @csrf
            <button type="button" onclick="confirmLogout()" class="p-3 rounded-xl text-gray-400 hover:text-red-600 transition" title="Keluar">
                <i class="fas fa-sign-out-alt text-xl"></i>
            </button>
        </form>
        
      </div>
  </div>
</aside>


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
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('-translate-x-full');
    }
</script>
<script>
    function confirmLogout() {
        Swal.fire({
            title: 'Yakin ingin logout?',
            text: 'Sesi Anda akan diakhiri.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e53e3e',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Ya, logout',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logoutForm').submit();
            }
        });
    }
</script>
@if (session('welcome_message'))

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: '{{ session('welcome_message') }}',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
            });
        });
    </script>
@endif

   @stack('scripts')
</body>
</html>
