<x-app-layout>
    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Breadcrumbs --}}
            <x-breadcrumb :items="$breadcrumbs" />

            {{-- Course Header Banner --}}
            <div class="bg-[#1a6341] rounded-xl p-6 mb-8 shadow-lg">
                <h1 class="text-2xl md:text-3xl font-bold text-white">{{ $course->subject->name ?? 'Course' }}</h1>
                <p class="text-green-200 text-sm mt-1">{{ $course->schoolClass->name ?? '' }}</p>
            </div>

            {{-- Form Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

                {{-- Card Header --}}
                <div class="flex items-center justify-between p-5 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-800">{{ isset($assignment) ? 'Edit Penugasan' : 'Tambah Penugasan' }}</h2>
                    </div>
                    <a href="{{ route('manage.courses.show', $course) }}" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-50" title="Kembali">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </a>
                </div>

                {{-- Error Messages --}}
                @if($errors->any())
                    <div class="mx-5 mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        <ul class="list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Form Body --}}
                <form 
                    action="{{ isset($assignment) ? route('manage.courses.assignments.update', [$course, $assignment]) : route('manage.courses.assignments.store', $course) }}" 
                    method="POST" 
                    enctype="multipart/form-data">
                    @csrf
                    @if(isset($assignment))
                        @method('PUT')
                    @endif

                    <div class="p-5 space-y-5">

                        {{-- Section Selector --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">BAB <span class="text-red-500">*</span></label>
                            <select name="section_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1a6341] focus:border-transparent bg-white">
                                <option value="">Pilih BAB</option>
                                @foreach($course->sections as $sec)
                                    <option value="{{ $sec->id }}" {{ (old('section_id', $sectionId ?? ($assignment->section_id ?? '')) == $sec->id) ? 'selected' : '' }}>
                                        {{ $sec->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Title --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Judul Penugasan <span class="text-red-500">*</span></label>
                            <input type="text" name="title" value="{{ old('title', $assignment->title ?? '') }}" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1a6341] focus:border-transparent"
                                placeholder="Masukkan judul tugas">
                        </div>

                        {{-- Description --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi / Instruksi</label>
                            <textarea name="description" rows="5" 
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1a6341] focus:border-transparent resize-y"
                                placeholder="Jelaskan tugas yang harus dikerjakan siswa...">{{ old('description', $assignment->description ?? '') }}</textarea>
                        </div>

                        {{-- Due Date & Time --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Deadline</label>
                                <input type="date" name="due_date" 
                                    value="{{ old('due_date', isset($assignment) && $assignment->due_date ? $assignment->due_date->format('Y-m-d') : '') }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1a6341] focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jam Deadline</label>
                                <input type="time" name="due_time" 
                                    value="{{ old('due_time', isset($assignment) && $assignment->due_date ? $assignment->due_date->format('H:i') : '23:59') }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1a6341] focus:border-transparent">
                            </div>
                        </div>

                        {{-- Allow Late Submission --}}
                        <div class="flex items-start gap-3">
                            <div class="flex items-center h-6 mt-0.5">
                                <input type="checkbox" name="allow_late_submission" id="allow_late_submission" value="1"
                                    {{ old('allow_late_submission', isset($assignment) && $assignment->allow_late_submission ? true : false) ? 'checked' : '' }}
                                    class="w-4 h-4 rounded border-gray-300 text-[#1a6341] focus:ring-[#1a6341]">
                            </div>
                            <label for="allow_late_submission" class="cursor-pointer">
                                <span class="block text-sm font-semibold text-gray-700">Izinkan pengumpulan setelah batas waktu</span>
                                <span class="block text-xs text-gray-500 mt-0.5">Siswa masih bisa mengumpulkan tugas setelah melewati deadline, namun akan ditandai sebagai <em>Telat</em>.</span>
                            </label>
                        </div>

                        {{-- Max Score --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nilai Maksimal</label>
                            <input type="number" name="max_score" min="0" max="100" 
                                value="{{ old('max_score', $assignment->max_score ?? 100) }}"
                                class="w-40 border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1a6341] focus:border-transparent">
                        </div>

                        {{-- File Attachment --}}
                        <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Lampiran File (Opsional)</h3>
                            <p class="text-xs text-gray-500 mb-3">Upload file soal atau resource tambahan untuk siswa.</p>
                            <input type="file" name="file" 
                                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                            @if(isset($assignment) && $assignment->file_url)
                                <p class="text-xs text-gray-400 mt-2">File saat ini: <a href="{{ $assignment->file_url }}" target="_blank" class="text-blue-500 hover:underline">{{ basename($assignment->file_url) }}</a></p>
                            @endif
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-100 bg-gray-50">
                        <a href="{{ route('manage.courses.show', $course) }}" class="px-4 py-2.5 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg hover:bg-white transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-[#1a6341] hover:bg-[#145232] rounded-lg shadow-sm transition-colors">
                            {{ isset($assignment) ? 'Perbarui' : 'Posting' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
