<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            Detail Pertemuan: {{ \Carbon\Carbon::parse($meeting->meeting_date)->translatedFormat('l, d F Y') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        
        <!-- Breadcrumbs -->
        <nav class="flex text-sm text-gray-500 mb-4 font-medium" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('manage.attendances.dashboard') }}" class="hover:text-[#1a6341] transition-colors">Manajemen Absensi</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        <a href="{{ route('manage.courses.attendances.index', $course) }}" class="hover:text-[#1a6341] transition-colors">List Pertemuan</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        <span class="text-gray-700 ml-1 md:ml-2">Roster Siswa</span>
                    </div>
                </li>
            </ol>
        </nav>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Ada kesalahan pengisian:</strong>
            <ul class="list-disc pl-5 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center">
            <div>
                <h3 class="text-xl font-bold text-gray-800">{{ $course->subject->name ?? 'Course' }} - {{ $course->schoolClass->name ?? 'Class' }}</h3>
                <p class="text-gray-600 mt-1"><span class="font-semibold">Materi Pembahasan:</span> {{ $meeting->title }}</p>
            </div>
            <div class="mt-4 md:mt-0 text-left md:text-right">
                <p class="text-gray-800 font-bold bg-green-50 px-4 py-2 rounded-lg border border-green-100 inline-block shadow-sm">
                    ⏰ {{ \Carbon\Carbon::parse($meeting->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($meeting->end_time)->format('H:i') }}
                </p>
            </div>
        </div>

        <form action="{{ route('manage.courses.attendances.updateRoster', [$course, $meeting]) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-200 mb-6">
                <!-- Remove overflow-x-auto to allow flex wrap if needed, or keep it but container styling handles mobile -->
                <div class="w-full">
                    <table class="min-w-full divide-y md:divide-gray-200 w-full block md:table">
                        <thead class="bg-gray-50 border-b border-gray-200 hidden md:table-header-group">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-16 border-r border-gray-200">No</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-64 border-r border-gray-200">Siswa</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider border-r border-gray-200">Keterangan</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Catatan (Optional)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-50 md:bg-white divide-y-0 md:divide-y md:divide-gray-200 flex flex-col md:table-row-group space-y-4 md:space-y-0 p-4 md:p-0">
                            @forelse($attendances as $attendance)
                            <tr class="bg-white rounded-xl shadow-sm md:shadow-none border border-gray-200 md:border-none p-5 md:p-0 flex flex-col md:table-row hover:bg-gray-50 transition-colors w-full">
                                <td class="px-0 py-0 md:px-6 md:py-4 md:border-r border-gray-200 text-sm text-gray-500 font-medium hidden md:table-cell">
                                    {{ $loop->iteration }}
                                </td>
                                
                                <td class="px-0 py-0 md:px-6 md:py-4 md:border-r border-gray-200 block md:table-cell mb-4 md:mb-0">
                                    <div class="md:hidden text-xs font-semibold text-gray-400 mb-3 uppercase tracking-wider border-b border-gray-100 pb-2">
                                        Siswa #{{ $loop->iteration }}
                                    </div>
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 sm:h-12 sm:w-12 relative">
                                            <img class="h-10 w-10 sm:h-12 sm:w-12 rounded-full object-cover border md:border border-gray-200 shadow-sm" src="https://ui-avatars.com/api/?name={{ urlencode($attendance->student->user->full_name) }}&background=1a6341&color=fff" alt="">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm md:text-sm sm:text-base font-bold text-gray-900">{{ $attendance->student->user->full_name }}</div>
                                            <div class="text-xs sm:text-sm text-gray-500 font-medium mt-0.5">NISN: {{ $attendance->student->nisn }}</div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-0 py-0 md:px-6 md:py-4 md:border-r border-gray-200 text-center block md:table-cell mb-4 md:mb-0">
                                    <div class="md:hidden text-xs font-semibold text-gray-400 mb-2 uppercase tracking-wider text-left">
                                        Status Kehadiran
                                    </div>
                                    <div class="flex items-center justify-between md:justify-center md:space-x-8 bg-gray-50 md:bg-transparent p-3 md:p-0 rounded-lg md:rounded-none border border-gray-200 md:border-none w-full shadow-inner md:shadow-none">
                                        <!-- Hadir -->
                                        <label class="flex flex-col md:flex-row items-center cursor-pointer group gap-1.5 md:gap-0">
                                            <input type="radio" name="attendances[{{ $attendance->id }}][status]" value="Hadir" {{ $attendance->status == 'Hadir' ? 'checked' : '' }} class="form-radio h-6 w-6 md:h-5 md:w-5 text-green-600 focus:ring-green-500 border-gray-300">
                                            <span class="md:ml-2 text-[11px] md:text-sm font-bold md:font-medium text-gray-700 group-hover:text-green-700 transition-colors uppercase md:capitalize tracking-wider md:tracking-normal">Hadir</span>
                                        </label>
                                        
                                        <!-- Sakit -->
                                        <label class="flex flex-col md:flex-row items-center cursor-pointer group gap-1.5 md:gap-0">
                                            <input type="radio" name="attendances[{{ $attendance->id }}][status]" value="Sakit" {{ $attendance->status == 'Sakit' ? 'checked' : '' }} class="form-radio h-6 w-6 md:h-5 md:w-5 text-red-500 focus:ring-red-500 border-gray-300">
                                            <span class="md:ml-2 text-[11px] md:text-sm font-bold md:font-medium text-gray-700 group-hover:text-red-700 transition-colors uppercase md:capitalize tracking-wider md:tracking-normal">Sakit</span>
                                        </label>
                                        
                                        <!-- Alpha -->
                                        <label class="flex flex-col md:flex-row items-center cursor-pointer group gap-1.5 md:gap-0">
                                            <input type="radio" name="attendances[{{ $attendance->id }}][status]" value="Alpha" {{ $attendance->status == 'Alpha' ? 'checked' : '' }} class="form-radio h-6 w-6 md:h-5 md:w-5 text-gray-600 focus:ring-gray-500 border-gray-300">
                                            <span class="md:ml-2 text-[11px] md:text-sm font-bold md:font-medium text-gray-700 group-hover:text-gray-900 transition-colors uppercase md:capitalize tracking-wider md:tracking-normal">Alpha</span>
                                        </label>
                                        
                                        <!-- Izin/Lainnya -->
                                        <label class="flex flex-col md:flex-row items-center cursor-pointer group gap-1.5 md:gap-0">
                                            <input type="radio" name="attendances[{{ $attendance->id }}][status]" value="Izin" {{ $attendance->status == 'Izin' ? 'checked' : '' }} class="form-radio h-6 w-6 md:h-5 md:w-5 text-orange-500 focus:ring-orange-500 border-gray-300">
                                            <span class="md:ml-2 text-[11px] md:text-sm font-bold md:font-medium text-gray-700 group-hover:text-orange-700 transition-colors uppercase md:capitalize tracking-wider md:tracking-normal">Izin</span>
                                        </label>
                                    </div>
                                </td>
                                
                                <td class="px-0 py-0 md:px-6 md:py-4 block md:table-cell">
                                    <div class="md:hidden text-xs font-semibold text-gray-400 mb-2 uppercase tracking-wider text-left">
                                        Catatan Pendidik
                                    </div>
                                    <input type="text" name="attendances[{{ $attendance->id }}][note]" value="{{ $attendance->note }}" placeholder="Tambah catatan (opsional)..." class="block w-full border-gray-300 rounded-lg md:rounded-md shadow-sm focus:ring-[#1a6341] focus:border-[#1a6341] sm:text-sm md:bg-gray-50 py-3 md:py-2 transition-colors">
                                </td>
                            </tr>
                            @empty
                            <tr class="block md:table-row">
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500 block md:table-cell w-full">
                                    Tidak ada data siswa dalam kelas ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Submit Button Fixed at bottom of content -->
            <div class="flex justify-end mt-4 mb-8">
                <button type="submit" class="bg-[#1a6341] hover:bg-[#155034] text-white font-bold py-3 px-8 rounded-lg shadow-md border-b-4 border-[#12422b] hover:border-[#0f3623] active:border-t-4 active:border-b-0 transition-all flex items-center text-lg">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Simpan Rekap Absensi
                </button>
            </div>
        </form>

    </div>
</x-app-layout>
