<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Dashboard Guru') }}
        </h2>
    </x-slot>

    <!-- Content Wrapper -->
    <div class="space-y-6">
        
        <!-- Welcome Banner -->
        <div class="bg-gradient-to-r from-blue-700 to-indigo-800 rounded-xl shadow-lg p-6 md:p-8 text-white relative overflow-hidden">
            <div class="relative z-10">
                <h1 class="text-3xl font-extrabold mb-2">Halo, {{ Auth::user()->full_name ?? Auth::user()->name }}! 👋</h1>
                <p class="text-blue-100 text-lg">
                    Selamat datang kembali di LMS SMAN 4 Bogor.
                    @if($academicYear) 
                        Tahun Ajaran Aktif: <span class="font-bold">{{ $academicYear->name }}</span>
                    @else
                        Sistem sedang dalam konfigurasi Tahun Ajaran.
                    @endif
                </p>
            </div>
            <!-- Decorative circle -->
            <div class="absolute -right-20 -bottom-20 w-64 h-64 rounded-full bg-white opacity-10 pointer-events-none"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Main Column (Left) -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Quick Stats for Teachers -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col items-center justify-center text-center">
                        <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <h4 class="text-3xl font-extrabold text-gray-800">{{ $totalClasses }}</h4>
                        <p class="text-sm font-medium text-gray-500 mt-1">Kelas Diampu</p>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col items-center justify-center text-center">
                        <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <h4 class="text-3xl font-extrabold text-gray-800">{{ $totalStudents }}</h4>
                        <p class="text-sm font-medium text-gray-500 mt-1">Total Siswa</p>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col items-center justify-center text-center">
                        <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <h4 class="text-3xl font-extrabold text-gray-800">{{ $classSubjects->count() }}</h4>
                        <p class="text-sm font-medium text-gray-500 mt-1">Mata Pelajaran (Course)</p>
                    </div>
                </div>

                <!-- Kurikulum / Mata Pelajaran yang Diampu -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-bold text-lg text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            Mata Pelajaran yang Anda Ampu
                        </h3>
                        <a href="{{ route('courses.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">Manajemen Kelas &rarr;</a>
                    </div>
                    
                    <div class="p-5">
                        @if($classSubjects->isEmpty())
                            <div class="text-center py-8 text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                <p>Belum ada mata pelajaran yang ditugaskan kepada Anda pada TP ini.</p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($classSubjects->take(6) as $cs)
                                    <a href="{{ route('courses.show', $cs->slug) }}" class="group block border border-gray-200 rounded-lg p-4 hover:border-indigo-500 hover:shadow-md transition-all bg-gray-50 hover:bg-white relative overflow-hidden">
                                        <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                        <div class="flex items-start">
                                            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold mr-3 flex-shrink-0">
                                                {{ substr($cs->subject->name ?? '?', 0, 1) }}
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-gray-900 group-hover:text-indigo-600 line-clamp-1" title="{{ $cs->subject->name ?? 'Unknown' }}">
                                                    {{ $cs->subject->name ?? 'Unknown' }}
                                                </h4>
                                                <p class="text-xs font-bold text-indigo-500 mt-1 flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                                    Kelas: {{ $cs->schoolClass->name ?? 'Unknown Class' }}
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            <!-- Side Column (Right) -->
            <div class="space-y-6">
                
                <!-- Jadwal Mengajar Hari Ini -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden relative border-t-4 border-yellow-400">
                    <div class="p-5 border-b border-gray-100">
                        <h3 class="font-bold text-lg text-gray-800 flex items-center justify-between">
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Jadwal Mengajar Ini
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
                                <p class="text-sm font-medium">Anda tidak memiliki jadwal mengajar hari ini.</p>
                                <p class="text-xs text-gray-400 mt-1">Gunakan waktu ini untuk memeriksa tugas.</p>
                            </div>
                        @else
                            <ul class="divide-y divide-gray-100">
                                @foreach($todaySchedules as $sched)
                                <li class="p-4 hover:bg-gray-50 transition-colors border-l-4 border-transparent hover:border-indigo-500">
                                    <div class="flex justify-between items-start">
                                        <div class="w-14 text-center mr-3 mt-1 flex-shrink-0">
                                            <div class="text-xs font-bold text-gray-800">{{ \Carbon\Carbon::parse($sched->start_time)->format('H:i') }}</div>
                                            <div class="text-xs text-gray-400">s/d</div>
                                            <div class="text-xs font-bold text-gray-500">{{ \Carbon\Carbon::parse($sched->end_time)->format('H:i') }}</div>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-bold text-indigo-700 text-sm">{{ $sched->classSubject->subject->name ?? 'Unknown' }}</h4>
                                            <p class="text-xs font-bold text-gray-700 mt-1">Kelas: {{ $sched->classSubject->schoolClass->name ?? 'Unknown' }}</p>
                                            <p class="text-[10px] text-gray-500 mt-1 flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                                Ruang: <span class="ml-1 bg-gray-100 rounded px-1">{{ $sched->room ?? '-' }}</span>
                                            </p>
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
