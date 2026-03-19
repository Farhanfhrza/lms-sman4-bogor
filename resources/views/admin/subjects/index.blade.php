<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Data Mata Pelajaran') }}
        </h2>
    </x-slot>

    <!-- Component Logic: Alpine.js -->
    <div x-data="{ 
            activeTab: 'master', 
            showCreateModal: false, 
            showEditModal: false,
            editData: { id: '', name: '', code: '' }
        }" class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-[#1a6341] relative">

        <div class="p-6 text-gray-900">
            @if(session('success'))
                <div class="mb-6 p-3 bg-green-50 border border-green-300 text-green-800 rounded-lg text-sm flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-6 p-3 bg-red-50 border border-red-300 text-red-800 rounded-lg text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <!-- Tabs Navigation -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button @click="activeTab = 'master'"
                            :class="{'border-[#1a6341] text-[#1a6341]': activeTab === 'master', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'master'}"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm">
                        Data Master Mapel
                    </button>
                    <button @click="activeTab = 'active'"
                            :class="{'border-[#1a6341] text-[#1a6341]': activeTab === 'active', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'active'}"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm">
                        Mapel Terhubung Kelas
                    </button>
                </nav>
            </div>

            <!-- Tab 1: Master Subjects -->
            <div x-show="activeTab === 'master'" x-transition.opacity.duration.300ms>
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 bg-gray-100 p-4 rounded-t-lg border-b-2 border-yellow-400">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center mb-3 md:mb-0">
                        <svg class="w-5 h-5 mr-2 text-[#1a6341]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        Daftar Induk Mata Pelajaran
                    </h3>
                    <button @click="showCreateModal = true" class="bg-[#1a6341] text-white px-4 py-2 rounded-lg font-bold shadow-sm hover:bg-[#238054] transition-colors text-sm flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Mapel
                    </button>
                </div>

                <div class="overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-sm">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-700 font-bold uppercase border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 w-16">No</th>
                                <th class="px-6 py-4">Kode Mapel</th>
                                <th class="px-6 py-4">Nama Mata Pelajaran</th>
                                <th class="px-6 py-4">Digunakan Kelas</th>
                                <th class="px-6 py-4 text-center w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($subjects as $index => $subject)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-gray-600 font-medium">
                                    {{ $subjects->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 bg-gray-100 border border-gray-200 text-gray-800 text-xs font-bold rounded">{{ $subject->code }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $subject->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm border border-emerald-200 bg-emerald-50 text-emerald-700 px-3 py-1 rounded inline-flex items-center font-semibold">
                                        {{ $subject->class_subjects_count }} Kelas
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-3">
                                        <a href="{{ route('admin.subjects.show', $subject->id) }}" class="text-green-600 hover:text-green-800 transition-colors" title="Lihat Detail & Kelola Guru">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                        <button @click="showEditModal = true; editData = {id: {{ $subject->id }}, name: {{ \Illuminate\Support\Js::from($subject->name) }}, code: {{ \Illuminate\Support\Js::from($subject->code) }}}" class="text-blue-600 hover:text-blue-800 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <button @click="$dispatch('open-confirm-modal-del-subj-{{ $subject->id }}')" class="text-red-500 hover:text-red-700 transition-colors p-1" title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                        <x-confirm-delete-modal
                                            :id="'del-subj-'.$subject->id"
                                            title="Hapus Mata Pelajaran?"
                                            description="PERHATIAN! Menghapus mata pelajaran master akan menghapus seluruh data kelas mapel, materi, penugasan, dan kuis yang terhubung dengannya secara permanen!"
                                            :confirmText="$subject->name"
                                            :action="route('admin.subjects.destroy', $subject->id)"
                                            buttonLabel="Ya, Hapus Mapel"
                                        />
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada mata pelajaran yang ditambahkan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $subjects->appends(['class_subjects_page' => request()->class_subjects_page])->links() }}
                </div>
            </div>

            <!-- Tab 2: Connected Subjects (ClassSubjects) -->
            <div x-show="activeTab === 'active'" style="display: none;" x-transition.opacity.duration.300ms>
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 bg-gray-100 p-4 rounded-t-lg border-b-2 border-yellow-400">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center mb-3 md:mb-0">
                        Mapel Aktif (Tahun Ajaran Saat Ini)
                    </h3>
                    <a href="{{ route('admin.schedules.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold shadow-sm hover:bg-blue-700 transition-colors text-sm flex items-center">
                        Kelola Melalui Matrix Jadwal &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-sm">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-700 font-bold uppercase border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4">Mata Pelajaran</th>
                                <th class="px-6 py-4">Rombel/Kelas</th>
                                <th class="px-6 py-4">Guru Pengampu</th>
                                <th class="px-6 py-4 text-center">Aksi / Tautan Course</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($activeClassSubjects as $cs)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $cs->subject->name ?? 'Terhapus' }}</div>
                                    <div class="text-xs text-gray-500 mt-1"><span class="bg-gray-100 border border-gray-200 px-1 py-0.5 rounded">{{ $cs->subject->code ?? '-' }}</span></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-indigo-700">{{ $cs->schoolClass->name ?? 'Unknown' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($cs->teacher)
                                        <div class="flex items-center">
                                            <div class="h-6 w-6 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs mr-2">
                                                {{ substr($cs->teacher->user->full_name ?? '?', 0, 1) }}
                                            </div>
                                            <span class="text-sm font-medium text-gray-800">{{ $cs->teacher->user->full_name ?? 'Guru Terhapus' }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-amber-600 italic">Belum diset</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('manage.courses.show', $cs->slug ?? $cs->id) }}" class="text-xs border border-blue-200 bg-blue-50 text-blue-700 px-3 py-1.5 rounded-md font-bold hover:bg-blue-100 transition-colors inline-block tracking-wide">BUKA COURSE</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-400">Belum ada daftar mapel yang ditugaskan di tahun ajaran ini. Susun jadwal di menu Matrix Jadwal!</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $activeClassSubjects->appends(['subjects_page' => request()->subjects_page])->links() }}
                </div>
            </div>

        </div>

        <!-- Create Modal -->
        <div x-show="showCreateModal" style="display: none;" class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showCreateModal" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <!-- This element is to trick the browser into centering the modal contents. -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showCreateModal" 
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <form action="{{ route('admin.subjects.store') }}" method="POST">
                        @csrf
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                                        Tambah Mapel Master
                                    </h3>
                                    <div class="mt-4 space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Mata Pelajaran</label>
                                            <input type="text" name="name" class="w-full border-gray-300 rounded-md shadow-sm focus:border-[#1a6341] focus:ring focus:ring-[#1a6341] focus:ring-opacity-50" required placeholder="Contoh: Matematika Peminatan" />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Kode / Singkatan</label>
                                            <input type="text" name="code" class="w-full border-gray-300 rounded-md shadow-sm focus:border-[#1a6341] focus:ring focus:ring-[#1a6341] focus:ring-opacity-50" required placeholder="Contoh: MATE-MINAT" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#1a6341] text-base font-medium text-white hover:bg-[#238054] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1a6341] sm:ml-3 sm:w-auto sm:text-sm">
                                Simpan
                            </button>
                            <button type="button" @click="showCreateModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="showEditModal" style="display: none;" class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showEditModal" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showEditModal" 
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    
                    <form :action="`{{ url('admin/subjects') }}/${editData.id}`" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-amber-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                                        Edit Mapel Master
                                    </h3>
                                    <div class="mt-4 space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Mata Pelajaran</label>
                                            <input type="text" name="name" x-model="editData.name" class="w-full border-gray-300 rounded-md shadow-sm focus:border-[#1a6341] focus:ring focus:ring-[#1a6341] focus:ring-opacity-50" required />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Kode / Singkatan</label>
                                            <input type="text" name="code" x-model="editData.code" class="w-full border-gray-300 rounded-md shadow-sm focus:border-[#1a6341] focus:ring focus:ring-[#1a6341] focus:ring-opacity-50" required />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-amber-500 text-base font-medium text-white hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Update
                            </button>
                            <button type="button" @click="showEditModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
