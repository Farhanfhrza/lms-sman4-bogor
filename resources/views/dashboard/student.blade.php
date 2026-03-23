<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Dashboard Siswa') }}
        </h2>
    </x-slot>

    <!-- Content Wrapper -->
    <div class="space-y-6">
        
        <!-- Welcome Banner -->
        <div class="bg-gradient-to-r from-[#1a6341] to-[#238054] rounded-xl shadow-lg p-6 md:p-8 text-white relative overflow-hidden">
            <div class="relative z-10">
                <h1 class="text-3xl font-extrabold mb-2">Halo, {{ Auth::user()->full_name ?? Auth::user()->name }}! 👋</h1>
                <p class="text-green-100 text-lg">
                    Selamat datang di LMS SMAN 4 Bogor.
                    @if($academicYear) 
                        Tahun Ajaran Aktif: <span class="font-bold">{{ $academicYear->name }}</span>
                    @else
                        Sistem sedang dalam konfigurasi Tahun Ajaran.
                    @endif
                </p>
                <div class="mt-6 inline-block bg-white text-[#1a6341] rounded-lg px-4 py-2 font-bold shadow-sm">
                    @if($studentClass && $studentClass->schoolClass)
                        Kelas Saat Ini: {{ $studentClass->schoolClass->name }} ({{ $studentClass->schoolClass->major }})
                    @else
                        Anda belum tergabung dalam kelas mana pun. Hubungi Tata Usaha / Admin.
                    @endif
                </div>
            </div>
            <!-- Decorative circle -->
            <div class="absolute -right-20 -bottom-20 w-64 h-64 rounded-full bg-white opacity-10 pointer-events-none"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Main Column (Left) -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Quick Access: Mata Pelajaran Saya -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-bold text-lg text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            Mata Pelajaran Anda ({{ $classSubjects->count() }})
                        </h3>
                        <a href="{{ route('courses.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">Lihat Semua &rarr;</a>
                    </div>
                    
                    <div class="p-5">
                        @if($classSubjects->isEmpty())
                            <div class="text-center py-8 text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                <p>Belum ada mata pelajaran yang dijadwalkan untuk kelas Anda.</p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($classSubjects->take(6) as $cs)
                                    <a href="{{ route('courses.show', $cs->slug) }}" class="group block border border-gray-200 rounded-lg p-4 hover:border-[#1a6341] hover:shadow-md transition-all bg-gray-50 hover:bg-white relative overflow-hidden">
                                        <div class="absolute top-0 left-0 w-1 h-full bg-[#1a6341] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                        <div class="flex items-start">
                                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-[#1a6341] font-bold mr-3 flex-shrink-0 overflow-hidden border border-gray-200">
                                                <img src="{{ $cs->subject->cover_image_url }}" alt="{{ $cs->subject->name ?? 'Unknown' }}" class="w-full h-full object-cover">
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-gray-900 group-hover:text-[#1a6341] line-clamp-1" title="{{ $cs->subject->name ?? 'Unknown' }}">
                                                    {{ $cs->subject->name ?? 'Unknown' }}
                                                </h4>
                                                <p class="text-xs text-gray-500 mt-1 flex items-center">
                                                    @if($cs->teacher && $cs->teacher->user)
                                                        <img src="{{ $cs->teacher->user->profile_photo_url }}" class="w-4 h-4 rounded-full mr-1 object-cover">
                                                    @else
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                    @endif
                                                    {{ $cs->teacher->user->full_name ?? 'N/A' }}
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tugas yang Belum Dikerjakan -->
                @php
                    $mappedAssignments = $pendingAssignments->map(function($a) {
                        return [
                            'title' => $a->title,
                            'subject' => $a->section->classSubject->subject->name ?? 'Mata Pelajaran',
                            'due_date' => $a->due_date ? \Carbon\Carbon::parse($a->due_date)->format('Y-m-d H:i:s') : null,
                            'due_human' => $a->due_date ? \Carbon\Carbon::parse($a->due_date)->diffForHumans() : 'Tidak ada tenggat',
                            'is_overdue' => $a->due_date ? \Carbon\Carbon::parse($a->due_date)->isPast() : false,
                            'url' => route('assignments.show', $a->slug ?? $a->id)
                        ];
                    })->values()->all();
                @endphp
                <div x-data='{
                        filterTime: "all",
                        assignments: @json($mappedAssignments),
                        
                        get filteredAssignments() {
                            if (this.filterTime === "all") return this.assignments;
                            
                            const now = new Date();
                            const todayStart = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                            
                            return this.assignments.filter(a => {
                                if (!a.due_date) return false;
                                
                                const due = new Date(a.due_date);
                                // Hitung selisih hari dari mulai hari ini
                                const diffTime = due.getTime() - todayStart.getTime();
                                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                                
                                if (this.filterTime === "today") return diffDays >= 0 && diffDays <= 1;
                                if (this.filterTime === "7days") return diffDays >= 0 && diffDays <= 7;
                                if (this.filterTime === "30days") return diffDays >= 0 && diffDays <= 30;
                                return true;
                            });
                        }
                    }' class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mt-6">
                    <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:justify-between sm:items-center">
                        <h3 class="font-bold text-lg text-gray-800 flex items-center mb-3 sm:mb-0">
                            <svg class="w-5 h-5 mr-2 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Tugas Belum Dikerjakan
                            <span class="ml-2 bg-rose-100 text-rose-600 px-2 py-0.5 rounded-full text-xs font-bold" x-text="assignments.length"></span>
                        </h3>
                        <!-- Filter Toggle -->
                        <div class="flex space-x-1 border border-gray-200 rounded-lg p-1 bg-gray-50">
                            <button @click="filterTime = 'today'" :class="{'bg-white shadow text-rose-600 font-bold': filterTime === 'today', 'text-gray-500 hover:text-gray-700': filterTime !== 'today'}" class="px-2 py-1 text-[11px] sm:text-xs font-semibold rounded-md transition-all">Hari Ini</button>
                            <button @click="filterTime = '7days'" :class="{'bg-white shadow text-rose-600 font-bold': filterTime === '7days', 'text-gray-500 hover:text-gray-700': filterTime !== '7days'}" class="px-2 py-1 text-[11px] sm:text-xs font-semibold rounded-md transition-all">7 Hari</button>
                            <button @click="filterTime = '30days'" :class="{'bg-white shadow text-rose-600 font-bold': filterTime === '30days', 'text-gray-500 hover:text-gray-700': filterTime !== '30days'}" class="px-2 py-1 text-[11px] sm:text-xs font-semibold rounded-md transition-all">30 Hari</button>
                            <button @click="filterTime = 'all'" :class="{'bg-white shadow text-rose-600 font-bold': filterTime === 'all', 'text-gray-500 hover:text-gray-700': filterTime !== 'all'}" class="px-2 py-1 text-[11px] sm:text-xs font-semibold rounded-md transition-all">Semua</button>
                        </div>
                    </div>
                    
                    <div class="p-0">
                        <template x-if="filteredAssignments.length === 0">
                            <div class="p-8 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-green-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="font-medium text-gray-700">Mantap!</p>
                                <p class="text-sm">Tidak ada tugas tertunda untuk rentang waktu ini.</p>
                            </div>
                        </template>
                        <template x-if="filteredAssignments.length > 0">
                            <ul class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
                                <template x-for="task in filteredAssignments" :key="task.url">
                                    <li class="p-4 hover:bg-gray-50 transition-colors">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <h4 class="font-bold text-gray-800 text-sm hover:text-rose-600 transition-colors cursor-pointer" x-text="task.title"></h4>
                                                <p class="text-xs font-medium text-blue-600 mt-1" x-text="task.subject"></p>
                                                <div class="flex items-center mt-2">
                                                    <svg class="w-3 h-3 mr-1" :class="task.is_overdue ? 'text-red-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    <span class="text-[11px]" :class="task.is_overdue ? 'text-red-600 font-bold' : 'text-gray-500'" x-text="task.is_overdue ? 'Terlambat: ' + task.due_human : 'Tenggat: ' + task.due_human"></span>
                                                </div>
                                            </div>
                                            <div class="ml-4 flex-shrink-0 mt-1">
                                                <a :href="task.url" class="inline-flex items-center px-3 py-1.5 bg-rose-50 text-rose-600 rounded-md text-xs font-bold hover:bg-rose-100 transition-colors border border-rose-200">
                                                    Kerjakan
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                        </template>
                    </div>
                </div>

            </div> <!-- Penutup Main Column (Left) -->

            <!-- Side Column (Right) -->
            <div class="space-y-6">
                
                <!-- Absensi Terbuka / Active Check-ins -->
                @if(isset($activeMeetings) && $activeMeetings->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden relative border-t-4 border-green-500">
                    <div class="p-5 border-b border-gray-100 bg-green-50">
                        <h3 class="font-bold text-lg text-green-800 flex items-center justify-between">
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Isi Absensi Sekarang
                            </span>
                            <span class="bg-green-600 text-white text-xs px-2 py-1 rounded-full font-bold shadow-sm">
                                {{ $activeMeetings->count() }} Terbuka
                            </span>
                        </h3>
                    </div>
                    <div class="p-0">
                        <ul class="divide-y divide-gray-100">
                            @foreach($activeMeetings as $meeting)
                            <li class="p-4 hover:bg-green-50 transition-colors border-l-4 border-transparent hover:border-[#1a6341]">
                                <a href="{{ route('student.attendances.show', $meeting->classSubject->slug ?? $meeting->class_subject_id) }}" class="flex justify-between items-center group">
                                    <div class="flex-1">
                                        <h4 class="font-bold text-gray-800 text-sm group-hover:text-[#1a6341] transition-colors">{{ $meeting->classSubject->subject->name ?? 'Mata Pelajaran' }}</h4>
                                        <p class="text-xs text-gray-500 mt-0.5">Materi: {{ $meeting->title }}</p>
                                        <div class="text-[11px] mt-1 flex items-center text-rose-600 font-semibold">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Ditutup pukul {{ \Carbon\Carbon::parse($meeting->end_time)->format('H:i') }}
                                        </div>
                                    </div>
                                    <div class="bg-green-100 text-green-700 p-2 rounded-lg group-hover:bg-[#1a6341] group-hover:text-white transition-colors ml-3 cursor-pointer">
                                        Absen
                                    </div>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif

                <!-- Jadwal Hari Ini -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden relative border-t-4 border-yellow-400">
                    <div class="p-5 border-b border-gray-100">
                        <h3 class="font-bold text-lg text-gray-800 flex items-center justify-between">
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Jadwal Hari Ini
                            </span>
                            @php
                                $daysIndo = ['Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'];
                            @endphp
                            <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full font-bold">
                                {{ $daysIndo[now()->format('l')] ?? now()->format('l') }}
                            </span>
                        </h3>
                    </div>

                    <div class="p-0">
                        @if($todaySchedules->isEmpty())
                            <div class="p-6 text-center text-gray-500">
                                <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-state-2130362-1800926.png" class="h-24 mx-auto mb-3 opacity-60 mix-blend-multiply" alt="No schedule">
                                <p class="text-sm">Hore! Tidak ada jadwal pelajaran di kelas Anda hari ini.</p>
                            </div>
                        @else
                            <ul class="divide-y divide-gray-100">
                                @foreach($todaySchedules as $sched)
                                <li class="p-4 hover:bg-gray-50 transition-colors border-l-4 border-transparent hover:border-[#1a6341]">
                                    <div class="flex justify-between items-start">
                                        <div class="w-14 text-center mr-3 mt-1 flex-shrink-0">
                                            <div class="text-xs font-bold text-gray-800">{{ \Carbon\Carbon::parse($sched->start_time)->format('H:i') }}</div>
                                            <div class="text-xs text-gray-400">s/d</div>
                                            <div class="text-xs font-bold text-gray-500">{{ \Carbon\Carbon::parse($sched->end_time)->format('H:i') }}</div>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-bold text-[#1a6341] text-sm">{{ $sched->classSubject->subject->name ?? 'Unknown' }}</h4>
                                            <p class="text-xs text-gray-600 mt-1">{{ $sched->classSubject->teacher->user->full_name ?? 'Unknown Teacher' }}</p>
                                            <p class="text-[10px] text-gray-400 mt-0.5 inline-block bg-gray-100 rounded px-1">{{ $sched->room ?? 'Tanpa Ruangan' }}</p>
                                        </div>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

            </div>
            
        </div>
    </div>
</x-app-layout>
