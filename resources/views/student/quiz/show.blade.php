<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ $quiz->title }}
        </h2>
    </x-slot>

    <div class="pb-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <div class="py-4">
                <x-breadcrumb :items="$breadcrumbs" />
            </div>

            {{-- Success / Error Messages --}}
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif
            @if(session('info'))
                <div class="mb-4 p-4 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg text-sm">
                    {{ session('info') }}
                </div>
            @endif

            {{-- Quiz Banner --}}
            <div class="bg-gradient-to-r from-green-700 to-green-900 rounded-xl p-8 mb-6 shadow-lg relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full"></div>
                <div class="absolute -bottom-6 -left-6 w-24 h-24 bg-white/5 rounded-full"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <span class="px-3 py-1 bg-white/20 rounded-full text-white text-xs font-medium">Kuis</span>
                    </div>
                    <h1 class="text-3xl font-bold text-white mb-2">{{ $quiz->title }}</h1>
                    <p class="text-green-200 text-sm">
                        {{ $quiz->section->classSubject->subject->name ?? '' }} — {{ $quiz->section->classSubject->schoolClass->name ?? '' }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Main Content --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Quiz Info Card --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Informasi Kuis</h3>

                        @if($quiz->description)
                            <div class="prose prose-sm max-w-none text-gray-700 mb-6">
                                {!! $quiz->description !!}
                            </div>
                        @endif

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div class="bg-gray-50 rounded-lg p-3 text-center">
                                <p class="text-gray-500 mb-1">Jumlah Soal</p>
                                <p class="text-xl font-bold text-gray-800">{{ $quiz->questions->count() }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3 text-center">
                                <p class="text-gray-500 mb-1">Waktu</p>
                                <p class="text-xl font-bold text-gray-800">{{ $quiz->time_limit ? $quiz->time_limit . ' mnt' : '∞' }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3 text-center">
                                <p class="text-gray-500 mb-1">Maks. Percobaan</p>
                                <p class="text-xl font-bold text-gray-800">{{ $maxAttempts }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3 text-center">
                                <p class="text-gray-500 mb-1">Batas Waktu</p>
                                <p class="text-sm font-bold text-gray-800">{{ $quiz->end_at ? $quiz->end_at->format('d M Y H:i') : '-' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Rules Card --}}
                    <div class="bg-white rounded-xl shadow-sm border border-red-100 p-6">
                        <h3 class="text-lg font-bold text-red-700 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            Peraturan Kuis
                        </h3>
                        <ul class="space-y-2 text-sm text-gray-700">
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                Kuis akan berjalan dalam <strong>mode layar penuh (fullscreen)</strong>.
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                <strong>Dilarang</strong> membuka tab lain atau meninggalkan halaman kuis.
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                <strong>Dilarang</strong> keluar dari mode fullscreen selama kuis berlangsung.
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                Jika pelanggaran terdeteksi, <strong class="text-red-600">kuis akan otomatis dikumpulkan</strong>.
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                Jawaban akan otomatis disimpan setiap kali Anda memilih opsi.
                            </li>
                        </ul>
                    </div>

                    {{-- Previous Attempts --}}
                    @if($attempts->count() > 0)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Riwayat Percobaan</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-bold text-gray-600">No</th>
                                        <th class="px-4 py-3 text-left font-bold text-gray-600">Mulai</th>
                                        <th class="px-4 py-3 text-left font-bold text-gray-600">Selesai</th>
                                        <th class="px-4 py-3 text-center font-bold text-gray-600">Skor</th>
                                        <th class="px-4 py-3 text-center font-bold text-gray-600">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($attempts as $i => $att)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>
                                        <td class="px-4 py-3 text-gray-700">{{ $att->started_at?->format('d M Y H:i') ?? '-' }}</td>
                                        <td class="px-4 py-3 text-gray-700">{{ $att->submitted_at?->format('d M Y H:i') ?? '-' }}</td>
                                        <td class="px-4 py-3 text-center font-bold {{ $att->is_submitted ? 'text-green-700' : 'text-gray-400' }}">
                                            {{ $att->is_submitted ? number_format($att->total_score, 1) : '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($att->is_submitted)
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Selesai</span>
                                            @else
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Berlangsung</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">
                    {{-- CTA Card --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
                        <div class="mb-4">
                            <div class="text-4xl font-bold text-green-700 mb-1">{{ $attemptsUsed }}/{{ $maxAttempts }}</div>
                            <p class="text-sm text-gray-500">Percobaan Digunakan</p>
                        </div>

                        @if($activeAttempt)
                            <a href="{{ route('student.quiz.take', $activeAttempt) }}"
                               class="w-full inline-flex items-center justify-center px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-lg shadow-md transition-colors text-sm">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Lanjutkan Kuis
                            </a>
                        @elseif($canAttempt && $isOpen)
                            <form method="POST" action="{{ route('student.quiz.start', [$course, $quiz]) }}" id="startQuizForm">
                                @csrf
                                <button type="button" onclick="confirmStart()" 
                                        class="w-full inline-flex items-center justify-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg shadow-md transition-colors text-sm">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Mulai Kuis
                                </button>
                            </form>
                        @elseif(!$isOpen)
                            <div class="w-full px-6 py-3 bg-gray-200 text-gray-600 rounded-lg text-sm font-bold cursor-not-allowed">
                                Kuis Sudah Ditutup
                            </div>
                        @else
                            <div class="w-full px-6 py-3 bg-gray-200 text-gray-600 rounded-lg text-sm font-bold cursor-not-allowed">
                                Batas Percobaan Tercapai
                            </div>
                        @endif
                    </div>

                    {{-- Status Card --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-900 mb-3">Status Kuis</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Status</span>
                                @if($isOpen)
                                    <span class="font-medium text-green-600">Terbuka</span>
                                @else
                                    <span class="font-medium text-red-600">Ditutup</span>
                                @endif
                            </div>
                            @if($quiz->end_at)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Batas Akhir</span>
                                <span class="font-medium text-gray-800">{{ $quiz->end_at->format('d M Y') }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-gray-500">Tipe Soal</span>
                                <span class="font-medium text-gray-800">Pilihan Ganda</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmStart() {
            if (confirm('Apakah Anda yakin ingin memulai kuis?\n\nPastikan Anda sudah membaca peraturan kuis. Setelah dimulai, kuis akan berjalan dalam mode fullscreen dan tidak bisa dijeda.')) {
                document.getElementById('startQuizForm').submit();
            }
        }
    </script>
</x-app-layout>
