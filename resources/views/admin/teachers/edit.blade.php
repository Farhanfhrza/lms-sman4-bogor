<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Edit Guru') }}
        </h2>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-[#1a6341] relative">
        <div class="p-6 text-gray-900">
            <div class="flex items-center mb-6 bg-gray-100 p-4 rounded-t-lg border-b-2 border-yellow-400">
                <a href="{{ route('admin.teachers.index') }}" class="text-[#1a6341] hover:text-[#238054] mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h3 class="text-lg font-bold text-gray-800">Edit Data Guru: {{ $teacher->user->full_name ?? '' }}</h3>
            </div>

            <form method="POST" action="{{ route('admin.teachers.update', $teacher) }}" enctype="multipart/form-data" class="max-w-2xl mx-auto space-y-5">
                @csrf @method('PUT')

                {{-- Nama Lengkap & Foto --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $teacher->user->full_name) }}" required
                               class="w-full bg-gray-50 border border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-lg shadow-sm px-3 py-2 text-gray-900 placeholder-gray-400 transition-colors">
                        @error('full_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="profile_photo" class="block text-sm font-medium text-gray-700 mb-1">Foto Profil (Opsional)</label>
                        <div class="flex items-center gap-3">
                            <img src="{{ $teacher->user->profile_photo_url }}" alt="Profile" class="w-10 h-10 rounded-full object-cover border border-gray-200 shadow-sm">
                            <input type="file" id="profile_photo" name="profile_photo" accept="image/*"
                                   class="w-full bg-gray-50 border border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-lg shadow-sm px-3 py-1.5 text-gray-900 transition-colors file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#1a6341] file:text-white hover:file:bg-[#238054]">
                        </div>
                        @error('profile_photo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        <p class="text-[11px] text-gray-400 mt-1">Abaikan jika tidak ingin mengubah foto.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="login_id" class="block text-sm font-medium text-gray-700 mb-1">ID Login <span class="text-red-500">*</span></label>
                        <input type="text" id="login_id" name="login_id" value="{{ old('login_id', $teacher->user->login_identifier) }}" required
                               class="w-full bg-gray-50 border border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-lg shadow-sm px-3 py-2 text-gray-900 placeholder-gray-400 transition-colors">
                        @error('login_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $teacher->user->email) }}"
                               class="w-full bg-gray-50 border border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-lg shadow-sm px-3 py-2 text-gray-900 placeholder-gray-400 transition-colors">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                        <select id="gender" name="gender" required
                                class="w-full bg-gray-50 border border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-lg shadow-sm px-3 py-2 text-gray-900 transition-colors">
                            <option value="">Pilih...</option>
                            <option value="L" {{ old('gender', $teacher->user->gender) == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                            <option value="P" {{ old('gender', $teacher->user->gender) == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                        </select>
                        @error('gender') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="nip" class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                        <input type="text" id="nip" name="nip" value="{{ old('nip', $teacher->nip) }}"
                               class="w-full bg-gray-50 border border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-lg shadow-sm px-3 py-2 text-gray-900 placeholder-gray-400 transition-colors">
                        @error('nip') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="specialization" class="block text-sm font-medium text-gray-700 mb-1">Spesialisasi / Mapel</label>
                        <input type="text" id="specialization" name="specialization" value="{{ old('specialization', $teacher->specialization) }}"
                               class="w-full bg-gray-50 border border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-lg shadow-sm px-3 py-2 text-gray-900 placeholder-gray-400 transition-colors">
                        @error('specialization') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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

                {{-- Subject Assignment --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Mata Pelajaran Yang Diajar
                        <span class="text-xs text-gray-400 font-normal ml-1">(centang semua mapel yang bisa diajar oleh guru ini)</span>
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2 border border-gray-200 rounded-lg p-3 max-h-52 overflow-y-auto bg-gray-50">
                        @foreach($subjects as $subject)
                        <label class="flex items-center space-x-2 cursor-pointer p-1.5 rounded hover:bg-white transition-colors">
                            <input type="checkbox" name="subject_ids[]" value="{{ $subject->id }}"
                                   {{ in_array($subject->id, $assignedSubjectIds) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-[#1a6341] focus:ring-[#1a6341]">
                            <span class="text-sm text-gray-700">{{ $subject->name }}</span>
                            @if($subject->code)
                            <span class="text-xs text-gray-400">({{ $subject->code }})</span>
                            @endif
                        </label>
                        @endforeach
                    </div>
                    @error('subject_ids') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.teachers.index') }}" class="text-gray-500 hover:text-gray-700 mr-4 text-sm">Batal</a>
                    <button type="submit" class="bg-[#1a6341] hover:bg-[#238054] text-white px-6 py-2.5 rounded-lg shadow-sm transition-colors text-sm font-medium">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
