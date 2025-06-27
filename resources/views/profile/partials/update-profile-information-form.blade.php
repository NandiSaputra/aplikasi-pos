<form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
    @csrf
    @method('PATCH')

    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
        <input id="name" name="name" type="text" value="{{ old('name', Auth::user()->name) }}"
            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500">
        @error('name') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', Auth::user()->email) }}"
            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500">
        @error('email') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
    </div>

    <div class="flex justify-end">
        <button type="submit"
            class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 shadow">
            Simpan Perubahan
        </button>
    </div>
</form>
