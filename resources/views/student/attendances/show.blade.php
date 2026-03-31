<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            Absensi - {{ $course->subject->name ?? 'Course' }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        
        <!-- Breadcrumbs -->
        <nav class="flex text-sm text-gray-500 mb-6 font-medium" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('student.attendances.index') }}" class="hover:text-[#1a6341] transition-colors">Absensi</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        <span class="text-gray-700 ml-1 md:ml-2">{{ $course->subject->name ?? 'Course' }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif
        
        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
        @endif

        <div class="mb-6">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Absensi</h1>
            <p class="text-xl font-bold text-gray-800 mt-1">{{ $course->subject->name ?? 'Course' }} - {{ $course->schoolClass->name ?? 'Class' }}</p>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:flex-wrap gap-3 mb-6">
            <button class="w-full sm:w-auto bg-[#59b88b] text-white px-4 py-2 rounded-md font-medium shadow-sm hover:bg-[#4a9f77] transition-colors">All</button>
            <input type="text" placeholder="Search" class="border-gray-300 rounded-md shadow-sm focus:border-[#1a6341] focus:ring-[#1a6341] text-sm py-2 px-3 pl-8 w-full sm:w-auto">
            <button class="w-full sm:w-auto justify-center bg-[#59b88b] text-white px-4 py-2 rounded-md font-medium flex items-center shadow-sm hover:bg-[#4a9f77] transition-colors">
                Sort by Date
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
        </div>

        <div class="space-y-4">
            @forelse ($meetings as $meeting)
                @php
                    $now = now();
                    $meetingDate = \Carbon\Carbon::parse($meeting->meeting_date);
                    $startTime = \Carbon\Carbon::parse($meeting->start_time)->setDateFrom($meetingDate);
                    $endTime = \Carbon\Carbon::parse($meeting->end_time)->setDateFrom($meetingDate);
                    
                    // Allow check-in if within time window
                    $isActive = $now->between($startTime, $endTime);
                    $attendance = $attendances->get($meeting->id);
                    
                    // Lock if outside time window OR if student explicitly submitted attendance himself
                    $isLocked = !$isActive || ($attendance && $attendance->recorded_by === Auth::user()->id);
                    
                    $studentStatus = $attendance ? $attendance->status : 'Alpha';
                @endphp

                @if(!$isLocked)
                    <!-- Active, Unlocked Meeting (Ready for Self Check-in) -->
                    <div class="bg-white border border-[#1a6341] rounded-xl p-4 sm:p-6 shadow-sm">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-3 md:gap-0">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6 w-full md:w-auto">
                                <div class="text-lg sm:text-xl font-bold text-gray-800 w-full sm:w-32 border-b sm:border-b-0 border-gray-200 pb-2 sm:pb-0">
                                    {{ \Carbon\Carbon::parse($meeting->meeting_date)->format('d M Y') }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 text-base sm:text-lg">Pertemuan {{ $loop->iteration }}</h4>
                                    <p class="text-gray-600">{{ $meeting->title }}</p>
                                </div>
                            </div>
                            <div class="text-[#1a6341] font-bold text-xs sm:text-sm bg-green-50 px-3 py-1.5 rounded border border-green-200 self-start md:self-auto uppercase tracking-wide">
                                SEDANG BERLANGSUNG
                            </div>
                        </div>

                        <form action="{{ route('student.attendances.submit', [$course, $meeting]) }}" method="POST" class="flex flex-col sm:flex-row gap-3 sm:space-x-4 mt-4">
                            @csrf
                            <button type="submit" name="status" value="Hadir" class="w-full sm:flex-1 bg-[#1a6341] hover:bg-[#155034] text-white font-bold py-3 px-4 rounded-xl transition-colors shadow-sm text-center">
                                Hadir
                            </button>
                            <button type="submit" name="status" value="Izin" class="w-full sm:flex-1 bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-4 rounded-xl transition-colors shadow-sm text-center">
                                Izin
                            </button>
                            <button type="submit" name="status" value="Sakit" class="w-full sm:flex-1 bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-4 rounded-xl transition-colors shadow-sm text-center">
                                Sakit
                            </button>
                        </form>
                        <p class="text-xs text-gray-500 mt-3 text-center">Absensi dibuka hingga {{ \Carbon\Carbon::parse($meeting->end_time)->format('H:i') }}</p>
                    </div>

                @else
                    <!-- Locked Meeting (Past or Already Submitted) -->
                    @php
                        $bgClass = 'bg-[#cdcecf]'; // Default grey outline/background roughly matching mockup
                        $statusColor = 'bg-gray-600'; // Default chip color
                        
                        if ($studentStatus == 'Hadir') {
                            $statusColor = 'bg-[#1a6341]';
                        } elseif ($studentStatus == 'Alpha') {
                            $statusColor = 'bg-[#d63d3d]'; // Red
                        } else {
                            $statusColor = 'bg-gray-600'; // Izin/Sakit
                        }
                    @endphp
                    <div class="{{ $bgClass }} rounded-xl p-4 sm:p-5 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6">
                            <div class="text-lg sm:text-xl font-bold text-gray-800 sm:w-32 border-b sm:border-b-0 border-gray-400 pb-2 sm:pb-0">
                                {{ \Carbon\Carbon::parse($meeting->meeting_date)->format('d M Y') }}
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 text-base sm:text-lg">Materi: {{ $meeting->title }}</h4>
                                <p class="text-gray-700 text-sm mt-1">Waktu: {{ \Carbon\Carbon::parse($meeting->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($meeting->end_time)->format('H:i') }}</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between sm:justify-start gap-3 sm:space-x-4 mt-2 md:mt-0 w-full md:w-auto">
                            <div class="{{ $statusColor }} text-white font-bold py-2.5 px-4 sm:px-12 rounded-lg text-center flex-1 md:min-w-[140px] shadow-sm text-sm sm:text-base">
                                {{ $studentStatus }}
                            </div>
                            <!-- Lock Icon -->
                            <div class="bg-gray-600 text-white p-2.5 rounded-lg shadow-sm flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="bg-white p-6 rounded-lg shadow-sm text-center text-gray-500 border border-gray-200">
                    Belum ada pertemuan pada mata pelajaran ini.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
