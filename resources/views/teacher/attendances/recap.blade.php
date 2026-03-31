<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            Rekap Keseluruhan Absensi: {{ $course->subject->name ?? 'Course' }} ({{ $course->schoolClass->name ?? 'Class' }})
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        
        <!-- Breadcrumbs -->
        <nav class="flex text-sm text-gray-500 mb-6 font-medium" aria-label="Breadcrumb">
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
                        <span class="text-gray-700 ml-1 md:ml-2">Rekap Keseluruhan</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="bg-white shadow-md rounded-xl border border-gray-200 overflow-hidden mb-6 relative">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800">Rekapan Kehadiran Siswa Semester Ini</h3>
                <!-- Print/Export Button (Mockup) -->
                <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1a6341]">
                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Laporan
                </button>
            </div>
            
            <div class="overflow-x-auto w-full" style="max-height: 70vh;">
                <table class="min-w-max w-full divide-y divide-gray-200 table-fixed relative">
                    <thead class="bg-gray-100">
                        <tr>
                            <!-- Fixed Left Columns -->
                            <th scope="col" class="sticky top-0 left-auto md:left-0 z-20 bg-gray-100 px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-16 border-r border-b border-gray-300 md:shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">No.</th>
                            <th scope="col" class="sticky top-0 left-auto md:left-16 z-20 bg-gray-100 px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-64 border-r border-b border-gray-300 md:shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">Siswa</th>
                            
                            <!-- Scrollable Middle Columns (Meetings) -->
                            @foreach($meetings as $index => $meeting)
                                <th scope="col" class="sticky top-0 z-10 bg-gray-100 px-4 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider w-24 border-r border-b border-gray-300" title="{{ \Carbon\Carbon::parse($meeting->meeting_date)->translatedFormat('d M Y') }}">
                                    P{{ $index + 1 }}
                                </th>
                            @endforeach

                            <!-- Fixed Right Column -->
                            @if($meetings->count() > 0)
                                <th scope="col" class="sticky top-0 right-auto md:right-0 z-20 bg-[#e8f4ec] px-6 py-4 text-center text-xs font-bold text-[#1a6341] uppercase tracking-wider w-32 border-l border-b border-green-200 md:shadow-[-2px_0_5px_-2px_rgba(0,0,0,0.1)]">Persentase</th>
                            @else
                                <th scope="col" class="sticky top-0 right-auto md:right-0 z-20 bg-[#e8f4ec] px-6 py-4 text-center text-xs font-bold text-[#1a6341] uppercase tracking-wider w-32 border-l border-b border-green-200 md:shadow-[-2px_0_5px_-2px_rgba(0,0,0,0.1)]">Persentase</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $no = 1; @endphp
                        @forelse($matrix as $studentId => $data)
                            <tr class="hover:bg-gray-50 transition-colors group">
                                <!-- Fixed Left Columns -->
                                <td class="sticky left-auto md:left-0 z-10 bg-white group-hover:bg-gray-50 px-6 py-3 whitespace-nowrap text-sm text-gray-500 border-r border-gray-200 md:shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">
                                    {{ $no++ }}
                                </td>
                                <td class="sticky left-auto md:left-16 z-10 bg-white group-hover:bg-gray-50 px-6 py-3 border-r border-gray-200 md:shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] text-sm">
                                    <div class="font-bold text-gray-900">{{ $data['student']->user->full_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $data['student']->nisn }}</div>
                                </td>
                                
                                <!-- Scrollable Middle Columns -->
                                @foreach($meetings as $meeting)
                                    @php
                                        $status = $data['meetings'][$meeting->id] ?? '-';
                                        $label = '-';
                                        $colorClass = 'text-gray-400 font-normal';
                                        
                                        if ($status === 'Hadir') {
                                            $label = 'H';
                                            $colorClass = 'text-green-600 font-bold bg-green-50';
                                        } elseif ($status === 'Sakit') {
                                            $label = 'S';
                                            $colorClass = 'text-red-500 font-bold bg-red-50';
                                        } elseif ($status === 'Izin') {
                                            $label = 'I';
                                            $colorClass = 'text-orange-500 font-bold bg-orange-50';
                                        } elseif ($status === 'Alpha') {
                                            $label = 'A';
                                            $colorClass = 'text-gray-800 font-bold bg-gray-100';
                                        }
                                    @endphp
                                    <td class="px-4 py-3 text-center text-sm border-r border-gray-100 {{ $colorClass }}">
                                        {{ $label }}
                                    </td>
                                @endforeach

                                <!-- Fixed Right Column -->
                                <td class="sticky right-auto md:right-0 z-10 bg-white group-hover:bg-gray-50 px-6 py-3 text-center whitespace-nowrap border-l border-gray-200 md:shadow-[-2px_0_5px_-2px_rgba(0,0,0,0.05)]">
                                    <div class="inline-flex items-center justify-center px-2.5 py-1 text-sm font-bold rounded-full {{ $data['percentage'] >= 75 ? 'bg-green-100 text-green-800' : ($data['percentage'] >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $data['percentage'] }}%
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($meetings) + 3 }}" class="px-6 py-8 text-center text-gray-500">
                                    Tidak ada data siswa untuk direkap.
                                </td>
                            </tr>
                        @endforelse
                        
                        <!-- Summary Row (Optional, could be added later for global stats per meeting) -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Legend -->
        <div class="bg-gray-50 p-4 rounded-lg flex space-x-6 text-sm font-medium text-gray-600 border border-gray-200 mb-8 inline-block shadow-sm">
            <span class="flex items-center"><span class="w-4 h-4 rounded px-1.5 flex items-center justify-center bg-green-50 text-green-600 font-bold border border-green-200 mr-2 text-xs">H</span> Hadir</span>
            <span class="flex items-center"><span class="w-4 h-4 rounded px-1.5 flex items-center justify-center bg-red-50 text-red-500 font-bold border border-red-200 mr-2 text-xs">S</span> Sakit</span>
            <span class="flex items-center"><span class="w-4 h-4 rounded px-1.5 flex items-center justify-center bg-orange-50 text-orange-500 font-bold border border-orange-200 mr-2 text-xs">I</span> Izin/Lainnya</span>
            <span class="flex items-center"><span class="w-4 h-4 rounded px-1.5 flex items-center justify-center bg-gray-100 text-gray-800 font-bold border border-gray-300 mr-2 text-xs">A</span> Alpha</span>
        </div>

    </div>
</x-app-layout>
