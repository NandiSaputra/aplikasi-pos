<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profil Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-6 px-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Sidebar Info User -->
            <div class="bg-white shadow rounded-xl p-6 text-center">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}"
                    class="w-24 h-24 rounded-full mx-auto border-4 border-orange-500 mb-4" alt="Avatar">
                <h4 class="text-lg font-semibold text-gray-800">{{ Auth::user()->name }}</h4>
                <p class="text-sm text-gray-500 mb-2">{{ Auth::user()->email }}</p>
                <span class="text-xs bg-orange-100 text-orange-700 px-3 py-1 rounded-full">
                    Akun Terverifikasi
                </span>
            </div>

            <!-- Form Edit Profile + Password -->
            <div class="md:col-span-2 space-y-6">
                <!-- Update Profile -->
                <div class="bg-white p-6 rounded-xl shadow">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Perbarui Informasi Profil</h3>
                    @include('profile.partials.update-profile-information-form')
                </div>

                <!-- Update Password -->
                <div class="bg-white p-6 rounded-xl shadow">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Ubah Kata Sandi</h3>
                    @include('profile.partials.update-password-form')
                </div>

                <!-- Delete Account -->
                <div class="bg-white p-6 rounded-xl shadow">
                    <h3 class="text-lg font-bold text-red-600 mb-4">Hapus Akun</h3>
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
