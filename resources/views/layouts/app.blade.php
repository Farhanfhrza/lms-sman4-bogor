<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'LMS SMAN 4 BOGOR') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <style>
            [x-cloak] { display: none !important; }
            body { font-family: 'Inter', sans-serif; }
            /* Reserve space for fixed header and footer */
            body { padding-top: 64px; padding-bottom: 56px; }
        </style>
        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-gray-100 text-gray-900" x-data="{ mobileMenuOpen: false }">
        
        <!-- Fixed Header with Logos (Full Width, Above Everything) -->
        <header class="fixed top-0 left-0 right-0 bg-white shadow-md border-b border-gray-200 z-50">
            <div class="flex items-center justify-between px-6 py-3">
                
                <!-- Left: Logos -->
                <div class="flex items-center gap-4">
                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-600 hover:text-gray-900 focus:outline-none lg:hidden">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    
                    <!-- Logos (All Screens) -->
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/logos/instansi_logos.png') }}" alt="Logo" class="h-10 md:h-12 w-auto">
                    </div>
                </div>

                <!-- Right: User Profile -->
                <div x-data="{ dropdownOpen: false }" class="relative">
                    <button @click="dropdownOpen = !dropdownOpen" class="flex items-center space-x-3 focus:outline-none group">
                         <div class="text-right hidden md:block">
                            <p class="text-sm text-gray-500 font-medium">Selamat Datang :</p>
                            <p class="text-sm font-bold text-[#1a6341]">{{ Auth::user()->full_name ?? Auth::user()->name }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-gray-300 overflow-hidden border-2 border-white shadow-sm group-hover:border-[#1a6341] transition-colors">
                            <img src="{{ Auth::user()->profile_photo_url }}" alt="User" class="w-full h-full object-cover">
                        </div>
                    </button>

                    <!-- Profile Dropdown -->
                    <div x-show="dropdownOpen" 
                         @click.away="dropdownOpen = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5 z-50 origin-top-right"
                         style="display: none;">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            {{ __('Profile') }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}"
                               onclick="event.preventDefault(); this.closest('form').submit();"
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                {{ __('Log Out') }}
                            </a>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Mobile Dropdown Menu -->
            <div x-show="mobileMenuOpen" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="lg:hidden bg-[#1a6341] border-t border-[#1e754c] shadow-lg text-white"
                 style="display: none;">
                
                <nav class="flex flex-col py-2">
                     <!-- Group: DASHBOARD -->
                    <div class="px-4 py-2 text-xs font-semibold text-green-200 uppercase tracking-wider">
                        Dashboard
                    </div>
                    <a href="{{ route('dashboard') }}" class="block px-4 py-3 {{ request()->routeIs('dashboard') ? 'bg-white text-[#1a6341] font-bold' : 'hover:bg-[#238054]' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            Dashboard
                        </div>
                    </a>
                    
                     <!-- Group: MENU LMS -->
                     <div class="px-4 py-2 mt-2 text-xs font-semibold text-green-200 uppercase tracking-wider">
                        Menu LMS
                    </div>
                    <a href="{{ route('academic-calendar.index') }}" class="block px-4 py-3 {{ request()->routeIs('academic-calendar.*') ? 'bg-white text-[#1a6341] font-bold' : 'hover:bg-[#238054]' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Kalender Akademik
                        </div>
                    </a>
                    @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('teacher'))
                    <a href="{{ route('courses.index') }}" class="block px-4 py-3 {{ request()->routeIs('courses.*') ? 'bg-white text-[#1a6341] font-bold' : 'hover:bg-[#238054]' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            Courses
                        </div>
                    </a>

                    <a href="{{ route('manage.attendances.dashboard') }}" class="block px-4 py-3 {{ request()->routeIs('manage.attendances.*') || request()->routeIs('manage.courses.attendances.*') ? 'bg-white text-[#1a6341] font-bold' : 'hover:bg-[#238054]' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Manajemen Absensi
                        </div>
                    </a>
                    @if(Auth::user()->hasRole('teacher'))
                    <a href="{{ route('teacher.surveys.index') }}" class="block px-4 py-3 {{ request()->routeIs('teacher.surveys.*') ? 'bg-white text-[#1a6341] font-bold' : 'hover:bg-[#238054]' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Hasil Survei Mengajar
                        </div>
                    </a>
                    @endif
                    @endif

                    @if(Auth::user()->hasRole('student'))
                    <a href="{{ route('courses.index') }}" class="block px-4 py-3 {{ request()->routeIs('courses.*') ? 'bg-white text-[#1a6341] font-bold' : 'hover:bg-[#238054]' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            Kelas Anda
                        </div>
                    </a>

                    <a href="{{ route('student.attendances.index') }}" class="block px-4 py-3 {{ request()->routeIs('student.attendances.*') ? 'bg-white text-[#1a6341] font-bold' : 'hover:bg-[#238054]' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Absensi
                        </div>
                    </a>
                    <a href="{{ route('student.surveys.index') }}" class="block px-4 py-3 {{ request()->routeIs('student.surveys.*') ? 'bg-white text-[#1a6341] font-bold' : 'hover:bg-[#238054]' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Survei Penilaian Guru
                        </div>
                    </a>
                    @endif
                    @if(Auth::user()->hasRole('admin'))
                    <div class="px-4 py-2 mt-2 text-xs font-semibold text-green-200 uppercase tracking-wider">
                        Admin
                    </div>
                    <a href="{{ route('admin.teachers.index') }}" class="block px-4 py-3 {{ request()->routeIs('admin.teachers.*') ? 'bg-white text-[#1a6341] font-bold' : 'hover:bg-[#238054]' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Data Guru
                        </div>
                    </a>
                    <a href="{{ route('admin.students.index') }}" class="block px-4 py-3 {{ request()->routeIs('admin.students.*') ? 'bg-white text-[#1a6341] font-bold' : 'hover:bg-[#238054]' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Data Siswa
                        </div>
                    </a>
                    <a href="{{ route('admin.classes.index') }}" class="block px-4 py-3 {{ request()->routeIs('admin.classes.*') ? 'bg-white text-[#1a6341] font-bold' : 'hover:bg-[#238054]' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            Data Kelas
                        </div>
                    </a>
                    <a href="{{ route('admin.subjects.index') }}" class="block px-4 py-3 {{ request()->routeIs('admin.subjects.*') ? 'bg-white text-[#1a6341] font-bold' : 'hover:bg-[#238054]' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            Mata Pelajaran
                        </div>
                    </a>
                    <a href="{{ route('admin.academic-years.index') }}" class="block px-4 py-3 {{ request()->routeIs('admin.academic-years.*') ? 'bg-white text-[#1a6341] font-bold' : 'hover:bg-[#238054]' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Tahun Ajaran
                        </div>
                    </a>
                    <a href="{{ route('admin.surveys.index') }}" class="block px-4 py-3 {{ request()->routeIs('admin.surveys.*') ? 'bg-white text-[#1a6341] font-bold' : 'hover:bg-[#238054]' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Survei Guru
                        </div>
                    </a>
                    <a href="{{ route('admin.schedules.index') }}" class="block px-4 py-3 {{ request()->routeIs('admin.schedules.*') ? 'bg-white text-[#1a6341] font-bold' : 'hover:bg-[#238054]' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Penjadwalan Kelas
                        </div>
                    </a>
                    <a href="{{ route('admin.activity-logs') }}" class="block px-4 py-3 {{ request()->routeIs('admin.activity-logs') ? 'bg-white text-[#1a6341] font-bold' : 'hover:bg-[#238054]' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                            Log Aktivitas
                        </div>
                    </a>
                    @endif
                </nav>
            </div>
        </header>

        <!-- Desktop Sidebar (Below Header) -->
        <aside class="hidden lg:flex flex-col w-64 fixed left-0 bg-[#1a6341] text-white z-30 shadow-xl transition-all duration-300" style="top: 64px; bottom: 56px;">
            <!-- Desktop Navigation -->
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                <!-- Group: DASHBOARD -->
                <div class="px-3 mb-2 mt-2 text-xs font-semibold text-green-200 uppercase tracking-wider">
                    Dashboard
                </div>
                
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-colors group
                          {{ request()->routeIs('dashboard') ? 'bg-white text-[#1a6341] font-bold shadow-sm' : 'text-white hover:bg-[#238054]' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-[#1a6341]' : 'text-green-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    <span class="font-medium">Dashboard</span>
                </a>

                <!-- Group: MENU LMS -->
                <div class="px-3 mb-2 mt-6 text-xs font-semibold text-green-200 uppercase tracking-wider">
                    Menu LMS
                </div>

                <a href="{{ route('academic-calendar.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('academic-calendar.*') ? 'bg-white text-[#1a6341] font-bold shadow-sm' : 'text-white hover:bg-[#238054]' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('academic-calendar.*') ? 'text-[#1a6341]' : 'text-green-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="font-medium">Kalender Akademik</span>
                </a>

                @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('teacher'))
                <a href="{{ route('courses.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('courses.*') ? 'bg-white text-[#1a6341] font-bold shadow-sm' : 'text-white hover:bg-[#238054]' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('courses.*') ? 'text-[#1a6341]' : 'text-green-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span class="font-medium">Courses</span>
                </a>

                <a href="{{ route('manage.attendances.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('manage.attendances.*') || request()->routeIs('manage.courses.attendances.*') ? 'bg-white text-[#1a6341] font-bold shadow-sm' : 'text-white hover:bg-[#238054]' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('manage.attendances.*') || request()->routeIs('manage.courses.attendances.*') ? 'text-[#1a6341]' : 'text-green-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span class="font-medium">Manajemen Absensi</span>
                </a>
                @if(Auth::user()->hasRole('teacher'))
                <a href="{{ route('teacher.surveys.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('teacher.surveys.*') ? 'bg-white text-[#1a6341] font-bold shadow-sm' : 'text-white hover:bg-[#238054]' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('teacher.surveys.*') ? 'text-[#1a6341]' : 'text-green-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="font-medium">Hasil Survei Mengajar</span>
                </a>
                @endif
                @endif

                @if(Auth::user()->hasRole('student'))
                <a href="{{ route('courses.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('courses.*') ? 'bg-white text-[#1a6341] font-bold shadow-sm' : 'text-white hover:bg-[#238054]' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('courses.*') ? 'text-[#1a6341]' : 'text-green-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <span class="font-medium">Kelas Anda</span>
                </a>

                <a href="{{ route('student.attendances.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('student.attendances.*') ? 'bg-white text-[#1a6341] font-bold shadow-sm' : 'text-white hover:bg-[#238054]' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('student.attendances.*') ? 'text-[#1a6341]' : 'text-green-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span class="font-medium">Absensi</span>
                </a>

                <a href="{{ route('student.surveys.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('student.surveys.*') ? 'bg-white text-[#1a6341] font-bold shadow-sm' : 'text-white hover:bg-[#238054]' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('student.surveys.*') ? 'text-[#1a6341]' : 'text-green-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="font-medium">Survei Guru</span>
                </a>
                @endif

                @if(Auth::user()->hasRole('admin'))
                <!-- Group: ADMIN -->
                <div class="px-3 mb-2 mt-6 text-xs font-semibold text-green-200 uppercase tracking-wider">
                    Admin
                </div>

                <a href="{{ route('admin.teachers.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-colors group
                          {{ request()->routeIs('admin.teachers.*') ? 'bg-white text-[#1a6341] font-bold shadow-sm' : 'text-white hover:bg-[#238054]' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.teachers.*') ? 'text-[#1a6341]' : 'text-green-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="font-medium">Data Guru</span>
                </a>

                <a href="{{ route('admin.students.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-colors group
                          {{ request()->routeIs('admin.students.*') ? 'bg-white text-[#1a6341] font-bold shadow-sm' : 'text-white hover:bg-[#238054]' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.students.*') ? 'text-[#1a6341]' : 'text-green-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span class="font-medium">Data Siswa</span>
                </a>

                <a href="{{ route('admin.classes.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-colors group
                          {{ request()->routeIs('admin.classes.*') ? 'bg-white text-[#1a6341] font-bold shadow-sm' : 'text-white hover:bg-[#238054]' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.classes.*') ? 'text-[#1a6341]' : 'text-green-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span class="font-medium">Data Kelas</span>
                </a>

                <a href="{{ route('admin.subjects.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-colors group
                          {{ request()->routeIs('admin.subjects.*') ? 'bg-white text-[#1a6341] font-bold shadow-sm' : 'text-white hover:bg-[#238054]' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.subjects.*') ? 'text-[#1a6341]' : 'text-green-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <span class="font-medium">Mata Pelajaran</span>
                </a>

                <a href="{{ route('admin.academic-years.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-colors group
                          {{ request()->routeIs('admin.academic-years.*') ? 'bg-white text-[#1a6341] font-bold shadow-sm' : 'text-white hover:bg-[#238054]' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.academic-years.*') ? 'text-[#1a6341]' : 'text-green-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="font-medium">Tahun Ajaran</span>
                </a>

                <a href="{{ route('admin.surveys.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-colors group
                          {{ request()->routeIs('admin.surveys.*') ? 'bg-white text-[#1a6341] font-bold shadow-sm' : 'text-white hover:bg-[#238054]' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.surveys.*') ? 'text-[#1a6341]' : 'text-green-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="font-medium">Survei Guru</span>
                </a>

                <a href="{{ route('admin.schedules.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-colors group
                          {{ request()->routeIs('admin.schedules.*') ? 'bg-white text-[#1a6341] font-bold shadow-sm' : 'text-white hover:bg-[#238054]' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.schedules.*') ? 'text-[#1a6341]' : 'text-green-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="font-medium">Penjadwalan Kelas</span>
                </a>

                <a href="{{ route('admin.activity-logs') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-colors group
                          {{ request()->routeIs('admin.activity-logs') ? 'bg-white text-[#1a6341] font-bold shadow-sm' : 'text-white hover:bg-[#238054]' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.activity-logs') ? 'text-[#1a6341]' : 'text-green-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span class="font-medium">Log Aktivitas</span>
                </a>
                @endif
            </nav>
        </aside>

        <!-- Main Content Wrapper (Below Header, Above Footer, Next to Sidebar on Desktop) -->
        <div class="lg:ml-64 bg-gray-50 transition-all duration-300 min-h-screen" style="padding-top: 0; padding-bottom: 0;">
            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto w-full p-4 md:p-8">
                {{ $slot }}
            </main>
        </div>

        <!-- Fixed Footer (Full Width, Above Everything) -->
        <footer class="fixed bottom-0 left-0 right-0 bg-[#0f291e] border-t border-green-900 text-white py-4 px-6 text-center lg:text-left z-50">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center text-xs md:text-sm font-medium">
                <p>&copy; 2026 SMAN 4 Bogor. All rights reserved.</p>
                <p class="mt-2 md:mt-0 opacity-70">LMS Version 1.0</p>
            </div>
        </footer>

        @stack('scripts')
    </body>
</html>
