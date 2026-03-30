<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Manajemen Absensi') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        
        <div class="flex items-center justify-between mb-4 mt-2">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Manajemen Absensi</h1>
        </div>

        <!-- Filters (Mockup) -->
        <div class="flex flex-wrap items-center gap-3 mb-6">
            <button class="bg-[#59b88b] text-white px-4 py-2 rounded-md font-medium shadow-sm hover:bg-[#4a9f77] transition-colors whitespace-nowrap">All</button>
            <input type="text" placeholder="Search" class="border-gray-300 rounded-md shadow-sm focus:border-[#1a6341] focus:ring-[#1a6341] text-sm py-2 px-3 w-full sm:w-auto">
            <button class="bg-[#59b88b] text-white px-4 py-2 rounded-md font-medium flex items-center shadow-sm hover:bg-[#4a9f77] transition-colors whitespace-nowrap">
                Sort by course name
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
        </div>

        <!-- Course Cards List -->
        <div class="flex flex-col space-y-4">
            @forelse ($courses as $course)
                <a href="{{ route('student.attendances.show', $course) }}" class="block group hover:shadow-md transition-shadow">
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-sm hover:bg-white hover:border-gray-300 transition-colors">
                        
                        <div class="flex items-center space-x-4 md:space-x-5">
                            <div class="bg-[#59b88b] text-white p-3 rounded-lg shadow-sm flex-shrink-0">
                                <!-- Classroom Icon -->
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                            
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">{{ $course->subject->name ?? 'Course' }}</h3>
                                <p class="text-gray-600 text-sm mt-1">{{ $course->schoolClass->name ?? 'Class' }}</p>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 md:gap-6 mt-2 md:mt-0">
                            <div class="bg-gray-200 text-gray-700 px-3 py-2 md:px-4 md:py-2 rounded-lg font-medium text-xs md:text-sm flex flex-wrap gap-x-3 gap-y-1 md:gap-x-3">
                                <span>Pertemuan: {{ $course->stats['pertemuan'] }}</span>
                                <span>Hadir: {{ $course->stats['hadir'] }}</span>
                                <span>Izin: {{ $course->stats['izin'] }}</span>
                                <span>Sakit: {{ $course->stats['sakit'] }}</span>
                                <span>Alpha: {{ $course->stats['alpha'] }}</span>
                            </div>

                            <!-- Percentage Box -->
                            @php
                                $percentColor = $course->percentage >= 75 ? 'bg-[#1a6341]' : ($course->percentage >= 50 ? 'bg-yellow-500' : 'bg-red-500');
                            @endphp
                            <div class="{{ $percentColor }} text-white font-bold px-4 py-2 sm:py-3 rounded-lg min-w-[60px] text-center text-sm md:text-lg shadow-sm self-end sm:self-auto w-fit sm:w-auto">
                                {{ $course->percentage }}%
                            </div>
                        </div>

                    </div>
                </a>
            @empty
                <div class="bg-white p-6 rounded-lg shadow-sm text-center text-gray-500 border border-gray-200">
                    Anda belum terdaftar di kelas manapun.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
