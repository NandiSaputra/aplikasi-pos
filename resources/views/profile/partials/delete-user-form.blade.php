<form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini?');">
    @csrf
    @method('DELETE')

    <p class="text-sm text-gray-600 mb-4">
        Menghapus akun akan menghapus semua data Anda secara permanen. Tindakan ini tidak dapat dibatalkan.
    </p>

    <div class="flex justify-end">
        <button type="submit"
            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 shadow">
            Hapus Akun
        </button>
    </div>
</form>
