<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            Absensi - {{ $course->subject->name ?? 'Course' }} ({{ $course->schoolClass->name ?? 'Class' }})
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6" x-data="{ addMeetingModalOpen: false }">
        
        <!-- Breadcrumbs -->
        <nav class="flex text-sm text-gray-500 mb-4 font-medium" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('manage.attendances.dashboard') }}" class="hover:text-[#1a6341] transition-colors">Manajemen Absensi</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        <span class="text-gray-700 ml-1 md:ml-2">List Pertemuan</span>
                    </div>
                </li>
            </ol>
        </nav>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
            <button @click="addMeetingModalOpen = true" class="w-full sm:w-auto justify-center bg-[#1a6341] hover:bg-[#155034] text-white font-bold py-2.5 px-5 rounded-lg border-b-4 border-[#12422b] hover:border-[#0f3623] active:border-t-4 active:border-b-0 transition-all flex items-center shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                TAMBAH PERTEMUAN
            </button>
            <a href="{{ route('manage.courses.attendances.recap', $course) }}" class="w-full sm:w-auto justify-center bg-white border-2 border-[#1a6341] text-[#1a6341] hover:bg-green-50 font-bold py-2 px-5 rounded-lg flex items-center shadow-sm transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                REKAP KESELURUHAN
            </a>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden space-y-4">
            @forelse($meetings as $meeting)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex justify-between items-start border-b border-gray-100 pb-3 mb-3">
                        <div>
                            <div class="text-sm font-bold text-gray-900">{{ Carbon\Carbon::parse($meeting->meeting_date)->translatedFormat('l, d F Y') }}</div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ Carbon\Carbon::parse($meeting->start_time)->format('H:i') }} - {{ Carbon\Carbon::parse($meeting->end_time)->format('H:i') }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="text-sm font-semibold text-gray-900">{{ $meeting->title }}</div>
                        @if($meeting->description)
                            <div class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $meeting->description }}</div>
                        @endif
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-3 mb-4">
                        <div class="text-xs font-bold text-gray-500 uppercase text-center mb-2">Rekap Kehadiran</div>
                        <div class="flex justify-between space-x-2 text-sm font-bold">
                            <span class="text-green-600 bg-green-100 flex-1 text-center py-1 rounded">H: {{ $meeting->stats['hadir'] }}</span>
                            <span class="text-red-500 bg-red-100 flex-1 text-center py-1 rounded">S: {{ $meeting->stats['sakit'] }}</span>
                            <span class="text-orange-500 bg-orange-100 flex-1 text-center py-1 rounded">I: {{ $meeting->stats['izin'] }}</span>
                            <span class="text-gray-500 bg-gray-200 flex-1 text-center py-1 rounded">A: {{ $meeting->stats['alpha'] }}</span>
                        </div>
                    </div>
                    
                    <a href="{{ route('manage.courses.attendances.showMeeting', [$course, $meeting]) }}" class="flex justify-center items-center text-white bg-blue-600 hover:bg-blue-700 font-medium py-2.5 px-4 rounded-lg text-sm w-full transition-colors shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        Detail Data
                    </a>
                </div>
            @empty
                <div class="bg-white p-8 rounded-xl border border-gray-200 text-center text-gray-500">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    <p class="text-base">Belum ada pertemuan yang dibuat.</p>
                </div>
            @endforelse
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block bg-white shadow-md rounded-xl overflow-hidden border border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">TANGGAL</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">MATERI PEMBAHASAN</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">REKAP KEHADIRAN</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($meetings as $meeting)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">{{ Carbon\Carbon::parse($meeting->meeting_date)->translatedFormat('l, d F Y') }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ Carbon\Carbon::parse($meeting->start_time)->format('H:i') }} - {{ Carbon\Carbon::parse($meeting->end_time)->format('H:i') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $meeting->title }}</div>
                                @if($meeting->description)
                                    <div class="text-xs text-gray-500 mt-1 line-clamp-2 max-w-sm">{{ $meeting->description }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center space-x-4 text-sm font-bold">
                                    <span class="text-green-600">H: {{ $meeting->stats['hadir'] }}</span>
                                    <span class="text-red-500">S: {{ $meeting->stats['sakit'] }}</span>
                                    <span class="text-orange-500">I: {{ $meeting->stats['izin'] }}</span>
                                    <span class="text-gray-500">A: {{ $meeting->stats['alpha'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <a href="{{ route('manage.courses.attendances.showMeeting', [$course, $meeting]) }}" class="inline-flex items-center text-white bg-blue-600 hover:bg-blue-700 font-medium py-1.5 px-3 rounded text-xs transition-colors shadow-sm">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Detail Data
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    <p class="text-base">Belum ada pertemuan yang dibuat.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Tambah Pertemuan Modal -->
        <div x-show="addMeetingModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                <div x-show="addMeetingModalOpen" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="addMeetingModalOpen = false"></div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div x-show="addMeetingModalOpen" class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <form action="{{ route('manage.courses.attendances.meetings.store', $course) }}" method="POST">
                        @csrf
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-100">
                            <h3 class="text-xl leading-6 font-bold text-gray-900" id="modal-title">
                                Tambah Pertemuan Baru
                            </h3>
                            <div class="mt-5 space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="col-span-2">
                                        <label for="title" class="block text-sm font-semibold text-gray-700 mb-1">Materi Pembahasan <span class="text-red-500">*</span></label>
                                        <input type="text" name="title" id="title" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#1a6341] focus:border-[#1a6341] sm:text-sm bg-gray-50 p-2.5 border" placeholder="Contoh: Bab 1 Pendahuluan">
                                    </div>
                                    <div class="col-span-2">
                                        <label for="meeting_date" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Pertemuan <span class="text-red-500">*</span></label>
                                        <input type="date" name="meeting_date" id="meeting_date" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#1a6341] focus:border-[#1a6341] sm:text-sm bg-gray-50 p-2.5 border">
                                    </div>
                                    <div>
                                        <label for="start_time" class="block text-sm font-semibold text-gray-700 mb-1">Jam Mulai <span class="text-red-500">*</span></label>
                                        <input type="time" name="start_time" id="start_time" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#1a6341] focus:border-[#1a6341] sm:text-sm bg-gray-50 p-2.5 border">
                                    </div>
                                    <div>
                                        <label for="end_time" class="block text-sm font-semibold text-gray-700 mb-1">Jam Selesai <span class="text-red-500">*</span></label>
                                        <input type="time" name="end_time" id="end_time" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#1a6341] focus:border-[#1a6341] sm:text-sm bg-gray-50 p-2.5 border">
                                    </div>
                                    <div class="col-span-2">
                                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi/Catatan (Opsional)</label>
                                        <textarea name="description" id="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#1a6341] focus:border-[#1a6341] sm:text-sm bg-gray-50 p-2.5 border" placeholder="Catatan opsional untuk pertemuan ini"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100 rounded-b-xl">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#1a6341] text-base font-medium text-white hover:bg-[#155034] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1a6341] sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                                Simpan
                            </button>
                            <button type="button" @click="addMeetingModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1a6341] sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
