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

            <form method="POST" action="{{ route('admin.students.update', $student) }}" class="max-w-2xl mx-auto space-y-5">
                @csrf @method('PUT')

                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $student->user->full_name) }}" required
                           class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm">
                    @error('full_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $student->user->email) }}"
                           class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="nisn" class="block text-sm font-medium text-gray-700 mb-1">NISN <span class="text-red-500">*</span></label>
                        <input type="text" id="nisn" name="nisn" value="{{ old('nisn', $student->nisn) }}" required
                               class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm">
                        @error('nisn') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="enrollment_year" class="block text-sm font-medium text-gray-700 mb-1">Tahun Masuk <span class="text-red-500">*</span></label>
                        <select id="enrollment_year" name="enrollment_year" required
                                class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm">
                            @for($y = date('Y') + 1; $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ old('enrollment_year', $student->enrollment_year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                        @error('enrollment_year') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                    <input type="password" id="password" name="password" minlength="8"
                           class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm">
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
