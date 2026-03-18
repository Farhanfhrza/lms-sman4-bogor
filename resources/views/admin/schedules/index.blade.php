<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Penjadwalan Kelas') }}
        </h2>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-[#1a6341] relative">
        <div class="p-6 text-gray-900">

            {{-- Header --}}
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 bg-gray-100 p-4 rounded-t-lg border-b-2 border-yellow-400">
                <h3 class="text-lg font-bold text-gray-800 mb-2 md:mb-0 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-[#1a6341]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Matrix Jadwal Mata Pelajaran
                </h3>
                @if($academicYear)
                    <span class="bg-[#1a6341] text-white px-3 py-1 rounded-full text-sm font-medium">TA: {{ $academicYear->name }}</span>
                @else
                    <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-medium">⚠ Tidak ada Tahun Ajaran aktif!</span>
                @endif
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-300 text-green-800 rounded-lg text-sm flex items-start">
                <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
            @endif
            @if(session('error'))
            <div class="mb-4 p-3 bg-red-50 border border-red-300 text-red-800 rounded-lg text-sm flex items-start">
                <svg class="w-5 h-5 mr-2 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span>{{ session('error') }}</span>
            </div>
            @endif
            @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-300 text-red-800 rounded-lg text-sm">
                <p class="font-semibold mb-1">Terdapat kesalahan pada pengisian form:</p>
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
            @endif

            {{-- ===================== --}}
            {{-- Step 1: Filter Kelas --}}
            {{-- ===================== --}}
            @if(!$academicYear)
            <div class="text-center py-16 text-gray-500">
                <svg class="w-16 h-16 mx-auto mb-4 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <p class="text-lg font-semibold text-red-600">Tidak ada Tahun Ajaran Aktif</p>
                <p class="text-sm mt-1">Silakan aktifkan Tahun Ajaran di pengaturan terlebih dahulu.</p>
            </div>
            @else
            <form method="GET" action="{{ route('admin.schedules.index') }}" class="mb-6">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h4 class="text-sm font-bold text-gray-700 mb-3">Pilih Kelas:</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        {{-- Tingkat --}}
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Tingkat Kelas</label>
                            <select name="grade" onchange="this.form.major.value=''; this.form.rombel_id.value=''; this.form.submit();"
                                    class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm text-sm">
                                <option value="">-- Pilih Tingkat --</option>
                                @foreach(array_keys($groupedClasses) as $grade)
                                    <option value="{{ $grade }}" {{ $selectedGrade == $grade ? 'selected' : '' }}>
                                        Kelas {{ $grade == 10 ? 'X' : ($grade == 11 ? 'XI' : 'XII') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Jurusan --}}
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Jurusan / Peminatan</label>
                            <select name="major" onchange="this.form.rombel_id.value=''; this.form.submit();"
                                    class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm text-sm"
                                    {{ empty($selectedGrade) ? 'disabled' : '' }}>
                                <option value="">-- Pilih Jurusan --</option>
                                @foreach($availableMajors as $major)
                                    <option value="{{ $major }}" {{ $selectedMajor == $major ? 'selected' : '' }}>{{ $major }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Rombel --}}
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Rombel</label>
                            <select name="rombel_id" onchange="this.form.submit();"
                                    class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm text-sm"
                                    {{ empty($selectedMajor) ? 'disabled' : '' }}>
                                <option value="">-- Pilih Rombel --</option>
                                @foreach($availableRombels as $rombel)
                                    <option value="{{ $rombel->id }}" {{ $selectedRombel == $rombel->id ? 'selected' : '' }}>
                                        {{ $rombel->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </form>

            {{-- ======================== --}}
            {{-- Step 2: Matrix Form     --}}
            {{-- ======================== --}}
            @if($selectedClass)
            <form method="POST" action="{{ route('admin.schedules.store') }}"
                  x-data="scheduleMatrix({{ json_encode($existingMappings->values()->map(fn($m) => [
                      'subject_id'   => $m->subject_id,
                      'subject_name' => $m->subject->name ?? '?',
                      'subject_code' => $m->subject->code ?? '',
                      'teacher_id'   => $m->teacher_id,
                      'schedules'    => $m->schedules->map(fn($s) => [
                          'day_of_week' => $s->day_of_week,
                          'start_time'  => substr($s->start_time, 0, 5),
                          'end_time'    => substr($s->end_time, 0, 5),
                          'room'        => $s->room ?? '',
                      ])->toArray(),
                  ])->toArray()) }})">
                @csrf
                <input type="hidden" name="class_id"  value="{{ $selectedClass->id }}">
                <input type="hidden" name="grade"     value="{{ $selectedGrade }}">
                <input type="hidden" name="major"     value="{{ $selectedMajor }}">

                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-bold text-gray-700">
                        Jadwal untuk: <span class="text-[#1a6341]">{{ $selectedClass->name }}</span>
                        <span class="text-xs text-gray-400 font-normal ml-2">(<span x-text="rows.length"></span> mata pelajaran)</span>
                    </h4>
                </div>

                {{-- Subject Search & Add --}}
                <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg flex flex-col sm:flex-row items-start sm:items-center gap-3">
                    <div class="flex-1 relative">
                        <label class="block text-xs text-blue-700 font-medium mb-1">Cari & Tambah Mata Pelajaran:</label>
                        <input type="text" x-model="searchQuery" @input.debounce.300ms="searchSubjects()"
                               @focus="showResults = true" @click.away="showResults = false"
                               placeholder="Ketik nama mata pelajaran..."
                               class="w-full border-blue-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm text-sm">
                        <div x-show="showResults && searchResults.length > 0"
                             class="absolute z-20 left-0 right-0 bg-white border border-gray-200 rounded-lg shadow-lg mt-1 max-h-48 overflow-y-auto">
                            <template x-for="subj in searchResults" :key="subj.id">
                                <button type="button" @click="addRow(subj); showResults = false; searchQuery = ''"
                                        class="w-full text-left px-4 py-2.5 text-sm hover:bg-green-50 border-b last:border-0 flex justify-between items-center">
                                    <span x-text="subj.name" class="font-medium text-gray-700"></span>
                                    <span x-text="subj.code" class="text-xs text-gray-400"></span>
                                </button>
                            </template>
                        </div>
                        <p x-show="showResults && searchResults.length === 0 && searchQuery.length > 1"
                           class="absolute z-20 left-0 right-0 bg-white border border-gray-200 rounded-lg shadow-lg mt-1 px-4 py-3 text-sm text-gray-400">
                            Tidak ada mapel ditemukan.
                        </p>
                    </div>
                </div>

                {{-- Matrix Table --}}
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-gray-700 font-bold uppercase">
                            <tr>
                                <th class="px-4 py-3 text-left tracking-wider w-52">Mata Pelajaran</th>
                                <th class="px-4 py-3 text-left tracking-wider w-56">Guru Pengajar</th>
                                <th class="px-4 py-3 text-left tracking-wider">Jadwal Pertemuan</th>
                                <th class="px-4 py-3 text-center tracking-wider w-12">Hapus</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="(row, ri) in rows" :key="row.subject_id">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    {{-- Subject Name --}}
                                    <td class="px-4 py-3 align-top">
                                        <input type="hidden" :name="'mappings[' + row.subject_id + '][_label]'" :value="row.subject_name">
                                        <div class="font-medium text-gray-800" x-text="row.subject_name"></div>
                                        <div class="text-xs text-gray-400" x-text="row.subject_code"></div>
                                    </td>

                                    {{-- Teacher Dropdown --}}
                                    <td class="px-4 py-3 align-top">
                                        <select :name="'mappings[' + row.subject_id + '][teacher_id]'"
                                                x-model="row.teacher_id"
                                                class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm text-sm"
                                                required>
                                            <option value="">-- Pilih Guru --</option>
                                            <template x-for="t in row.teachers" :key="t.id">
                                                <option :value="t.id" x-text="t.name" :selected="t.id == row.teacher_id"></option>
                                            </template>
                                        </select>
                                        <p x-show="row.teachers.length === 0" class="text-xs text-amber-600 mt-1">
                                            ⚠ Belum ada guru yang ditugaskan untuk mapel ini. Assign guru terlebih dahulu di halaman Data Guru.
                                        </p>
                                    </td>

                                    {{-- Schedules --}}
                                    <td class="px-4 py-3 align-top">
                                        <template x-for="(sched, si) in row.schedules" :key="si">
                                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                                <select :name="'mappings[' + row.subject_id + '][schedules][' + si + '][day_of_week]'"
                                                        x-model="sched.day_of_week"
                                                        class="border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm text-sm w-28" required>
                                                    <option value="">Hari</option>
                                                    <template x-for="day in ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu']">
                                                        <option :value="day" x-text="day" :selected="sched.day_of_week === day"></option>
                                                    </template>
                                                </select>
                                                <input type="time" :name="'mappings[' + row.subject_id + '][schedules][' + si + '][start_time]'"
                                                       x-model="sched.start_time"
                                                       class="border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm text-sm w-28" required>
                                                <span class="text-gray-400 text-xs">s/d</span>
                                                <input type="time" :name="'mappings[' + row.subject_id + '][schedules][' + si + '][end_time]'"
                                                       x-model="sched.end_time"
                                                       class="border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm text-sm w-28" required>
                                                <input type="text" :name="'mappings[' + row.subject_id + '][schedules][' + si + '][room]'"
                                                       x-model="sched.room" placeholder="Ruang (opsional)"
                                                       class="border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm text-sm w-32">
                                                <button type="button" @click="row.schedules.splice(si, 1)"
                                                        class="text-red-400 hover:text-red-600 transition-colors" title="Hapus jadwal ini">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </div>
                                        </template>
                                        <button type="button" @click="row.schedules.push({day_of_week:'',start_time:'',end_time:'',room:''})"
                                                class="text-[#1a6341] hover:text-[#238054] text-xs font-medium flex items-center mt-1 transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            Tambah Jam
                                        </button>
                                    </td>

                                    {{-- Remove Row --}}
                                    <td class="px-4 py-3 align-top text-center">
                                        <button type="button" @click="rows.splice(ri, 1)"
                                                class="text-red-400 hover:text-red-600 transition-colors" title="Hapus mata pelajaran ini">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="rows.length === 0">
                                <td colspan="4" class="px-4 py-8 text-center text-gray-400">
                                    <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    Gunakan kotak pencarian di atas untuk menambahkan mata pelajaran ke jadwal kelas ini.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Submit --}}
                <div class="mt-5 flex justify-end">
                    <button type="submit"
                            class="bg-[#1a6341] hover:bg-[#238054] text-white px-6 py-3 rounded-lg shadow-sm transition-colors flex items-center text-sm font-bold">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simpan Jadwal {{ $selectedClass->name }}
                    </button>
                </div>
            </form>

            @else
            <div class="text-center py-16 text-gray-400">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <p class="text-lg font-semibold text-gray-500">Pilih Kelas untuk Mulai</p>
                <p class="text-sm mt-1">Gunakan filter di atas untuk memilih Tingkat, Jurusan, dan Rombel.</p>
            </div>
            @endif
            @endif

        </div>
    </div>

    @if($selectedClass && $academicYear)
    <script>
        function scheduleMatrix(initialRows) {
            return {
                rows: [],
                searchQuery: '',
                searchResults: [],
                showResults: false,

                init() {
                    // Load initial rows and fetch teacher lists for each
                    initialRows.forEach(r => {
                        this.rows.push({
                            subject_id:   r.subject_id,
                            subject_name: r.subject_name,
                            subject_code: r.subject_code,
                            teacher_id:   r.teacher_id,
                            schedules:    r.schedules,
                            teachers:     [],
                        });
                    });
                    this.rows.forEach(r => this.loadTeachers(r));
                },

                addRow(subj) {
                    if (this.rows.find(r => r.subject_id === subj.id)) {
                        alert(`"${subj.name}" sudah ada di daftar jadwal.`);
                        return;
                    }
                    const row = {
                        subject_id:   subj.id,
                        subject_name: subj.name,
                        subject_code: subj.code || '',
                        teacher_id:   '',
                        schedules:    [],
                        teachers:     [],
                    };
                    this.rows.push(row);
                    this.loadTeachers(row);
                },

                loadTeachers(rowObj) {
                    fetch(`{{ route('admin.schedules.teachers-for-subject') }}?subject_id=${rowObj.subject_id}`)
                        .then(r => r.json())
                        .then(data => { 
                            const idx = this.rows.findIndex(r => r.subject_id === rowObj.subject_id);
                            if (idx !== -1) {
                                this.rows[idx].teachers = data;
                            } else {
                                rowObj.teachers = data;
                            }
                        });
                },

                searchSubjects() {
                    if (this.searchQuery.length < 1) { this.searchResults = []; return; }
                    const exceptIds = this.rows.map(r => r.subject_id);
                    const params = new URLSearchParams({ q: this.searchQuery });
                    exceptIds.forEach(id => params.append('except[]', id));
                    fetch(`{{ route('admin.schedules.search-subjects') }}?${params}`)
                        .then(r => r.json())
                        .then(data => { this.searchResults = data; this.showResults = true; });
                },
            };
        }
    </script>
    @endif
</x-app-layout>
