<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir Restoran Modern</title>

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Livewire Styles -->
    @livewireStyles

    <!-- Font Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        html, body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: #94a3b8;
        }
    </style>
</head>
<body class="h-full">
<div class="flex flex-col lg:flex-row h-full min-h-screen">

    <!-- Sidebar -->
    <aside class="w-full lg:w-20 bg-white border-r border-gray-200 shadow-sm py-6 flex lg:flex-col justify-between items-center px-4 lg:px-0">
        <div>
            <a href="{{ route('dashboard') }}" class="text-orange-500 text-2xl">
                <i class="fas fa-utensils"></i>
            </a>
        </div>

        <nav class="flex lg:flex-col gap-4 mt-4">
        <livewire:sidebar />
        </nav>

        <div class="flex gap-4 lg:flex-col items-center mt-6">
            <a href="{{ route('profile.edit') }}"
               class="p-3 rounded-xl transition-all duration-200
               {{ $current === 'profile.edit' ? 'bg-orange-100 text-orange-600 shadow' : 'text-gray-400 hover:text-orange-600' }}"
               title="Pengaturan Akun">
                <i class="fas fa-user-cog text-xl"></i>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="p-3 rounded-xl text-gray-400 hover:text-red-600 transition-all duration-200"
                        title="Keluar">
                    <i class="fas fa-sign-out-alt text-xl"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col lg:flex-row overflow-hidden">
      {{$slot}}
    </main>
</div>

<!-- Livewire Scripts -->
@livewireScripts

<!-- SweetAlert Notifications -->
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

<!-- Midtrans Snap -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.clientKey') }}"></script>
<script>
    window.addEventListener('open-snap', function (event) {
        const snapToken = event.detail.snapToken;
        window.snap.pay(snapToken, {
            onSuccess: function (result) {
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { type: 'success', message: 'Pembayaran berhasil!' }
                }));
                location.reload();
            },
            onPending: function (result) {
                console.log('Pending:', result);
            },
            onError: function () {
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { type: 'error', message: 'Pembayaran gagal. Coba lagi.' }
                }));
            },
            onClose: function () {
                console.log('Popup ditutup tanpa menyelesaikan pembayaran');
            }
        });
    });
</script>

@stack('scripts')
</body>
</html>
