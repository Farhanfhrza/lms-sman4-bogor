<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Tambah Guru') }}
        </h2>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-[#1a6341] relative">
        <div class="p-6 text-gray-900">
            <div class="flex items-center mb-6 bg-gray-100 p-4 rounded-t-lg border-b-2 border-yellow-400">
                <a href="{{ route('admin.teachers.index') }}" class="text-[#1a6341] hover:text-[#238054] mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h3 class="text-lg font-bold text-gray-800">Formulir Tambah Guru Baru</h3>
            </div>

            <form method="POST" action="{{ route('admin.teachers.store') }}" class="max-w-2xl mx-auto space-y-5">
                @csrf

                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required
                           class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm">
                    @error('full_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="login_id" class="block text-sm font-medium text-gray-700 mb-1">ID Login <span class="text-red-500">*</span></label>
                        <input type="text" id="login_id" name="login_id" value="{{ old('login_id') }}" required
                               class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm">
                        @error('login_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        <p class="text-xs text-gray-400 mt-1">NIP atau identitas login unik.</p>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="nip" class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                        <input type="text" id="nip" name="nip" value="{{ old('nip') }}"
                               class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm">
                        @error('nip') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="specialization" class="block text-sm font-medium text-gray-700 mb-1">Spesialisasi / Mapel</label>
                        <input type="text" id="specialization" name="specialization" value="{{ old('specialization') }}"
                               class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm">
                        @error('specialization') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                    <input type="password" id="password" name="password" required minlength="8"
                           class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-400 mt-1">Minimal 8 karakter.</p>
                </div>

                <div class="flex items-center justify-end pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.teachers.index') }}" class="text-gray-500 hover:text-gray-700 mr-4 text-sm">Batal</a>
                    <button type="submit" class="bg-[#1a6341] hover:bg-[#238054] text-white px-6 py-2.5 rounded-lg shadow-sm transition-colors text-sm font-medium">
                        Simpan Guru
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
