<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Edit Siswa') }}
        </h2>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-[#1a6341] relative">
        <div class="p-6 text-gray-900">
            <div class="flex items-center mb-6 bg-gray-100 p-4 rounded-t-lg border-b-2 border-yellow-400">
                <a href="{{ route('admin.students.index') }}" class="text-[#1a6341] hover:text-[#238054] mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h3 class="text-lg font-bold text-gray-800">Edit Data Siswa: {{ $student->user->full_name ?? '' }}</h3>
            </div>

            <form method="POST" action="{{ route('admin.students.update', $student) }}" enctype="multipart/form-data" class="max-w-2xl mx-auto space-y-5">
                @csrf @method('PUT')

                {{-- Nama Lengkap & Foto --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $student->user->full_name) }}" required
                               class="w-full bg-gray-50 border border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-lg shadow-sm px-3 py-2 text-gray-900 placeholder-gray-400 transition-colors">
                        @error('full_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="profile_photo" class="block text-sm font-medium text-gray-700 mb-1">Foto Profil (Opsional)</label>
                        <div class="flex items-center gap-3">
                            <img src="{{ $student->user->profile_photo_url }}" alt="Profile" class="w-10 h-10 rounded-full object-cover border border-gray-200 shadow-sm">
                            <input type="file" id="profile_photo" name="profile_photo" accept="image/*"
                                   class="w-full bg-gray-50 border border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-lg shadow-sm px-3 py-1.5 text-gray-900 transition-colors file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#1a6341] file:text-white hover:file:bg-[#238054]">
                        </div>
                        @error('profile_photo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        <p class="text-[11px] text-gray-400 mt-1">Abaikan jika tidak ingin mengubah foto.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $student->user->email) }}"
                               class="w-full bg-gray-50 border border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-lg shadow-sm px-3 py-2 text-gray-900 placeholder-gray-400 transition-colors">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                        <select id="gender" name="gender" required
                                class="w-full bg-gray-50 border border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-lg shadow-sm px-3 py-2 text-gray-900 transition-colors">
                            <option value="">Pilih Jenis Kelamin...</option>
                            <option value="L" {{ old('gender', $student->user->gender) == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                            <option value="P" {{ old('gender', $student->user->gender) == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                        </select>
                        @error('gender') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="nisn" class="block text-sm font-medium text-gray-700 mb-1">NISN <span class="text-red-500">*</span></label>
                        <input type="text" id="nisn" name="nisn" value="{{ old('nisn', $student->nisn) }}" required
                               class="w-full bg-gray-50 border border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-lg shadow-sm px-3 py-2 text-gray-900 placeholder-gray-400 transition-colors">
                        @error('nisn') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="enrollment_year" class="block text-sm font-medium text-gray-700 mb-1">Tahun Masuk <span class="text-red-500">*</span></label>
                        <select id="enrollment_year" name="enrollment_year" required
                                class="w-full bg-gray-50 border border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-lg shadow-sm px-3 py-2 text-gray-900 transition-colors">
                            @for($y = date('Y') + 1; $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ old('enrollment_year', $student->enrollment_year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                        @error('enrollment_year') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Password Baru dengan toggle --}}
                <div x-data="{ show: false }">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" id="password" name="password" minlength="8"
                               class="w-full bg-gray-50 border border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-lg shadow-sm px-3 py-2 pr-10 text-gray-900 placeholder-gray-400 transition-colors">
                        <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-400 mt-1">Kosongkan jika tidak ingin mengubah password.</p>
                </div>

                <div class="flex items-center justify-end pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.students.index') }}" class="text-gray-500 hover:text-gray-700 mr-4 text-sm">Batal</a>
                    <button type="submit" class="bg-[#1a6341] hover:bg-[#238054] text-white px-6 py-2.5 rounded-lg shadow-sm transition-colors text-sm font-medium">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
