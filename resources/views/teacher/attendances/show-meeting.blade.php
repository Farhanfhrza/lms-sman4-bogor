<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            Detail Pertemuan: {{ \Carbon\Carbon::parse($meeting->meeting_date)->translatedFormat('l, d F Y') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6" x-data="{ editMeetingModalOpen: false }">
        
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
                @if($meeting->description)
                    <p class="text-gray-500 mt-1 text-sm"><span class="font-semibold">Deskripsi:</span> {{ $meeting->description }}</p>
                @endif
            </div>
            <div class="mt-4 md:mt-0 flex flex-col items-start md:items-end space-y-3 w-full md:w-auto">
                <p class="text-gray-800 font-bold bg-green-50 px-4 py-2 rounded-lg border border-green-100 inline-block shadow-sm">
                    ⏰ {{ \Carbon\Carbon::parse($meeting->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($meeting->end_time)->format('H:i') }}
                </p>
                <button @click="editMeetingModalOpen = true" type="button" class="text-sm bg-white text-blue-600 hover:bg-blue-50 font-bold py-2 px-4 rounded-lg border border-blue-200 shadow-sm transition-colors flex items-center justify-center w-full md:w-auto">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Edit Detail
                </button>
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
                                    <div class="grid grid-cols-4 md:flex md:items-center md:justify-center gap-2 md:gap-4 w-full">
                                        <!-- Hadir -->
                                        <label class="cursor-pointer">
                                            <input type="radio" name="attendances[{{ $attendance->id }}][status]" value="Hadir" {{ $attendance->status == 'Hadir' ? 'checked' : '' }} class="peer sr-only">
                                            <div class="flex items-center justify-center py-2.5 px-1 rounded-md text-[11px] font-bold sm:text-sm md:font-semibold border-2 border-transparent text-gray-500 bg-gray-50 md:bg-white shadow-sm hover:bg-green-50 peer-checked:bg-green-100 peer-checked:text-green-700 peer-checked:border-green-500 transition-all uppercase md:capitalize tracking-wider md:tracking-normal md:w-[72px] md:py-1.5">
                                                Hadir
                                            </div>
                                        </label>
                                        
                                        <!-- Sakit -->
                                        <label class="cursor-pointer">
                                            <input type="radio" name="attendances[{{ $attendance->id }}][status]" value="Sakit" {{ $attendance->status == 'Sakit' ? 'checked' : '' }} class="peer sr-only">
                                            <div class="flex items-center justify-center py-2.5 px-1 rounded-md text-[11px] font-bold sm:text-sm md:font-semibold border-2 border-transparent text-gray-500 bg-gray-50 md:bg-white shadow-sm hover:bg-red-50 peer-checked:bg-red-100 peer-checked:text-red-700 peer-checked:border-red-500 transition-all uppercase md:capitalize tracking-wider md:tracking-normal md:w-[72px] md:py-1.5">
                                                Sakit
                                            </div>
                                        </label>
                                        
                                        <!-- Izin/Lainnya -->
                                        <label class="cursor-pointer">
                                            <input type="radio" name="attendances[{{ $attendance->id }}][status]" value="Izin" {{ $attendance->status == 'Izin' ? 'checked' : '' }} class="peer sr-only">
                                            <div class="flex items-center justify-center py-2.5 px-1 rounded-md text-[11px] font-bold sm:text-sm md:font-semibold border-2 border-transparent text-gray-500 bg-gray-50 md:bg-white shadow-sm hover:bg-orange-50 peer-checked:bg-orange-100 peer-checked:text-orange-700 peer-checked:border-orange-500 transition-all uppercase md:capitalize tracking-wider md:tracking-normal md:w-[72px] md:py-1.5">
                                                Izin
                                            </div>
                                        </label>

                                        <!-- Alpha -->
                                        <label class="cursor-pointer">
                                            <input type="radio" name="attendances[{{ $attendance->id }}][status]" value="Alpha" {{ $attendance->status == 'Alpha' ? 'checked' : '' }} class="peer sr-only">
                                            <div class="flex items-center justify-center py-2.5 px-1 rounded-md text-[11px] font-bold sm:text-sm md:font-semibold border-2 border-transparent text-gray-500 bg-gray-50 md:bg-white shadow-sm hover:bg-gray-100 peer-checked:bg-gray-200 peer-checked:text-gray-900 peer-checked:border-gray-500 transition-all uppercase md:capitalize tracking-wider md:tracking-normal md:w-[72px] md:py-1.5">
                                                Alpha
                                            </div>
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

        <!-- Edit Pertemuan Modal -->
        <div x-show="editMeetingModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                <div x-show="editMeetingModalOpen" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="editMeetingModalOpen = false"></div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div x-show="editMeetingModalOpen" class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <form action="{{ route('manage.courses.attendances.meetings.update', [$course, $meeting]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-100">
                            <h3 class="text-xl leading-6 font-bold text-gray-900" id="modal-title">
                                Edit Detail Pertemuan
                            </h3>
                            <div class="mt-5 space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="col-span-2">
                                        <label for="title" class="block text-sm font-semibold text-gray-700 mb-1">Materi Pembahasan <span class="text-red-500">*</span></label>
                                        <input type="text" name="title" id="title" value="{{ $meeting->title }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#1a6341] focus:border-[#1a6341] sm:text-sm bg-gray-50 p-2.5 border">
                                    </div>
                                    <div class="col-span-2">
                                        <label for="meeting_date" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Pertemuan <span class="text-red-500">*</span></label>
                                        <input type="date" name="meeting_date" id="meeting_date" value="{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('Y-m-d') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#1a6341] focus:border-[#1a6341] sm:text-sm bg-gray-50 p-2.5 border">
                                    </div>
                                    <div>
                                        <label for="start_time" class="block text-sm font-semibold text-gray-700 mb-1">Jam Mulai <span class="text-red-500">*</span></label>
                                        <input type="time" name="start_time" id="start_time" value="{{ \Carbon\Carbon::parse($meeting->start_time)->format('H:i') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#1a6341] focus:border-[#1a6341] sm:text-sm bg-gray-50 p-2.5 border">
                                    </div>
                                    <div>
                                        <label for="end_time" class="block text-sm font-semibold text-gray-700 mb-1">Jam Selesai <span class="text-red-500">*</span></label>
                                        <input type="time" name="end_time" id="end_time" value="{{ \Carbon\Carbon::parse($meeting->end_time)->format('H:i') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#1a6341] focus:border-[#1a6341] sm:text-sm bg-gray-50 p-2.5 border">
                                    </div>
                                    <div class="col-span-2">
                                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi/Catatan (Opsional)</label>
                                        <textarea name="description" id="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#1a6341] focus:border-[#1a6341] sm:text-sm bg-gray-50 p-2.5 border">{{ $meeting->description }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100 rounded-b-xl">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#1a6341] text-base font-medium text-white hover:bg-[#155034] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1a6341] sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                                Simpan Perubahan
                            </button>
                            <button type="button" @click="editMeetingModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1a6341] sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
