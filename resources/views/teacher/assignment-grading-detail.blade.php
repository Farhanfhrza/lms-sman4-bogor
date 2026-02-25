<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ $course->subject->name ?? 'Mata Pelajaran' }}
        </h2>
    </x-slot>

    {{-- Breadcrumbs --}}
    @if(!empty($breadcrumbs))
        <nav class="mb-6 text-sm text-gray-500">
            @foreach($breadcrumbs as $i => $crumb)
                @if(!$loop->last)
                    <a href="{{ $crumb['url'] ?? '#' }}" class="hover:text-[#1a6341] transition">{{ $crumb['label'] }}</a>
                    <span class="mx-1.5">&rsaquo;</span>
                @else
                    <span class="text-gray-800 font-medium">{{ $crumb['label'] }}</span>
                @endif
            @endforeach
        </nav>
    @endif

    {{-- Class Label --}}
    <p class="text-gray-500 font-medium text-sm mb-6">{{ $course->schoolClass->name ?? 'Kelas' }}</p>

    {{-- Assignment Title + Student Info --}}
    <div class="mb-6">
        <div class="flex items-start gap-3 mb-2">
            <div class="shrink-0 w-10 h-10 rounded-full bg-[#1a6341] flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-[#1a6341]">{{ $assignment->title }}</h3>
                <p class="text-sm text-gray-600 mt-0.5">
                    <span class="font-semibold">{{ $student->user->full_name ?? '-' }}</span>
                    <span class="mx-1.5 text-gray-300">|</span>
                    <span>{{ $student->nisn ?? '-' }}</span>
                </p>
            </div>
        </div>
    </div>

    {{-- Main Content: Two Column Layout --}}
    <div class="flex flex-col lg:flex-row gap-6" x-data="gradingDetail()">

        {{-- LEFT: File Preview --}}
        <div class="flex-1">
            <div class="bg-gray-900 rounded-xl overflow-hidden shadow-lg min-h-[700px] flex flex-col">
                @if($submission && $submission->file_url)
                    @php
                        $fileExt = pathinfo($submission->file_url, PATHINFO_EXTENSION);
                        $fileUrl = asset('storage/' . $submission->file_url);
                        $fileName = basename($submission->file_url);
                        $isPdf = strtolower($fileExt) === 'pdf';
                        $isImage = in_array(strtolower($fileExt), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                    @endphp

                    {{-- File name header bar --}}
                    <div class="flex items-center justify-between bg-gray-800 px-4 py-2.5">
                        <div class="flex items-center gap-2 text-gray-300 text-sm min-w-0">
                            <svg class="w-4 h-4 text-red-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M4 18h12a2 2 0 002-2V6l-4-4H4a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="truncate">{{ $fileName }}</span>
                        </div>
                        <div class="flex items-center gap-2 shrink-0 ml-3">
                            <a href="{{ $fileUrl }}" target="_blank" class="p-1.5 rounded hover:bg-gray-700 text-gray-400 hover:text-white transition" title="Buka di tab baru">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            </a>
                            <a href="{{ $fileUrl }}" download class="p-1.5 rounded hover:bg-gray-700 text-gray-400 hover:text-white transition" title="Download">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            </a>
                        </div>
                    </div>

                    {{-- Preview content --}}
                    <div class="flex-1 relative">
                        @if($isPdf)
                            <iframe src="{{ $fileUrl }}" class="w-full h-full min-h-[650px] border-0" title="PDF Preview"></iframe>
                        @elseif($isImage)
                            <div class="flex items-center justify-center h-full min-h-[650px] p-4 bg-gray-800">
                                <img src="{{ $fileUrl }}" alt="Submission Image" class="max-w-full max-h-[630px] object-contain rounded shadow">
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center h-full min-h-[650px] text-gray-400">
                                <svg class="w-16 h-16 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                <p class="text-sm">Preview tidak tersedia untuk tipe file ini.</p>
                                <a href="{{ $fileUrl }}" download class="mt-3 text-sm text-emerald-400 hover:text-emerald-300 underline">Download File</a>
                            </div>
                        @endif
                    </div>
                @elseif($submission && $submission->submission_text)
                    {{-- Text Submission --}}
                    <div class="flex items-center bg-gray-800 px-4 py-2.5">
                        <span class="text-gray-300 text-sm font-medium">Jawaban Teks</span>
                    </div>
                    <div class="flex-1 p-6 text-gray-200 text-sm leading-relaxed overflow-y-auto max-h-[680px]">
                        {!! nl2br(e($submission->submission_text)) !!}
                    </div>
                @elseif($submission && $submission->link_url)
                    {{-- Link Submission --}}
                    <div class="flex items-center bg-gray-800 px-4 py-2.5">
                        <span class="text-gray-300 text-sm font-medium">Link Jawaban</span>
                    </div>
                    <div class="flex flex-col items-center justify-center h-full min-h-[650px] text-gray-400">
                        <svg class="w-16 h-16 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                        <p class="text-sm mb-2">Siswa melampirkan tautan:</p>
                        <a href="{{ $submission->link_url }}" target="_blank" class="text-emerald-400 hover:text-emerald-300 underline text-sm break-all px-4">{{ $submission->link_url }}</a>
                    </div>
                @else
                    {{-- No Submission --}}
                    <div class="flex flex-col items-center justify-center min-h-[700px] text-gray-500">
                        <svg class="w-16 h-16 mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <p class="text-sm font-medium">Belum Mengumpulkan</p>
                        <p class="text-xs mt-1 opacity-60">Siswa belum mengirimkan jawaban.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- RIGHT: Grading Panel --}}
        <div class="w-full lg:w-80 xl:w-96 space-y-4 self-start lg:sticky lg:top-20">

            {{-- File Info Card --}}
            @if($submission)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h5 class="text-sm font-bold text-gray-800 mb-2">File</h5>
                    <p class="text-xs text-gray-500 mb-3">
                        Diserahkan pada tanggal
                        <span class="font-medium text-gray-700">{{ $submission->submitted_at ? $submission->submitted_at->format('d M, H.i') : '-' }}</span>
                    </p>

                    @if($submission->file_url)
                        <div class="flex items-center gap-2 border-l-4 border-[#1a6341] pl-3 py-1">
                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            <a href="{{ asset('storage/' . $submission->file_url) }}" target="_blank" class="text-sm text-gray-700 hover:text-[#1a6341] truncate transition">
                                {{ Str::limit(basename($submission->file_url), 25) }}
                            </a>
                        </div>
                    @elseif($submission->link_url)
                        <div class="flex items-center gap-2 border-l-4 border-blue-400 pl-3 py-1">
                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101"></path></svg>
                            <a href="{{ $submission->link_url }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 truncate transition">
                                {{ Str::limit($submission->link_url, 30) }}
                            </a>
                        </div>
                    @elseif($submission->submission_text)
                        <div class="flex items-center gap-2 border-l-4 border-amber-400 pl-3 py-1">
                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                            <span class="text-sm text-gray-700">Jawaban teks</span>
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h5 class="text-sm font-bold text-gray-800 mb-2">File</h5>
                    <p class="text-xs text-gray-400 italic">Siswa belum mengumpulkan tugas.</p>
                </div>
            @endif

            {{-- Grading Form --}}
            <form action="{{ route('manage.courses.assignments.submissions.grade', [$course, $assignment, $student]) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Score --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-4">
                    <h5 class="text-sm font-bold text-gray-800 mb-3">Nilai</h5>
                    <div class="flex items-center gap-1">
                        <input type="number" name="score" min="0" max="{{ $assignment->max_score ?? 100 }}"
                               value="{{ old('score', $submission->score ?? '') }}"
                               class="w-20 border border-gray-300 rounded-lg px-3 py-2 text-sm text-center font-semibold focus:ring-[#1a6341] focus:border-[#1a6341]"
                               placeholder="0">
                        <span class="text-gray-500 text-sm font-medium">/ {{ $assignment->max_score ?? 100 }}</span>
                    </div>
                    @error('score')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Feedback --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-4">
                    <h5 class="text-sm font-bold text-gray-800 mb-2">Komentar Pribadi</h5>
                    @if($submission && $submission->feedback)
                        <p class="text-xs text-gray-600 mb-2">{{ $submission->feedback }}</p>
                    @else
                        <p class="text-xs text-gray-400 italic mb-2">Belum ada komentar</p>
                    @endif
                    <textarea name="feedback" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-[#1a6341] focus:border-[#1a6341] resize-y"
                              placeholder="Tulis komentar...">{{ old('feedback', $submission->feedback ?? '') }}</textarea>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('manage.courses.assignments.submissions', [$course, $assignment]) }}"
                       class="flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Cancel
                    </a>
                    <button type="submit"
                            class="flex items-center gap-1.5 px-5 py-2 text-sm font-semibold text-white bg-[#1a6341] hover:bg-[#155c38] rounded-lg shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function gradingDetail() {
        return {};
    }
    </script>
</x-app-layout>
