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
        <div class="flex flex-col sm:flex-row sm:items-center sm:flex-wrap gap-3 mb-6">
            <button class="w-full sm:w-auto bg-[#59b88b] text-white px-4 py-2 rounded-md font-medium shadow-sm hover:bg-[#4a9f77] transition-colors whitespace-nowrap">All</button>
            <input type="text" placeholder="Search" class="border-gray-300 rounded-md shadow-sm focus:border-[#1a6341] focus:ring-[#1a6341] text-sm py-2 px-3 w-full sm:w-auto">
            <button class="w-full sm:w-auto justify-center bg-[#59b88b] text-white px-4 py-2 rounded-md font-medium flex items-center shadow-sm hover:bg-[#4a9f77] transition-colors whitespace-nowrap">
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

                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 md:gap-6 mt-4 md:mt-0 w-full sm:w-auto">
                            <div class="bg-gray-100 sm:bg-gray-200 text-gray-700 p-3 md:px-4 md:py-2 rounded-lg font-medium text-xs md:text-sm grid grid-cols-2 lg:flex lg:flex-wrap gap-2 lg:gap-x-3 w-full sm:w-auto">
                                <span class="col-span-2 lg:col-span-1 border-b border-gray-200 lg:border-none pb-1 lg:pb-0 mb-1 lg:mb-0 text-center lg:text-left">Pertemuan: {{ $course->stats['pertemuan'] }}</span>
                                <span class="text-center lg:text-left">Hadir: {{ $course->stats['hadir'] }}</span>
                                <span class="text-center lg:text-left">Sakit: {{ $course->stats['sakit'] }}</span>
                                <span class="text-center lg:text-left">Izin: {{ $course->stats['izin'] }}</span>
                                <span class="text-center lg:text-left">Alpha: {{ $course->stats['alpha'] }}</span>
                            </div>

                            <!-- Percentage Box -->
                            @php
                                $percentColor = $course->percentage >= 75 ? 'bg-[#1a6341]' : ($course->percentage >= 50 ? 'bg-yellow-500' : 'bg-red-500');
                            @endphp
                            <div class="{{ $percentColor }} text-white font-bold px-4 py-3 rounded-lg flex-shrink-0 text-center text-base md:text-lg shadow-sm w-full sm:w-auto self-stretch flex items-center justify-center">
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
