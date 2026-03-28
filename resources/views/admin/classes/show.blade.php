<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center">
            <a href="{{ route('admin.classes.index') }}" class="text-gray-500 hover:text-[#1a6341] mr-3 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            Detail Kelas: {{ $schoolClass->name }}
        </h2>
    </x-slot>

    <div class="space-y-6" x-data="{ currentTab: 'overview', showEnrollModal: false }">

        @if(session('success'))
            <div class="p-3 bg-green-50 border border-green-300 text-green-800 rounded-lg text-sm flex items-center">
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

        {{-- Top Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg border-t-4 border-[#1a6341] shadow-sm p-4 flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-[#1a6341] mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Wali Kelas</p>
                    <p class="text-sm font-bold text-gray-900">{{ $schoolClass->homeroomTeacher->user->full_name ?? 'Kosong' }}</p>
                </div>
            </div>
            
            <div class="bg-white rounded-lg border-t-4 border-blue-500 shadow-sm p-4 flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total Siswa</p>
                    <p class="text-lg font-bold text-gray-900">{{ $schoolClass->studentClasses->count() }} orang</p>
                </div>
            </div>

            <div class="bg-white rounded-lg border-t-4 border-purple-500 shadow-sm p-4 flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Mata Pelajaran</p>
                    <p class="text-lg font-bold text-gray-900">{{ $schoolClass->classSubjects->count() }} Subjek</p>
                </div>
            </div>

            <div class="bg-white rounded-lg border-t-4 border-yellow-500 shadow-sm p-4 flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Tahun Ajaran</p>
                    <p class="text-sm font-bold text-gray-900">{{ $schoolClass->academicYear->name ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        {{-- Tabs Navigation --}}
        <div class="bg-white border-b border-gray-200 shadow-sm rounded-lg overflow-hidden flex">
            <button @click="currentTab = 'overview'" :class="{'bg-[#1a6341] text-white border-b-2 border-green-700': currentTab === 'overview', 'text-gray-500 hover:text-gray-700 hover:bg-gray-50': currentTab !== 'overview'}" class="px-6 py-3 font-medium text-sm transition-colors focus:outline-none flex flex-1 justify-center items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                Daftar Siswa
            </button>
            <button @click="currentTab = 'schedule'" :class="{'bg-[#1a6341] text-white border-b-2 border-green-700': currentTab === 'schedule', 'text-gray-500 hover:text-gray-700 hover:bg-gray-50': currentTab !== 'schedule'}" class="px-6 py-3 font-medium text-sm transition-colors focus:outline-none flex flex-1 justify-center items-center border-l border-gray-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Jadwal Pelajaran
            </button>
        </div>

        {{-- Tab Content: Daftar Siswa --}}
        <div x-show="currentTab === 'overview'" class="bg-white shadow-sm rounded-lg border border-gray-200 p-6" style="display: none;" x-cloak x-data="{ editMode: false }">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                <h3 class="text-lg font-bold text-gray-800">Siswa yang Terdaftar di Kelas {{ $schoolClass->name }}</h3>
                <div class="flex items-center gap-2">
                    <form action="{{ route('admin.classes.generate-attendance-numbers', $schoolClass) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin men-generate ulang nomor absen secara otomatis berdasarkan abjad? Ini akan menimpa nomor urut sebelumnya.')">
                        @csrf
                        <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors border border-gray-300 flex items-center">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Generate Otomatis
                        </button>
                    </form>
                    <button @click="editMode = !editMode" class="bg-yellow-50 hover:bg-yellow-100 text-yellow-700 border border-yellow-300 px-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        <span x-text="editMode ? 'Batal Edit' : 'Edit Nomor'"></span>
                    </button>
                    <button @click="showEnrollModal = true" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        Setup Siswa
                    </button>
                </div>
            </div>
            
            <form action="{{ route('admin.classes.update-attendance-numbers', $schoolClass) }}" method="POST" id="formAttendanceNumbers">
                @csrf
                @method('PUT')
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="w-full text-sm text-left align-middle">
                        <thead class="bg-gray-50 text-gray-700 font-bold uppercase border-b border-gray-200">
                            <tr>
                                <th class="w-24 px-6 py-3 text-center">No Absen</th>
                                <th class="px-6 py-3">NISN</th>
                                <th class="px-6 py-3">Nama Lengkap</th>
                                <th class="px-6 py-3">Jenis Kelamin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @php
                                $sortedStudents = $schoolClass->studentClasses->sortBy([
                                    ['attendance_number', 'asc'],
                                    ['student.user.full_name', 'asc'],
                                ]);
                            @endphp
                            @forelse($sortedStudents as $sc)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 text-center font-medium">
                                        <div x-show="!editMode" class="text-[#1a6341] font-bold text-base">{{ $sc->attendance_number ?? '-' }}</div>
                                        <div x-show="editMode" style="display: none;">
                                            <input type="number" name="attendance_numbers[{{ $sc->student_id }}]" value="{{ $sc->attendance_number }}" min="1" class="w-16 text-center border-gray-300 rounded-md focus:ring-[#1a6341] py-1 text-sm bg-yellow-50 font-bold">
                                        </div>
                                    </td>
                                    <td class="px-6 py-3 font-mono text-gray-600">{{ $sc->student->nisn }}</td>
                                    <td class="px-6 py-3 font-semibold text-gray-900">{{ $sc->student->user->full_name ?? $sc->student->user->name }}</td>
                                    <td class="px-6 py-3">{{ $sc->student->user->gender == 'L' ? 'Laki-laki' : ($sc->student->user->gender == 'P' ? 'Perempuan' : '-') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-400">Belum ada siswa di kelas ini. Klik "Setup Siswa" untuk menambahkan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex justify-end" x-show="editMode" style="display: none;">
                    <button type="submit" class="bg-[#1a6341] hover:bg-[#145232] text-white px-6 py-2 rounded-lg text-sm font-bold shadow-sm flex items-center transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        {{-- Tab Content: Jadwal Pelajaran --}}
        <div x-show="currentTab === 'schedule'" class="bg-white shadow-sm rounded-lg border border-gray-200 p-6" style="display: none;" x-cloak>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800">Jadwal Mata Pelajaran - Kelas {{ $schoolClass->name }}</h3>
                <a href="{{ route('admin.schedules.index') }}" class="text-[#1a6341] hover:text-[#238054] text-sm font-medium flex items-center">
                    Buka Pengaturan Jadwal (Matrix)
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>

            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="w-full text-sm text-left align-middle">
                    <thead class="bg-gray-50 text-gray-700 font-bold uppercase border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3">Hari</th>
                            <th class="px-6 py-3">Waktu</th>
                            <th class="px-6 py-3">Mata Pelajaran</th>
                            <th class="px-6 py-3">Guru Pengajar</th>
                            <th class="px-6 py-3">Ruangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php
                            $daysIndo = [
                                'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
                                'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
                            ];
                            $allSchedules = collect();
                            foreach($schoolClass->classSubjects as $cs) {
                                foreach($cs->schedules as $sched) {
                                    $sched->subject = $cs->subject;
                                    $sched->teacher = $cs->teacher;
                                    $allSchedules->push($sched);
                                }
                            }
                            $sortedSchedules = $allSchedules->sortBy(function($s) {
                                $dayOrder = [
                                    'Senin'=>1, 'Selasa'=>2, 'Rabu'=>3, 'Kamis'=>4, 'Jumat'=>5, 'Sabtu'=>6, 'Minggu'=>7,
                                    'Monday'=>1, 'Tuesday'=>2, 'Wednesday'=>3, 'Thursday'=>4, 'Friday'=>5, 'Saturday'=>6, 'Sunday'=>7
                                ];
                                return ($dayOrder[$s->day_of_week] ?? 9) . $s->start_time;
                            });
                        @endphp

                        @forelse($sortedSchedules as $schedule)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 font-semibold text-gray-900">{{ $daysIndo[$schedule->day_of_week] ?? $schedule->day_of_week }}</td>
                                <td class="px-6 py-3 text-gray-600 font-mono">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</td>
                                <td class="px-6 py-3 font-bold text-[#1a6341]">{{ $schedule->subject->name ?? 'N/A' }}</td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center">
                                        <div class="w-6 h-6 rounded-full bg-gray-200 mr-2 overflow-hidden flex-shrink-0">
                                            @if($schedule->teacher)
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($schedule->teacher->user->full_name ?? '') }}&background=E5E7EB&color=4B5563" class="w-full h-full object-cover">
                                            @endif
                                        </div>
                                        <span>{{ $schedule->teacher->user->full_name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-3">{{ $schedule->room ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada jadwal untuk kelas ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Setup Siswa / Student Enrollment Modal --}}
        <div x-show="showEnrollModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" aria-labelledby="modal-title" role="dialog" aria-modal="true"
             x-data="enrollmentSystem()">
            <div class="flex items-center justify-center min-h-screen p-4 text-center sm:block sm:p-0">
                <div x-show="showEnrollModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showEnrollModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showEnrollModal" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle w-full max-w-5xl border-t-4 border-blue-600">
                    <form action="{{ route('admin.classes.enroll', $schoolClass) }}" method="POST">
                        @csrf
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="text-xl font-bold text-gray-900" id="modal-title">Setup Siswa: {{ $schoolClass->name }}</h3>
                            <button type="button" @click="showEnrollModal = false" class="text-gray-400 hover:text-gray-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        
                        <div class="p-6">
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700">
                                            Pilih siswa dari kotak kiri (Belum Ada Kelas) dan pindahkan ke kotak kanan (Masuk Kelas). Jangan lupa klik "Simpan Perubahan" setelah memindahkan.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-[1fr_auto_1fr] gap-4 items-center h-[28rem]">
                                {{-- Left Column: Available Students --}}
                                <div class="h-full flex flex-col border border-gray-300 rounded-lg bg-white overflow-hidden shadow-inner">
                                    <div class="bg-gray-100 px-4 py-2 border-b border-gray-300 font-bold text-gray-700 flex justify-between items-center">
                                        <span>Belum Punya Kelas (<span x-text="availableStudents.length"></span>)</span>
                                    </div>
                                    <div class="p-2 border-b border-gray-200">
                                        <input type="text" x-model="searchAvailable" placeholder="Cari Nama/NISN..." class="w-full text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 py-1.5">
                                    </div>
                                    <div class="flex-1 overflow-y-auto p-2 bg-gray-50">
                                        <template x-for="student in filteredAvailable" :key="student.id">
                                            <div @click="toggleSelection(student.id, 'available')" 
                                                 :class="{'bg-blue-100 border-blue-300': isSelected(student.id, 'available'), 'bg-white border-gray-200 hover:bg-gray-100': !isSelected(student.id, 'available')}"
                                                 class="p-2 mb-1 border rounded text-sm cursor-pointer transition-colors user-select-none flex justify-between items-center">
                                                <div>
                                                    <div class="font-bold text-gray-800" x-text="student.name"></div>
                                                    <div class="text-xs text-gray-500 font-mono" x-text="student.nisn"></div>
                                                </div>
                                                <input type="checkbox" :checked="isSelected(student.id, 'available')" class="h-4 w-4 text-blue-600 rounded border-gray-300 pointer-events-none">
                                            </div>
                                        </template>
                                        <div x-show="filteredAvailable.length === 0" class="text-center py-4 text-gray-400 text-sm">Tidak ditemukan</div>
                                    </div>
                                </div>

                                {{-- Middle Controls --}}
                                <div class="flex md:flex-col justify-center gap-3 p-4">
                                    <button type="button" @click="moveToRight" :disabled="selectedAvailable.length === 0" class="p-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 text-white rounded-full shadow transition-colors" title="Pindah ke Kelas Ini">
                                        <svg class="w-6 h-6 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                                        <svg class="w-6 h-6 block md:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 13l-7 7-7-7m14-8l-7 7-7-7"></path></svg>
                                    </button>
                                    <button type="button" @click="moveToLeft" :disabled="selectedEnrolled.length === 0" class="p-2 bg-red-500 hover:bg-red-600 disabled:bg-gray-300 text-white rounded-full shadow transition-colors" title="Keluarkan dari Kelas">
                                        <svg class="w-6 h-6 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
                                        <svg class="w-6 h-6 block md:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11l7-7 7 7M5 19l7-7 7 7"></path></svg>
                                    </button>
                                </div>

                                {{-- Right Column: Enrolled Students --}}
                                <div class="h-full flex flex-col border border-green-500 rounded-lg bg-white overflow-hidden shadow-inner ring-2 ring-green-100">
                                    <div class="bg-green-100 px-4 py-2 border-b border-green-300 font-bold text-[#1a6341] flex justify-between items-center">
                                        <span>Di dalam {{ $schoolClass->name }} (<span x-text="enrolledStudents.length"></span>)</span>
                                    </div>
                                    <div class="p-2 border-b border-gray-200">
                                        <input type="text" x-model="searchEnrolled" placeholder="Cari Nama/NISN..." class="w-full text-sm border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 py-1.5">
                                    </div>
                                    <div class="flex-1 overflow-y-auto p-2 bg-gray-50">
                                        {{-- Hidden inputs to actually submit the data --}}
                                        <template x-for="student in enrolledStudents" :key="student.id">
                                            <input type="hidden" name="enrolled_students[]" :value="student.id">
                                        </template>

                                        <template x-for="student in filteredEnrolled" :key="student.id">
                                            <div @click="toggleSelection(student.id, 'enrolled')" 
                                                 :class="{'bg-red-100 border-red-300': isSelected(student.id, 'enrolled'), 'bg-white border-gray-200 hover:bg-gray-100': !isSelected(student.id, 'enrolled')}"
                                                 class="p-2 mb-1 border rounded text-sm cursor-pointer transition-colors user-select-none flex justify-between items-center">
                                                <div>
                                                    <div class="font-bold text-gray-800" x-text="student.name"></div>
                                                    <div class="text-xs text-gray-500 font-mono" x-text="student.nisn"></div>
                                                </div>
                                                <input type="checkbox" :checked="isSelected(student.id, 'enrolled')" class="h-4 w-4 text-red-600 rounded border-gray-300 pointer-events-none">
                                            </div>
                                        </template>
                                        <div x-show="filteredEnrolled.length === 0" class="text-center py-4 text-gray-400 text-sm">Belum ada siswa dipilih</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end">
                            <button type="button" @click="showEnrollModal = false" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium mr-3">Batal</button>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium shadow flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('enrollmentSystem', () => ({
                searchAvailable: '',
                searchEnrolled: '',
                
                // Safely load data from backend using JSON
                availableStudents: @json($unenrolledStudents->map(function($us) {
                    return [
                        'id' => $us->id,
                        'name' => $us->user->full_name ?? $us->user->name ?? 'Unknown',
                        'nisn' => $us->nisn ?? '-'
                    ];
                })->values()->all()),
                
                enrolledStudents: @json($schoolClass->studentClasses->map(function($sc) {
                    return [
                        'id' => $sc->student->id,
                        'name' => $sc->student->user->full_name ?? $sc->student->user->name ?? 'Unknown',
                        'nisn' => $sc->student->nisn ?? '-'
                    ];
                })->values()->all()),
                
                // Track selected items
                selectedAvailable: [],
                selectedEnrolled: [],

                get filteredAvailable() {
                    if (this.searchAvailable === '') return this.availableStudents;
                    const q = this.searchAvailable.toLowerCase();
                    return this.availableStudents.filter(s => s.name.toLowerCase().includes(q) || s.nisn.toLowerCase().includes(q));
                },

                get filteredEnrolled() {
                    if (this.searchEnrolled === '') return this.enrolledStudents;
                    const q = this.searchEnrolled.toLowerCase();
                    return this.enrolledStudents.filter(s => s.name.toLowerCase().includes(q) || s.nisn.toLowerCase().includes(q));
                },

                toggleSelection(id, listType) {
                    if (listType === 'available') {
                        const idx = this.selectedAvailable.indexOf(id);
                        if (idx > -1) this.selectedAvailable.splice(idx, 1);
                        else this.selectedAvailable.push(id);
                    } else {
                        const idx = this.selectedEnrolled.indexOf(id);
                        if (idx > -1) this.selectedEnrolled.splice(idx, 1);
                        else this.selectedEnrolled.push(id);
                    }
                },

                isSelected(id, listType) {
                    if (listType === 'available') return this.selectedAvailable.includes(id);
                    return this.selectedEnrolled.includes(id);
                },

                moveToRight() {
                    // Get actual student objects
                    const moving = this.availableStudents.filter(s => this.selectedAvailable.includes(s.id));
                    // Add to enrolled, remove from available
                    this.enrolledStudents = [...this.enrolledStudents, ...moving].sort((a, b) => a.name.localeCompare(b.name));
                    this.availableStudents = this.availableStudents.filter(s => !this.selectedAvailable.includes(s.id));
                    // Clear selection
                    this.selectedAvailable = [];
                },

                moveToLeft() {
                    // Get actual student objects
                    const moving = this.enrolledStudents.filter(s => this.selectedEnrolled.includes(s.id));
                    // Add to available, remove from enrolled
                    this.availableStudents = [...this.availableStudents, ...moving].sort((a, b) => a.name.localeCompare(b.name));
                    this.enrolledStudents = this.enrolledStudents.filter(s => !this.selectedEnrolled.includes(s.id));
                    // Clear selection
                    this.selectedEnrolled = [];
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>
