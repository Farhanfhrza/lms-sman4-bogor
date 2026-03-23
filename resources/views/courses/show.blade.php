<x-app-layout>
    <div class="pb-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Breadcrumb -->
            <div class="py-4">
                <x-breadcrumb :items="$breadcrumbs" />
            </div>

            <!-- Banner Header dengan Background Image -->
            <div class="relative rounded-lg overflow-hidden mb-6 shadow-lg" style="height: 250px;">
                <!-- Background Image dengan Overlay -->
                <div class="absolute inset-0 bg-gradient-to-r from-[#1a6341]/95 to-[#2a8d5f]/95">
                    <img src="{{ $course->subject->cover_image_url }}" 
                         alt="Background" 
                         class="w-full h-full object-cover mix-blend-overlay opacity-40">
                </div>
                
                <!-- Content -->
                <div class="relative z-10 h-full flex flex-col justify-center px-8 md:px-12">
                    <h1 class="text-4xl md:text-5xl font-bold text-white mb-3">
                        {{ $course->subject->name ?? 'Mata Pelajaran' }}
                    </h1>
                    <p class="text-xl text-green-100 font-medium">
                        {{ $course->schoolClass->name ?? 'Kelas' }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Informasi Umum (from class_subjects.general_info) -->
                    @if($course->general_info)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Informasi Umum</h2>
                        
                        <div class="prose prose-sm max-w-none text-gray-700">
                            {!! $course->general_info !!}
                        </div>

                        <!-- Pokok Bahasan (list all BAB titles) -->
                        @if($course->sections && $course->sections->count() > 0)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h3 class="font-semibold text-gray-900 mb-3">Pokok Bahasan Mata Pelajaran</h3>
                            <ul class="list-decimal list-inside text-gray-700 space-y-1.5 ml-2">
                                @foreach($course->sections as $section)
                                    <li>{{ $section->title }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Sections (BAB) - Linear Layout -->
                    @forelse($course->sections ?? [] as $section)
                        <div class="bg-white rounded-lg shadow-md p-6" id="section-{{ $section->id }}">
                            <!-- Section Header -->
                            <div class="mb-4">
                                <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $section->title }}</h2>
                                @if($section->description)
                                    <p class="text-gray-600 leading-relaxed">{{ $section->description }}</p>
                                @endif
                            </div>

                            <!-- Materials -->
                            <div class="space-y-3">
                                @forelse($section->materials ?? [] as $index => $material)
                                    <div class="flex items-start bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors border border-gray-200">
                                        <!-- Icon -->
                                        <div class="flex-shrink-0 mr-4">
                                            @if($material->content_type === 'video')
                                                <div class="w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"></path>
                                                    </svg>
                                                </div>
                                            @elseif($material->content_type === 'pdf')
                                                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            @else
                                                <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Content -->
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-semibold text-gray-900 mb-1">{{ $material->title }}</h4>
                                            @if($material->description)
                                                <p class="text-sm text-gray-600">{{ Str::limit($material->description, 80) }}</p>
                                            @endif
                                        </div>

                                        <!-- Action Button -->
                                        <a href="{{ route('materials.show', $material) }}" 
                                           class="flex-shrink-0 ml-4 px-4 py-2 bg-[#1a6341] text-white rounded-md hover:bg-[#144d32] transition-colors text-sm font-medium">
                                            View
                                        </a>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-sm italic">Belum ada materi</p>
                                @endforelse

                                <!-- Assignments -->
                                @forelse($section->assignments ?? [] as $assignment)
                                    <div class="flex items-start bg-orange-50 rounded-lg p-4 hover:bg-orange-100 transition-colors border border-orange-200">
                                        <!-- Icon -->
                                        <div class="flex-shrink-0 mr-4">
                                            <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                        </div>

                                        <!-- Content -->
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-semibold text-gray-900 mb-1">{{ $assignment->title }}</h4>
                                            @if($assignment->description)
                                                <p class="text-sm text-gray-600 mb-2">{{ Str::limit($assignment->description, 80) }}</p>
                                            @endif
                                            @if($assignment->due_date)
                                                <p class="text-xs text-gray-500">
                                                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Batas: {{ $assignment->due_date->format('d M Y, H:i') }}
                                                </p>
                                            @endif
                                        </div>

                                        <!-- Action Button -->
                                        <a href="{{ route('assignments.show', $assignment) }}" 
                                           class="flex-shrink-0 ml-4 px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 transition-colors text-sm font-medium">
                                            Submit
                                        </a>
                                    </div>
                                @empty
                                @endforelse

                                <!-- Quizzes -->
                                @forelse($section->quizzes ?? [] as $quiz)
                                    <div class="flex items-start bg-purple-50 rounded-lg p-4 hover:bg-purple-100 transition-colors border border-purple-200">
                                        <!-- Icon -->
                                        <div class="flex-shrink-0 mr-4">
                                            <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                        </div>

                                        <!-- Content -->
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-semibold text-gray-900 mb-1">{{ $quiz->title }}</h4>
                                            @if($quiz->description)
                                                <p class="text-sm text-gray-600 mb-2">{{ Str::limit(strip_tags($quiz->description), 80) }}</p>
                                            @endif
                                            <div class="flex items-center gap-4 text-xs text-gray-500">
                                                @if($quiz->time_limit)
                                                    <span>
                                                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        {{ $quiz->time_limit }} menit
                                                    </span>
                                                @endif
                                                @if($quiz->end_at)
                                                    <span>Batas: {{ $quiz->end_at->format('d M Y') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Action Button -->
                                        @php
                                            $isAvailable = $quiz->end_at ? now()->isBefore($quiz->end_at) : true;
                                        @endphp
                                        @if($isAvailable)
                                            <a href="{{ route('student.quiz.show', [$course, $quiz]) }}" 
                                               class="flex-shrink-0 ml-4 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors text-sm font-medium">
                                                Mulai Kuis
                                            </a>
                                        @else
                                            <span class="flex-shrink-0 ml-4 px-4 py-2 bg-gray-300 text-gray-600 rounded-md text-sm font-medium cursor-not-allowed">
                                                Ditutup
                                            </span>
                                        @endif
                                    </div>
                                @empty
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-lg shadow-md p-8 text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-gray-500">Belum ada konten pembelajaran</p>
                        </div>
                    @endforelse

                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    
                    <!-- Teacher Info -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="font-bold text-gray-900 mb-4">Informasi Pengajar</h3>
                        <div class="flex items-center mb-4">
                            <div class="w-16 h-16 rounded-full overflow-hidden bg-gray-200 mr-4 shadow-sm">
                                <img src="{{ $course->teacher->user->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($course->teacher->user->full_name ?? 'Teacher') . '&background=1a6341&color=fff&size=128' }}" 
                                     alt="Teacher" 
                                     class="w-full h-full object-cover">
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $course->teacher->user->full_name ?? 'Guru Pengajar' }}</p>
                                <p class="text-sm text-gray-500">{{ $course->teacher->user->email ?? '' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Shortcut Absensi -->
                    @role('student')
                    <div class="bg-gradient-to-br from-[#1a6341] to-[#238054] rounded-lg shadow-md p-6 text-white relative overflow-hidden group">
                        <div class="absolute -right-4 -top-4 w-24 h-24 rounded-full bg-white opacity-10 group-hover:scale-110 transition-transform duration-500"></div>
                        <div class="relative z-10">
                            <h3 class="font-bold text-lg mb-2 flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Kehadiran Anda
                            </h3>
                            <p class="text-green-100 text-sm mb-4">Pastikan Anda selalu mengisi daftar hadir pada setiap pertemuan.</p>
                            <a href="{{ route('student.attendances.show', $course->slug) }}" class="inline-flex w-full items-center justify-center px-4 py-2.5 bg-white text-[#1a6341] font-bold rounded-md hover:bg-gray-50 transition-colors shadow-sm">
                                Cek & Isi Absensi
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>
                    @endrole

                    <!-- Progress (for students) -->
                    @role('student')
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="font-bold text-gray-900 mb-4">Progress Belajar</h3>
                        <div class="text-center mb-4">
                            <div class="text-5xl font-bold text-[#1a6341] mb-2">{{ number_format($progress, 0) }}%</div>
                            <p class="text-sm text-gray-500">Selesai</p>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-[#1a6341] rounded-full h-3 transition-all duration-500" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>
                    @endrole

                    <!-- Classmates Widget -->
                    @role('student')
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-[#1a6341]" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                            </svg>
                            Teman Sekelas
                        </h3>
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            @forelse($classmates->take(10) as $classmate)
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-200 mr-3 border border-gray-100 shadow-sm">
                                        <img src="{{ $classmate->profile_photo_url }}" 
                                             alt="{{ $classmate->full_name ?? $classmate->name }}" 
                                             class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $classmate->full_name ?? $classmate->name }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 italic">Belum ada teman sekelas</p>
                            @endforelse
                            @if($classmates->count() > 10)
                                <button class="text-[#1a6341] hover:text-[#144d32] text-sm font-medium w-full text-left">
                                    + {{ $classmates->count() - 10 }} lainnya
                                </button>
                            @endif
                        </div>
                    </div>
                    @endrole

                    <!-- Academic Info -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="font-bold text-gray-900 mb-4">Informasi Akademik</h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-500">Kelas</p>
                                <p class="font-medium text-gray-900">{{ $course->schoolClass->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Tahun Ajaran</p>
                                <p class="font-medium text-gray-900">{{ $course->academicYear->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Total BAB</p>
                                <p class="font-medium text-gray-900">{{ $course->sections->count() ?? 0 }} bab</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>


