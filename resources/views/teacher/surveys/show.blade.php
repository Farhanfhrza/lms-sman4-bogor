<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Hasil Survei:') }} {{ $survey->title }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-breadcrumb :items="$breadcrumbs" />

            <!-- Summary Card -->
            <div class="bg-[#1a6341] text-white rounded-xl shadow-lg overflow-hidden mb-8 p-6 sm:p-8">
                <div class="flex flex-col md:flex-row md:items-center gap-6">
                    <div class="flex-1">
                        <h3 class="text-2xl font-extrabold mb-1">{{ $survey->title }}</h3>
                        <p class="text-green-200 text-sm mb-4">{{ $survey->academicYear->name ?? '-' }} &bull; Semester {{ $survey->semester }}</p>
                        <div class="flex items-center gap-2 text-sm text-green-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <span>Total Responden: <strong class="text-white">{{ $totalResponses }} siswa</strong></span>
                        </div>
                    </div>
                    <div class="text-center bg-white/20 backdrop-blur rounded-2xl px-8 py-6">
                        <div class="text-5xl font-black text-yellow-300">{{ $overallAverage }}</div>
                        <div class="flex justify-center mt-1">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 {{ $i <= round($overallAverage) ? 'text-yellow-400' : 'text-white/30' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            @endfor
                        </div>
                        <p class="text-white/80 text-xs mt-1">Rata-rata Keseluruhan</p>
                    </div>
                </div>
            </div>

            <!-- Per-Question Breakdown -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl mb-8">
                <div class="p-6 md:p-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-[#1a6341]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Rincian Penilaian Per Aspek
                    </h3>
                    <div class="space-y-6">
                        @foreach($questions as $index => $q)
                            @php $avg = $questionAverages[$q->id] ?? 0; $percent = ($avg / 5) * 100; @endphp
                            <div>
                                <div class="flex items-start justify-between mb-1">
                                    <p class="text-sm font-medium text-gray-700 pr-4">{{ $index + 1 }}. {{ $q->question_text }}</p>
                                    <span class="font-bold text-[#1a6341] text-lg flex-shrink-0">{{ $avg }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                    <div class="h-3 rounded-full 
                                        @if($avg >= 4) bg-green-500 
                                        @elseif($avg >= 3) bg-yellow-400 
                                        @else bg-red-400 
                                        @endif"
                                        style="width: {{ $percent }}%">
                                    </div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-400 mt-1">
                                    <span>Sangat Kurang (1)</span>
                                    <span>Sangat Baik (5)</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Anonymous Comments -->
            @if($comments->isNotEmpty())
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl">
                <div class="p-6 md:p-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-[#1a6341]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                        Komentar & Saran Anonim ({{ $comments->count() }} komentar)
                    </h3>
                    <p class="text-sm text-gray-500 mb-4 italic">Identitas siswa dirahasiakan. Komentar ini diharapkan menjadi bahan refleksi positif.</p>
                    <div class="space-y-3">
                        @foreach($comments as $comment)
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                                <p class="text-sm text-gray-700 flex-1">{{ $comment }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @else
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl">
                <div class="p-6 text-center text-gray-500 text-sm italic">
                    Tidak ada komentar atau saran tertulis dari siswa pada periode ini.
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
