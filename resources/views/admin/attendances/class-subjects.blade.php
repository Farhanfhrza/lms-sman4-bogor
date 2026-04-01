<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Mata Pelajaran: ') }} {{ $schoolClass->name }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        
        <div class="flex items-center mb-4 mt-2">
            <a href="{{ route('manage.attendances.dashboard') }}" class="text-[#1a6341] hover:text-[#155034] transition-colors mr-3 mt-1">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z"></path></svg>
            </a>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Kelas {{ $schoolClass->name }}</h1>
        </div>

        <!-- Filters -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:flex-wrap gap-3 mb-6">
            <input type="text" placeholder="Cari Mata Pelajaran..." class="w-full sm:w-auto border-gray-300 rounded-md shadow-sm focus:border-[#1a6341] focus:ring-[#1a6341] text-sm py-2 px-3 pl-8">
        </div>

        <!-- Course Cards List -->
        <div class="flex flex-col space-y-4">
            @forelse ($courses as $course)
                <a href="{{ route('manage.courses.attendances.index', $course) }}" class="block group hover:shadow-md transition-shadow">
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 flex items-center shadow-sm hover:bg-white hover:border-gray-300 transition-colors">
                        <div class="bg-[#59b88b] text-white p-3 rounded-lg mr-4 md:mr-5 shadow-sm flex-shrink-0">
                            <!-- Icon Group -->
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <div class="flex-grow">
                            <h3 class="text-xl font-bold text-gray-800">{{ $course->subject->name ?? 'Subject' }}</h3>
                            <p class="text-gray-600 text-sm mt-1">Pengampu: <span class="font-medium text-gray-800">{{ $course->teacher->user->full_name ?? 'Belum Ada Guru' }}</span></p>
                        </div>
                        <div class="hidden sm:block text-[#1a6341] font-semibold flex-shrink-0">
                            Kelola Absen &rarr;
                        </div>
                    </div>
                </a>
            @empty
                <div class="bg-white p-6 rounded-lg shadow-sm text-center text-gray-500 border border-gray-200">
                    Belum ada mata pelajaran yang ditugaskan di kelas ini.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
