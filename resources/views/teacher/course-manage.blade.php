<x-app-layout>
    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Breadcrumbs --}}
            <x-breadcrumb :items="$breadcrumbs" />

            {{-- Course Header Banner --}}
            <div class="bg-[#1a6341] rounded-xl p-6 mb-8 shadow-lg">
                <h1 class="text-2xl md:text-3xl font-bold text-white">{{ $course->subject->name ?? 'Course' }}</h1>
                <p class="text-green-200 text-sm mt-1">{{ $course->schoolClass->name ?? '' }}</p>
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center" x-data="{ show: true }" x-show="show">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('success') }}</span>
                    <button @click="show = false" class="ml-auto text-green-500 hover:text-green-700">&times;</button>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Informasi Umum Section --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8" x-data="{ editingInfo: false }">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-800">Informasi Umum</h2>
                    <button @click="editingInfo = !editingInfo" class="text-sm text-[#1a6341] hover:text-[#145232] font-medium flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        <span x-text="editingInfo ? 'Batal' : 'Edit'"></span>
                    </button>
                </div>

                {{-- View Mode --}}
                <div x-show="!editingInfo">
                    @if($course->general_info)
                        <div class="prose prose-sm max-w-none text-gray-700">{!! nl2br(e($course->general_info)) !!}</div>
                    @else
                        <p class="text-gray-400 italic text-sm">Belum ada informasi umum. Klik Edit untuk menambahkan.</p>
                    @endif
                </div>

                {{-- Edit Mode --}}
                <div x-show="editingInfo" x-cloak>
                    <form action="{{ route('manage.courses.update-info', $course) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <textarea name="general_info" rows="8" class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-[#1a6341] focus:border-transparent resize-y" placeholder="Masukkan deskripsi, capaian pembelajaran, pokok bahasan, dan pustaka...">{{ old('general_info', $course->general_info) }}</textarea>
                        <div class="flex justify-end mt-3 gap-2">
                            <button type="button" @click="editingInfo = false" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg">Batal</button>
                            <button type="submit" class="px-4 py-2 text-sm text-white bg-[#1a6341] hover:bg-[#145232] rounded-lg shadow-sm">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Sections (BAB) --}}
            @foreach($course->sections as $section)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6" x-data="{ editingSection: false, showDeleteConfirm: false }">

                    {{-- Section Header --}}
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex-1" x-show="!editingSection">
                            <h3 class="text-lg font-bold text-gray-800">{{ $section->title }}</h3>
                            @if($section->description)
                                <p class="text-sm text-gray-500 mt-1">{{ $section->description }}</p>
                            @endif
                        </div>

                        {{-- Section Edit Form --}}
                        <div class="flex-1" x-show="editingSection" x-cloak>
                            <form action="{{ route('manage.courses.sections.update', [$course, $section]) }}" method="POST" class="space-y-2">
                                @csrf
                                @method('PUT')
                                <input type="text" name="title" value="{{ $section->title }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-bold focus:ring-2 focus:ring-[#1a6341]" required>
                                <textarea name="description" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#1a6341]" placeholder="Deskripsi BAB (opsional)">{{ $section->description }}</textarea>
                                <div class="flex gap-2">
                                    <button type="submit" class="px-3 py-1.5 text-xs bg-[#1a6341] text-white rounded-lg hover:bg-[#145232]">Simpan</button>
                                    <button type="button" @click="editingSection = false" class="px-3 py-1.5 text-xs text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                                </div>
                            </form>
                        </div>

                        {{-- Section Action Buttons --}}
                        <div class="flex items-center gap-2 ml-4" x-show="!editingSection">
                            {{-- Add Content Dropdown --}}
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white bg-[#1a6341] hover:bg-[#145232] rounded-lg shadow-sm transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Tambah Isi
                                </button>
                                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-1 w-40 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-20" style="display: none;">
                                    <a href="{{ route('manage.courses.materials.create', ['course' => $course, 'section_id' => $section->id]) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                        Materi
                                    </a>
                                    <a href="{{ route('manage.courses.assignments.create', ['course' => $course, 'section_id' => $section->id]) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        Penugasan
                                    </a>
                                </div>
                            </div>

                            {{-- Edit Section --}}
                            <button @click="editingSection = true" class="p-1.5 text-gray-400 hover:text-[#1a6341] rounded-lg hover:bg-gray-50" title="Edit BAB">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>

                            {{-- Delete Section --}}
                            <button @click="showDeleteConfirm = true" class="p-1.5 text-gray-400 hover:text-red-500 rounded-lg hover:bg-red-50" title="Hapus BAB">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Delete Confirmation Modal --}}
                    <div x-show="showDeleteConfirm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @click.self="showDeleteConfirm = false">
                        <div class="bg-white rounded-xl p-6 max-w-sm mx-4 shadow-2xl">
                            <h4 class="text-lg font-bold text-gray-800 mb-2">Hapus BAB?</h4>
                            <p class="text-sm text-gray-600 mb-4">Semua materi dan penugasan di dalam BAB <strong>"{{ $section->title }}"</strong> akan ikut terhapus. Tindakan ini tidak dapat dibatalkan.</p>
                            <div class="flex justify-end gap-2">
                                <button @click="showDeleteConfirm = false" class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                                <form action="{{ route('manage.courses.sections.destroy', [$course, $section]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-2 text-sm text-white bg-red-500 hover:bg-red-600 rounded-lg shadow-sm">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Content Items --}}
                    <div class="space-y-3">
                        {{-- Materials --}}
                        @foreach($section->materials as $material)
                            <div class="flex items-center justify-between p-3 bg-blue-50 border border-blue-100 rounded-lg group hover:shadow-sm transition-shadow">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <div class="w-9 h-9 rounded-lg bg-blue-500 flex items-center justify-center flex-shrink-0">
                                        @if($material->content_type === 'video')
                                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M6.3 2.84A1.5 1.5 0 004 4.11v11.78a1.5 1.5 0 002.3 1.27l9.344-5.891a1.5 1.5 0 000-2.538L6.3 2.84z"></path></svg>
                                        @elseif($material->content_type === 'pdf')
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        @else
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $material->title }}</p>
                                        @if($material->description)
                                            <p class="text-xs text-gray-500 truncate">{{ Str::limit($material->description, 60) }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 ml-2 transition-opacity">
                                    <a href="{{ route('manage.courses.materials.edit', [$course, $material]) }}" class="p-1.5 text-gray-400 hover:text-[#1a6341] rounded-lg hover:bg-white" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>
                                    <form action="{{ route('manage.courses.materials.destroy', [$course, $material]) }}" method="POST" onsubmit="return confirm('Hapus materi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 rounded-lg hover:bg-red-50" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            {{-- YouTube Thumbnail Preview --}}
                            @if($material->content_type === 'video' && $material->link_url)
                                @php
                                    $ytId = null;
                                    if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|v\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $material->link_url, $ytMatches)) {
                                        $ytId = $ytMatches[1];
                                    }
                                @endphp
                                @if($ytId)
                                    <div class="ml-12 -mt-1 mb-2">
                                        <div class="relative w-full max-w-md rounded-lg overflow-hidden shadow-sm border border-gray-200" style="padding-top: 28%;">
                                            <img src="https://img.youtube.com/vi/{{ $ytId }}/hqdefault.jpg" alt="Video thumbnail" class="absolute inset-0 w-full h-full object-cover">
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <div class="w-12 h-12 rounded-full bg-red-600 bg-opacity-90 flex items-center justify-center shadow-lg">
                                                    <svg class="w-5 h-5 text-white ml-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M6.3 2.84A1.5 1.5 0 004 4.11v11.78a1.5 1.5 0 002.3 1.27l9.344-5.891a1.5 1.5 0 000-2.538L6.3 2.84z"></path></svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        @endforeach

                        {{-- Assignments --}}
                        @foreach($section->assignments as $assignment)
                            <div class="flex items-center justify-between p-3 bg-orange-50 border border-orange-100 rounded-lg group hover:shadow-sm transition-shadow">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <div class="w-9 h-9 rounded-lg bg-orange-500 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $assignment->title }}</p>
                                        @if($assignment->description)
                                            <p class="text-xs text-gray-500 truncate">{{ Str::limit($assignment->description, 60) }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 ml-2 transition-opacity">
                                    <a href="{{ route('manage.courses.assignments.submissions', [$course, $assignment]) }}" class="p-1.5 text-gray-400 hover:text-emerald-600 rounded-lg hover:bg-emerald-50" title="Penilaian">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                    </a>
                                    <a href="{{ route('manage.courses.assignments.edit', [$course, $assignment]) }}" class="p-1.5 text-gray-400 hover:text-[#1a6341] rounded-lg hover:bg-white" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>
                                    <form action="{{ route('manage.courses.assignments.destroy', [$course, $assignment]) }}" method="POST" onsubmit="return confirm('Hapus penugasan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 rounded-lg hover:bg-red-50" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach

                        {{-- Quizzes --}}
                        @foreach($section->quizzes as $quiz)
                            <div class="flex items-center justify-between p-3 bg-purple-50 border border-purple-100 rounded-lg group hover:shadow-sm transition-shadow">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <div class="w-9 h-9 rounded-lg bg-purple-500 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $quiz->title }}</p>
                                        @if($quiz->description)
                                            <p class="text-xs text-gray-500 truncate">{{ Str::limit($quiz->description, 60) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if($section->materials->isEmpty() && $section->assignments->isEmpty() && $section->quizzes->isEmpty())
                            <p class="text-sm text-gray-400 italic py-2 text-center">Belum ada konten. Klik "+ Tambah Isi" untuk menambahkan.</p>
                        @endif
                    </div>
                </div>
            @endforeach

            {{-- Add New Section (BAB) --}}
            <div class="mt-6" x-data="{ showAddSection: false }">
                <button @click="showAddSection = !showAddSection" class="w-full py-3 border-2 border-dashed border-gray-300 rounded-xl text-gray-500 hover:text-[#1a6341] hover:border-[#1a6341] transition-colors font-medium text-sm flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah BAB
                </button>

                <div x-show="showAddSection" x-cloak x-transition class="mt-4 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Tambah BAB Baru</h3>
                    <form action="{{ route('manage.courses.sections.store', $course) }}" method="POST">
                        @csrf
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Judul BAB <span class="text-red-500">*</span></label>
                                <input type="text" name="title" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#1a6341] focus:border-transparent" placeholder="Contoh: BAB 1 - Pendahuluan">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (Opsional)</label>
                                <textarea name="description" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#1a6341] focus:border-transparent" placeholder="Deskripsi singkat tentang BAB ini..."></textarea>
                            </div>
                        </div>
                        <div class="flex justify-end mt-4 gap-2">
                            <button type="button" @click="showAddSection = false" class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                            <button type="submit" class="px-4 py-2 text-sm text-white bg-[#1a6341] hover:bg-[#145232] rounded-lg shadow-sm">Tambah BAB</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
