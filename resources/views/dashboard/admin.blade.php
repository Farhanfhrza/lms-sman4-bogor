<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Dashboard Admin') }}
        </h2>
    </x-slot>

    <!-- Content Wrapper -->
    <div class="space-y-6">
        
        <!-- Welcome Banner -->
        <div class="bg-gradient-to-r from-gray-800 to-gray-900 rounded-xl shadow-lg p-6 md:p-8 text-white relative overflow-hidden">
            <div class="relative z-10">
                <h1 class="text-3xl font-extrabold mb-2">Halo, {{ Auth::user()->full_name ?? Auth::user()->name }}! 👋</h1>
                <p class="text-gray-300 text-lg">
                    Selamat datang di Panel Administrator LMS SMAN 4 Bogor.
                    @if($academicYear) 
                        Tahun Ajaran Aktif: <span class="font-bold text-white">{{ $academicYear->name }}</span>
                    @else
                        Sistem sedang dalam konfigurasi Tahun Ajaran.
                    @endif
                </p>
                <div class="mt-4 inline-flex items-center space-x-2">
                    <a href="{{ route('admin.academic-years.index') }}" class="px-4 py-2 bg-white text-gray-900 rounded-lg text-sm font-bold shadow-sm hover:bg-gray-100 transition-colors">Kelola Tahun Ajaran</a>
                    <a href="{{ route('admin.activity-logs') }}" class="px-4 py-2 bg-gray-700 text-white rounded-lg text-sm font-bold shadow-sm hover:bg-gray-600 border border-gray-600 transition-colors">Log Sistem</a>
                </div>
            </div>
            <!-- Decorative circle -->
            <div class="absolute -right-20 -bottom-20 w-64 h-64 rounded-full bg-white opacity-5 pointer-events-none"></div>
            <svg class="absolute right-10 top-1/2 transform -translate-y-1/2 w-48 h-48 opacity-10" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
        </div>

        <!-- System Overview Widgets (now clickable) -->
        <h3 class="text-lg font-bold text-gray-800 mt-8 mb-4 border-b pb-2">Ikhtisar Sistem</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <!-- Siswa -->
            <a href="{{ route('admin.students.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex items-center hover:shadow-md hover:border-blue-300 transition-all group">
                <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-4 group-hover:bg-blue-600 group-hover:text-white transition-colors flex-shrink-0">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <div>
                    <h4 class="text-3xl font-extrabold text-gray-800">{{ number_format($totalStudents) }}</h4>
                    <p class="text-sm font-medium text-gray-500 mt-0.5 group-hover:text-blue-600 transition-colors">Total Siswa</p>
                    <p class="text-xs text-blue-400 mt-1 opacity-0 group-hover:opacity-100 transition-opacity">Kelola Siswa →</p>
                </div>
            </a>
            
            <!-- Guru -->
            <a href="{{ route('admin.teachers.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex items-center hover:shadow-md hover:border-emerald-300 transition-all group">
                <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mr-4 group-hover:bg-emerald-600 group-hover:text-white transition-colors flex-shrink-0">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <h4 class="text-3xl font-extrabold text-gray-800">{{ number_format($totalTeachers) }}</h4>
                    <p class="text-sm font-medium text-gray-500 mt-0.5 group-hover:text-emerald-600 transition-colors">Total Guru</p>
                    <p class="text-xs text-emerald-400 mt-1 opacity-0 group-hover:opacity-100 transition-opacity">Kelola Guru →</p>
                </div>
            </a>

            <!-- Kelas -->
            <a href="{{ route('admin.classes.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex items-center hover:shadow-md hover:border-amber-300 transition-all group">
                <div class="w-14 h-14 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mr-4 group-hover:bg-amber-600 group-hover:text-white transition-colors flex-shrink-0">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div>
                    <h4 class="text-3xl font-extrabold text-gray-800">{{ number_format($totalClasses) }}</h4>
                    <p class="text-sm font-medium text-gray-500 mt-0.5 group-hover:text-amber-600 transition-colors">Kelas (Rombel)</p>
                    <p class="text-xs text-amber-400 mt-1 opacity-0 group-hover:opacity-100 transition-opacity">Kelola Kelas →</p>
                </div>
            </a>

            <!-- Mapel -->
            <a href="{{ route('admin.subjects.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex items-center hover:shadow-md hover:border-purple-300 transition-all group">
                <div class="w-14 h-14 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mr-4 group-hover:bg-purple-600 group-hover:text-white transition-colors flex-shrink-0">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <div>
                    <h4 class="text-3xl font-extrabold text-gray-800">{{ number_format($totalSubjects) }}</h4>
                    <p class="text-sm font-medium text-gray-500 mt-0.5 group-hover:text-purple-600 transition-colors">Mata Pelajaran</p>
                    <p class="text-xs text-purple-400 mt-1 opacity-0 group-hover:opacity-100 transition-opacity">Kelola Mapel →</p>
                </div>
            </a>

        </div>

    </div>
</x-app-layout>
