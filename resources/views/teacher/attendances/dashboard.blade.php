<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Managemen Absensi') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        
        <div class="flex items-center justify-between mb-4 mt-2">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Managemen Absensi</h1>
        </div>

        <!-- Filters (Mockup shows All, Search, Sort) -->
        <div class="flex items-center mb-6 space-x-3">
            <button class="bg-[#59b88b] text-white px-4 py-2 rounded-md font-medium shadow-sm hover:bg-[#4a9f77] transition-colors">All</button>
            <input type="text" placeholder="Search" class="border-gray-300 rounded-md shadow-sm focus:border-[#1a6341] focus:ring-[#1a6341] text-sm py-2 px-3 pl-8">
            <button class="bg-[#59b88b] text-white px-4 py-2 rounded-md font-medium flex items-center shadow-sm hover:bg-[#4a9f77] transition-colors">
                Sort by course name
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
        </div>

        <!-- Course Cards List -->
        <div class="flex flex-col space-y-4">
            @forelse ($courses as $course)
                <a href="{{ route('manage.courses.attendances.index', $course) }}" class="block grouphover:shadow-md transition-shadow">
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 flex items-center shadow-sm hover:bg-white hover:border-gray-300 transition-colors">
                        <div class="bg-[#59b88b] text-white p-3 rounded-lg mr-5 shadow-sm">
                            <!-- Icon Group -->
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">{{ $course->subject->name ?? 'Course' }}</h3>
                            <p class="text-gray-600 text-sm mt-1">{{ $course->schoolClass->name ?? 'Class' }}</p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="bg-white p-6 rounded-lg shadow-sm text-center text-gray-500 border border-gray-200">
                    Anda belum ditugaskan untuk mengajar kelas manapun.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
