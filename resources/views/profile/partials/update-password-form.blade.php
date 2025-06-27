<form method="POST" action="{{ route('password.update') }}" class="space-y-4">
    @csrf
    @method('PUT')

    <div>
        <label for="current_password" class="block text-sm font-medium text-gray-700">Password Saat Ini</label>
        <input id="current_password" name="current_password" type="password"
            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500">
        @error('current_password') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="password" class="block text-sm font-medium text-gray-700">Password Baru</label>
        <input id="password" name="password" type="password"
            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500">
        @error('password') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
        <input id="password_confirmation" name="password_confirmation" type="password"
            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500">
    </div>

    <div class="flex justify-end">
        <button type="submit"
            class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 shadow">
            Ubah Password
        </button>
    </div>
</form>
