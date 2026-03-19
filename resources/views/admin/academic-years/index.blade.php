<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Tahun Ajaran') }}
        </h2>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-[#1a6341] relative">
        <div class="p-6 text-gray-900" x-data="{ showAddModal: false, showEditModal: false, editData: { id: '', name: '' } }">

            <div class="flex justify-between items-center mb-6 bg-gray-100 p-4 rounded-t-lg border-b-2 border-yellow-400">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-[#1a6341]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Daftar Tahun Ajaran
                </h3>
                <button @click="showAddModal = true" class="bg-[#1a6341] hover:bg-[#238054] text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm flex items-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Tahun Ajaran
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

            <div class="overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-sm">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-700 font-bold uppercase border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4">Tahun Ajaran</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($academicYears as $ay)
                            <tr class="hover:bg-gray-50 transition-colors {{ $ay->is_active ? 'bg-green-50/30' : '' }}">
                                <td class="px-6 py-4 font-medium text-gray-900 flex items-center">
                                    {{ $ay->name }}
                                    @if($ay->is_active)
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Aktif Sekarang</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($ay->is_active)
                                        <span class="inline-flex items-center text-green-600 font-semibold">
                                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Aktif
                                        </span>
                                    @else
                                        <button @click="$dispatch('open-confirm-modal-activate-ay-{{ $ay->id }}')" class="text-gray-500 hover:text-[#1a6341] transition-colors border border-gray-300 hover:border-[#1a6341] px-3 py-1 rounded text-xs">
                                            Set Aktif
                                        </button>
                                        <x-confirm-delete-modal
                                            :id="'activate-ay-'.$ay->id"
                                            title="Ganti Tahun Ajaran Aktif?"
                                            :description="'Mengaktifkan tahun ajaran &quot;'.$ay->name.'&quot; akan secara otomatis menonaktifkan tahun ajaran yang sedang berjalan. Semua pengaturan kelas dan mapel akan beralih ke tahun ajaran baru ini.'"
                                            :confirmText="$ay->name"
                                            :action="route('admin.academic-years.activate', $ay)"
                                            method="PATCH"
                                            buttonLabel="Ya, Aktifkan Sekarang"
                                        />
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-3">
                                        <button @click="editData = { id: '{{ $ay->id }}', name: '{{ $ay->name }}' }; showEditModal = true" class="text-blue-600 hover:text-blue-800 transition-colors" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <button @click="$dispatch('open-confirm-modal-del-ay-{{ $ay->id }}')" class="text-red-500 hover:text-red-700 transition-colors" title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                        <x-confirm-delete-modal
                                            :id="'del-ay-'.$ay->id"
                                            title="Hapus Tahun Ajaran?"
                                            :description="'Menghapus tahun ajaran &quot;'.$ay->name.'&quot; berpotensi menghapus seluruh data kelas, jadwal, dan riwayat akademik yang terhubung dengan tahun ajaran ini secara PERMANEN.'"
                                            :confirmText="$ay->name"
                                            :action="route('admin.academic-years.destroy', $ay)"
                                            buttonLabel="Ya, Hapus Tahun Ajaran"
                                        />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-400">Belum ada data Tahun Ajaran.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Add Modal --}}
            <div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showAddModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showAddModal = false"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div x-show="showAddModal" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border-t-4 border-[#1a6341]">
                        <form action="{{ route('admin.academic-years.store') }}" method="POST">
                            @csrf
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <h3 class="text-lg leading-6 font-bold text-gray-900 mb-4" id="modal-title">Tambah Tahun Ajaran</h3>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Tahun Ajaran</label>
                                    <input type="text" name="name" placeholder="Misal: 2024/2025" required class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm">
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
                        <form :action="'{{ url('admin/academic-years') }}/' + editData.id" method="POST">
                            @csrf @method('PUT')
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <h3 class="text-lg leading-6 font-bold text-gray-900 mb-4" id="modal-title">Edit Tahun Ajaran</h3>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Tahun Ajaran</label>
                                    <input type="text" name="name" x-model="editData.name" required class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm">
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

        </div>
    </div>
</x-app-layout>
