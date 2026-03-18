<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Data Kelas') }}
        </h2>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-[#1a6341] relative">
        <div class="p-6 text-gray-900" x-data="{ showAddModal: false, showEditModal: false, editData: { id: '', name: '', grade: '', major: '', teacher_id: '' } }">

            <div class="flex flex-col md:flex-row justify-between items-center mb-6 bg-gray-100 p-4 rounded-t-lg border-b-2 border-yellow-400">
                <div class="flex items-center mb-3 md:mb-0">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-[#1a6341]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Daftar Rombel Kelas
                    </h3>
                    @if($academicYear)
                        <span class="ml-3 bg-[#1a6341] text-white px-3 py-1 rounded-full text-xs font-medium border border-green-700 shadow-sm">
                            TA: {{ $academicYear->name }}
                        </span>
                    @else
                        <span class="ml-3 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-medium shadow-sm">
                            ⚠ TA Belum Aktif
                        </span>
                    @endif
                </div>

                <button @click="showAddModal = true" @if(!$academicYear) disabled @endif class="bg-[#1a6341] hover:bg-[#238054] text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm flex items-center disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Kelas
                </button>
            </div>

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-50 border border-green-300 text-green-800 rounded-lg text-sm flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-3 bg-red-50 border border-red-300 text-red-800 rounded-lg text-sm flex items-center">
                    <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-300 text-red-800 rounded-lg text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                    </ul>
                </div>
            @endif

            @if(!$academicYear)
            <div class="text-center py-16 text-gray-500">
                <svg class="w-16 h-16 mx-auto mb-4 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <p class="text-lg font-semibold text-red-600">Tidak ada Tahun Ajaran Aktif</p>
                <p class="text-sm mt-1">Silakan aktifkan Tahun Ajaran di pengaturan terlebih dahulu untuk mulai mengelola kelas.</p>
                <a href="{{ route('admin.academic-years.index') }}" class="mt-4 inline-block bg-[#1a6341] text-white px-4 py-2 rounded-lg text-sm">Ke Pengaturan TA</a>
            </div>
            @else
            {{-- Filter & Search Form --}}
            <form method="GET" action="{{ route('admin.classes.index') }}" class="mb-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Cari Nama Rombel</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Misal: X MIPA 1" class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Tingkat</label>
                        <select name="grade" class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm text-sm">
                            <option value="">Semua Tingkat</option>
                            <option value="10" {{ request('grade') == '10' ? 'selected' : '' }}>Kelas 10 (X)</option>
                            <option value="11" {{ request('grade') == '11' ? 'selected' : '' }}>Kelas 11 (XI)</option>
                            <option value="12" {{ request('grade') == '12' ? 'selected' : '' }}>Kelas 12 (XII)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Jurusan</label>
                        <select name="major" class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm text-sm">
                            <option value="">Semua Jurusan</option>
                            @foreach($majors as $m)
                                <option value="{{ $m }}" {{ request('major') == $m ? 'selected' : '' }}>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-sm transition-colors">Terapkan Filter</button>
                    </div>
                </div>
            </form>

            {{-- Table --}}
            <div class="overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-sm">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-700 font-bold uppercase border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4">Nama Rombel</th>
                            <th class="px-6 py-4">Tingkat</th>
                            <th class="px-6 py-4">Jurusan</th>
                            <th class="px-6 py-4">Wali Kelas</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($classes as $class)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-bold text-gray-900">{{ $class->name }}</td>
                                <td class="px-6 py-4">Kelas {{ $class->grade }}</td>
                                <td class="px-6 py-4">{{ $class->major }}</td>
                                <td class="px-6 py-4">
                                    @if($class->homeroomTeacher)
                                        <span class="text-sm font-medium text-gray-800">{{ $class->homeroomTeacher->user->full_name ?? 'N/A' }}</span>
                                    @else
                                        <span class="text-xs text-amber-600 italic">Belum diset</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-3">
                                        <a href="{{ route('admin.classes.show', $class) }}" class="text-green-600 hover:text-green-800 transition-colors" title="Lihat Detail Kelas & Atur Siswa">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                        <button @click="editData = { id: '{{ $class->id }}', name: '{{ $class->name }}', grade: '{{ $class->grade }}', major: '{{ $class->major }}', teacher_id: '{{ $class->teacher_id }}' }; showEditModal = true" class="text-blue-600 hover:text-blue-800 transition-colors" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <form action="{{ route('admin.classes.destroy', $class) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus kelas ini? Semua data relasi mungkin terdampak.');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 transition-colors" title="Hapus">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-400">Tidak ada kelas yang ditemukan untuk Tahun Ajaran ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ collect($classes)->isEmpty() ? '' : $classes->links() }}
            </div>

            {{-- Add Modal --}}
            <div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showAddModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showAddModal = false"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div x-show="showAddModal" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border-t-4 border-[#1a6341]">
                        <form action="{{ route('admin.classes.store') }}" method="POST">
                            @csrf
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <h3 class="text-lg leading-6 font-bold text-gray-900 mb-4" id="modal-title">Tambah Rombel Kelas</h3>
                                <div class="space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Tingkat Kelas</label>
                                            <select name="grade" required class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm">
                                                <option value="10">Kelas 10 (X)</option>
                                                <option value="11">Kelas 11 (XI)</option>
                                                <option value="12">Kelas 12 (XII)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                                            <input type="text" name="major" placeholder="MIPA, IPS, dll" required class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Rombel Lengkap</label>
                                            <input type="text" name="name" placeholder="Misal: X MIPA 1" required class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Wali Kelas (Opsional)</label>
                                            <select name="teacher_id" class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm">
                                                <option value="">-- Kosongkan / Belum Ada --</option>
                                                @foreach($teachers as $t)
                                                    <option value="{{ $t->id }}">{{ $t->user->full_name ?? 'N/A' }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#1a6341] text-base font-medium text-white hover:bg-[#238054] sm:ml-3 sm:w-auto sm:text-sm">Simpan</button>
                                <button type="button" @click="showAddModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Edit Modal --}}
            <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showEditModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showEditModal = false"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div x-show="showEditModal" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border-t-4 border-blue-500">
                        <form :action="'{{ url('admin/classes') }}/' + editData.id" method="POST">
                            @csrf @method('PUT')
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <h3 class="text-lg leading-6 font-bold text-gray-900 mb-4" id="modal-title">Edit Rombel Kelas</h3>
                                <div class="space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Tingkat Kelas</label>
                                            <select name="grade" x-model="editData.grade" required class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm">
                                                <option value="10">Kelas 10 (X)</option>
                                                <option value="11">Kelas 11 (XI)</option>
                                                <option value="12">Kelas 12 (XII)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                                            <input type="text" name="major" x-model="editData.major" required class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Rombel Lengkap</label>
                                            <input type="text" name="name" x-model="editData.name" required class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Wali Kelas (Opsional)</label>
                                            <select name="teacher_id" x-model="editData.teacher_id" class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm">
                                                <option value="">-- Kosongkan / Belum Ada --</option>
                                                @foreach($teachers as $t)
                                                    <option value="{{ $t->id }}">{{ $t->user->full_name ?? 'N/A' }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">Simpan Perubahan</button>
                                <button type="button" @click="showEditModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            @endif

        </div>
    </div>
</x-app-layout>
