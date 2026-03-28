<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Data Siswa') }}
        </h2>
    </x-slot>

    <div x-data="{ showImportModal: false }" class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-[#1a6341] relative">
        <div class="p-6 text-gray-900">
            
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 bg-gray-100 p-4 rounded-t-lg border-b-2 border-yellow-400">
                <h3 class="text-lg font-bold text-gray-800 mb-4 md:mb-0">
                    Daftar Siswa — Total: {{ $students->total() }}
                </h3>
                <div class="flex items-center space-x-2">
                    <button @click="showImportModal = true" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg shadow-sm transition-colors flex items-center text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Import Excel
                    </button>
                    <a href="{{ route('admin.students.create') }}" class="bg-[#1a6341] hover:bg-[#238054] text-white px-4 py-2 rounded-lg shadow-sm transition-colors flex items-center text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Manual
                    </a>
                </div>
            </div>

            @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="GET" action="{{ route('admin.students.index') }}" class="flex flex-col md:flex-row justify-between items-center mb-4 text-sm text-gray-600 gap-3">
                <div class="flex items-center gap-3">
                    <div class="flex items-center">
                        <span class="mr-2">Show</span>
                        <select name="per_page" onchange="this.form.submit()" class="border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm h-9 text-sm">
                            @foreach([10, 25, 50] as $pp)
                                <option value="{{ $pp }}" {{ request('per_page', 25) == $pp ? 'selected' : '' }}>{{ $pp }}</option>
                            @endforeach
                        </select>
                        <span class="ml-2">entries</span>
                    </div>
                    <div class="flex items-center">
                        <span class="mr-2">Angkatan:</span>
                        <select name="enrollment_year" onchange="this.form.submit()" class="border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm h-9 text-sm">
                            <option value="">Semua</option>
                            @foreach($enrollmentYears as $yr)
                                <option value="{{ $yr }}" {{ request('enrollment_year') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center">
                        <span class="mr-2">Kelas:</span>
                        <select name="class_id" onchange="this.form.submit()" class="border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm h-9 text-sm">
                            <option value="">Semua</option>
                            @foreach($schoolClasses as $sc)
                                <option value="{{ $sc->id }}" {{ request('class_id') == $sc->id ? 'selected' : '' }}>{{ $sc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex items-center w-full md:w-auto">
                    <span class="mr-2">Search:</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, NISN..."
                           class="border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm h-9 px-3 w-full md:w-64"
                           onchange="this.form.submit()">
                </div>
            </form>

            <div class="overflow-x-auto border-t border-b border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-gray-800 font-bold uppercase">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left tracking-wider">No</th>
                            <th scope="col" class="px-6 py-3 text-left tracking-wider">Nama</th>
                            <th scope="col" class="px-6 py-3 text-left tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-3 text-left tracking-wider">NISN</th>
                            <th scope="col" class="px-6 py-3 text-left tracking-wider">Kelas</th>
                            <th scope="col" class="px-6 py-3 text-left tracking-wider">Angkatan</th>
                            <th scope="col" class="px-6 py-3 text-center tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($students as $index => $student)
                        <tr class="hover:bg-gray-50 transition-colors {{ $index % 2 === 0 ? 'bg-gray-100' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $students->firstItem() + $index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0 mr-3 rounded-full overflow-hidden border border-gray-200 shadow-sm">
                                        <img class="h-10 w-10 object-cover" src="{{ optional($student->user)->profile_photo_url }}" alt="">
                                    </div>
                                    <div class="font-medium text-gray-700">{{ $student->user->full_name ?? '-' }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $student->user->email ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600 font-mono">{{ $student->nisn ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                @php
                                    $latestClass = $student->studentClasses->sortByDesc('id')->first();
                                @endphp
                                {{ $latestClass?->schoolClass?->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $student->enrollment_year }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('admin.students.edit', $student) }}" class="text-blue-600 hover:text-blue-800 transition-colors" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <button @click="$dispatch('open-confirm-modal-del-student-{{ $student->id }}')" class="text-red-500 hover:text-red-700 transition-colors" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                    <x-confirm-delete-modal
                                        :id="'del-student-'.$student->id"
                                        title="Hapus Siswa?"
                                        :description="'Data siswa &quot;'.($student->user->full_name ?? $student->user->name).'&quot; beserta akun dan seluruh riwayat tugasnya akan terhapus permanen.'"
                                        :confirmText="$student->user->full_name ?? $student->user->name"
                                        :action="route('admin.students.destroy', $student)"
                                        buttonLabel="Ya, Hapus Siswa"
                                    />
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                Belum ada data siswa.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="h-1 w-full bg-gray-200">
                    <div class="h-1 bg-[#1a6341]" style="width: {{ $students->total() > 0 ? min(100, ($students->currentPage() / $students->lastPage()) * 100) : 0 }}%"></div>
                </div>
            </div>

            <div class="flex flex-col md:flex-row justify-between items-center mt-4 text-sm text-gray-600">
                <div class="mb-2 md:mb-0">
                    Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} of {{ $students->total() }} entries
                </div>
                <div>
                    {{ $students->links() }}
                </div>
            </div>
        </div>

        <!-- Import Excel Modal -->
        <div x-show="showImportModal" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div @click.away="showImportModal = false"
                 class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">

                <!-- Modal Header -->
                <div class="bg-[#1a6341] text-white px-6 py-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Import Data Siswa dari Excel
                    </h3>
                    <button @click="showImportModal = false" class="text-white/80 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <form method="POST" action="{{ route('admin.students.import') }}" enctype="multipart/form-data" class="p-6 space-y-5">
                    @csrf

                    <div>
                        <label for="enrollment_year_import" class="block text-sm font-medium text-gray-700 mb-1">Tahun Masuk (Enrollment Year) <span class="text-red-500">*</span></label>
                        <select id="enrollment_year_import" name="enrollment_year" required
                                class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm">
                            @for($y = date('Y') + 1; $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label for="file" class="block text-sm font-medium text-gray-700 mb-1">File Excel / CSV <span class="text-red-500">*</span></label>
                        <input type="file" id="file" name="file" required accept=".xlsx,.csv,.xls"
                               class="w-full border border-gray-300 rounded-md shadow-sm text-sm file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-medium file:bg-[#1a6341] file:text-white hover:file:bg-[#238054]">
                        <p class="text-xs text-gray-400 mt-1">Format: .xlsx, .csv, .xls (maks. 5MB)</p>
                    </div>

                    <!-- Format Instructions -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm mt-4">
                        <div class="flex justify-between items-center mb-2">
                            <p class="font-semibold text-blue-800">📋 Contoh Format File Excel/CSV:</p>
                            <a href="{{ asset('templates/import_siswa_template.csv') }}" download class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded shadow-sm inline-flex flex-shrink-0 items-center transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Download Template CSV
                            </a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-xs border border-blue-200">
                                <thead>
                                    <tr class="bg-blue-100">
                                        <th class="border border-blue-200 px-3 py-1 text-left">Kolom A: <strong>NAMA</strong></th>
                                        <th class="border border-blue-200 px-3 py-1 text-left">Kolom B: <strong>NISN</strong></th>
                                        <th class="border border-blue-200 px-3 py-1 text-left">Kolom C: <strong>JENIS_KELAMIN</strong></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="border border-blue-200 px-3 py-1 text-gray-600">Budi Santoso</td>
                                        <td class="border border-blue-200 px-3 py-1 text-gray-600 font-mono">0012345678</td>
                                        <td class="border border-blue-200 px-3 py-1 text-gray-600 font-mono text-center">L</td>
                                    </tr>
                                    <tr class="bg-blue-50">
                                        <td class="border border-blue-200 px-3 py-1 text-gray-600">Ani Rahmawati</td>
                                        <td class="border border-blue-200 px-3 py-1 text-gray-600 font-mono">0098765432</td>
                                        <td class="border border-blue-200 px-3 py-1 text-gray-600 font-mono text-center">P</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="mt-3 text-blue-600 text-xs space-y-1">
                            ✅ <strong>NAMA</strong> dan <strong>NISN</strong> adalah kolom wajib isi.<br>
                            ✅ <strong>JENIS_KELAMIN</strong> diisi dengan <strong>L</strong> (Laki-laki) atau <strong>P</strong> (Perempuan).<br>
                            ✅ Email akan otomatis di-generate format: <code class="bg-blue-100 px-1 rounded">[NISN]@student.sman4bogor.sch.id</code><br>
                            ✅ Password default akun (bisa dirubah siswa nanti): <code class="bg-blue-100 px-1 rounded">Siswa[NISN]</code>
                        </p>
                    </div>

                    <div class="flex items-center justify-end pt-2 border-t border-gray-200">
                        <button type="button" @click="showImportModal = false" class="text-gray-500 hover:text-gray-700 mr-4 text-sm">Batal</button>
                        <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2.5 rounded-lg shadow-sm transition-colors text-sm font-medium flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Mulai Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
