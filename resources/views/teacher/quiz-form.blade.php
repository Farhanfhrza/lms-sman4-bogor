<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ $course->subject->name ?? 'Mata Pelajaran' }}
        </h2>
    </x-slot>

    {{-- QuillJS CSS --}}
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    {{-- Compressor.js for client-side image compression --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/compressorjs/1.2.1/compressor.min.js"></script>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

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
                        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="flex items-center gap-3">
                            <h2 class="text-lg font-bold text-gray-800">{{ isset($quiz) ? 'Edit Kuis' : 'Tambah Kuis' }}</h2>
                            <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">Segera</span>
                        </div>
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

                {{-- Form Body (Alpine.js quiz builder) --}}
                <form
                    action="{{ isset($quiz) ? route('manage.courses.quizzes.update', [$course, $quiz]) : route('manage.courses.quizzes.store', $course) }}"
                    method="POST"
                    enctype="multipart/form-data"
                    x-data="quizBuilder()"
                    @submit.prevent="submitForm($event)"
                    id="quizForm">
                    @csrf
                    @if(isset($quiz))
                        @method('PUT')
                    @endif

                    <div class="p-5 space-y-5">

                        {{-- Section Selector --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">BAB <span class="text-red-500">*</span></label>
                            <select name="section_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1a6341] focus:border-transparent bg-white">
                                <option value="">Pilih BAB</option>
                                @foreach($course->sections as $sec)
                                    <option value="{{ $sec->id }}" {{ (old('section_id', $sectionId ?? (isset($quiz) ? $quiz->section_id : '')) == $sec->id) ? 'selected' : '' }}>
                                        {{ $sec->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Title --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Judul Kuis <span class="text-red-500">*</span></label>
                            <input type="text" name="title" value="{{ old('title', $quiz->title ?? '') }}" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1a6341] focus:border-transparent"
                                placeholder="Kuis Sejarah Indonesia">
                        </div>

                        {{-- Subtitle --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Sub Judul Kuis (Opsional)</label>
                            <input type="text" name="subtitle" value="{{ old('subtitle', $subtitle ?? '') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1a6341] focus:border-transparent"
                                placeholder="Perjuangan Bangsa Indonesia Menuju Kemerdekaan">
                        </div>

                        {{-- Description (Quill WYSIWYG) --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi Kuis (Opsional)</label>
                            <div id="quizDescriptionEditor" class="bg-white rounded-lg border border-gray-300 min-h-[120px]"></div>
                            <input type="hidden" name="description" id="quizDescriptionInput">
                        </div>

                        {{-- Date, Time, Duration --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal</label>
                                <input type="date" name="due_date" value="{{ old('due_date', isset($quiz) && $quiz->end_at ? $quiz->end_at->format('Y-m-d') : '') }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1a6341] focus:border-transparent">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Waktu</label>
                                    <input type="time" name="due_time" value="{{ old('due_time', isset($quiz) && $quiz->end_at ? $quiz->end_at->format('H:i') : '21:55') }}"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1a6341] focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Durasi</label>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm text-gray-500">Menit</span>
                                        <input type="number" name="time_limit" value="{{ old('time_limit', $quiz->time_limit ?? 120) }}" min="1" max="600"
                                            class="w-20 border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1a6341] focus:border-transparent text-center">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Close on deadline --}}
                        <div class="flex items-center gap-3">
                            <div class="flex items-center h-6">
                                <input type="checkbox" name="close_on_deadline" id="close_on_deadline" value="1"
                                    {{ old('close_on_deadline') ? 'checked' : '' }}
                                    class="w-4 h-4 rounded border-gray-300 text-[#1a6341] focus:ring-[#1a6341]">
                            </div>
                            <label for="close_on_deadline" class="cursor-pointer">
                                <span class="text-sm font-semibold text-gray-700">Tutup Kuis setelah batas waktu</span>
                            </label>
                        </div>
                    </div>

                    {{-- DIVIDER --}}
                    <div class="border-t border-gray-200 mx-5 my-2"></div>

                    {{-- SCORING MODE TOGGLE + TOTAL INDICATOR --}}
                    <div class="px-5 pt-4 pb-2">
                        <input type="hidden" name="scoring_mode" :value="scoringMode">

                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 rounded-xl bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-100">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                    <span class="text-sm font-bold text-gray-800">Penilaian</span>
                                </div>
                                <div class="flex bg-white rounded-lg border border-gray-200 p-0.5">
                                    <button type="button"
                                        @click="scoringMode = 'auto'"
                                        :class="scoringMode === 'auto' ? 'bg-[#1a6341] text-white shadow-sm' : 'text-gray-600 hover:text-gray-800'"
                                        class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all">
                                        Otomatis
                                    </button>
                                    <button type="button"
                                        @click="scoringMode = 'manual'"
                                        :class="scoringMode === 'manual' ? 'bg-[#1a6341] text-white shadow-sm' : 'text-gray-600 hover:text-gray-800'"
                                        class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all">
                                        Manual
                                    </button>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <template x-if="scoringMode === 'auto'">
                                    <span class="text-xs text-purple-700 font-medium">
                                        Total: <strong>100</strong> poin
                                        (<span x-text="questions.length"></span> soal × <span x-text="(100 / questions.length).toFixed(1)"></span> poin)
                                    </span>
                                </template>
                                <template x-if="scoringMode === 'manual'">
                                    <span class="text-xs font-medium"
                                        :class="totalManualPoints() === 100 ? 'text-green-700' : 'text-red-600'">
                                        Total: <strong x-text="totalManualPoints()"></strong> / 100 poin
                                        <span x-show="totalManualPoints() !== 100" class="ml-1">⚠️</span>
                                        <span x-show="totalManualPoints() === 100" class="ml-1">✅</span>
                                    </span>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- QUESTION BUILDER --}}
                    <div class="p-5 space-y-4">
                        <div id="questionContainer" class="space-y-5">
                            <template x-for="(question, qIdx) in questions" :key="question.id">
                                <div class="rounded-xl border-2 border-[#1a6341] bg-white overflow-hidden shadow-sm"
                                     :data-question-id="question.id">

                                    {{-- Question Header Bar: Number + Move Up/Down --}}
                                    <div class="flex items-center justify-between px-4 py-2 bg-gray-50 border-b border-gray-100">
                                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">
                                            Soal <span x-text="qIdx + 1"></span>
                                        </span>
                                        <div class="flex items-center gap-0.5">
                                            {{-- Move Up --}}
                                            <button type="button" @click="moveQuestionUp(qIdx)"
                                                x-show="qIdx > 0"
                                                class="p-1.5 text-gray-400 hover:text-[#1a6341] hover:bg-white rounded-lg transition" title="Pindah ke atas">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                            </button>
                                            {{-- Move Down --}}
                                            <button type="button" @click="moveQuestionDown(qIdx)"
                                                x-show="qIdx < questions.length - 1"
                                                class="p-1.5 text-gray-400 hover:text-[#1a6341] hover:bg-white rounded-lg transition" title="Pindah ke bawah">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="p-4">
                                        {{-- Question header: Text input + Points --}}
                                        <div class="flex items-start gap-3 mb-3">
                                            <div class="flex-1">
                                                {{-- Quill editor per question --}}
                                                <div class="quill-question-editor border border-gray-300 rounded-lg min-h-[80px]"
                                                     :id="'questionEditor_' + question.id"
                                                     x-init="$nextTick(() => initQuill(question.id, question.text))"></div>
                                                <input type="hidden" :name="'questions[' + qIdx + '][text]'" :id="'questionText_' + question.id">
                                                <input type="hidden" :name="'questions[' + qIdx + '][existing_image]'" :value="question.existingImageUrl || ''">
                                            </div>
                                            {{-- Points input (only visible in manual mode) --}}
                                            <div class="flex items-center gap-1.5 shrink-0 pt-1" x-show="scoringMode === 'manual'" x-cloak>
                                                <input type="number" :name="'questions[' + qIdx + '][points]'"
                                                    x-model.number="question.points" min="1" max="100"
                                                    class="w-16 border border-gray-300 rounded-lg px-2 py-2 text-sm text-center focus:ring-2 focus:ring-[#1a6341] focus:border-transparent">
                                                <span class="text-xs text-gray-500 font-medium">Poin</span>
                                            </div>
                                            {{-- Image upload button --}}
                                            <div class="shrink-0 pt-1">
                                                <label :for="'qImage_' + question.id"
                                                       class="flex items-center justify-center w-9 h-9 rounded-lg cursor-pointer"
                                                       :class="question.imagePreview ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'"
                                                       title="Tambah Gambar">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                </label>
                                                <input type="file" :id="'qImage_' + question.id" :name="'questions[' + qIdx + '][image]'"
                                                    accept="image/*" class="hidden" @change="handleImageUpload($event, qIdx)">
                                            </div>
                                        </div>

                                        {{-- Image preview --}}
                                        <div x-show="question.imagePreview" class="mb-3 relative" x-cloak>
                                            <img :src="question.imagePreview" class="max-h-48 rounded-lg border border-gray-200 shadow-sm">
                                            <button type="button" @click="removeImage(qIdx)"
                                                class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 shadow">
                                                &times;
                                            </button>
                                        </div>

                                        {{-- Hidden input for correct_option (always submitted) --}}
                                        <input type="hidden" :name="'questions[' + qIdx + '][correct_option]'" :value="question.correctOption">

                                        {{-- Options --}}
                                        <div class="space-y-2 mb-3">
                                            <template x-for="(option, oIdx) in question.options" :key="option.id">
                                                <div class="flex items-center gap-2 group">
                                                    {{-- Radio button for correct answer (visual only, drives Alpine state) --}}
                                                    <input type="radio"
                                                        :name="'q_radio_' + question.id"
                                                        :checked="question.correctOption === oIdx"
                                                        @change="question.correctOption = oIdx"
                                                        class="w-4 h-4 text-[#1a6341] focus:ring-[#1a6341] border-gray-300">
                                                    {{-- Option text --}}
                                                    <input type="text" :name="'questions[' + qIdx + '][options][' + oIdx + '][text]'"
                                                        x-model="option.text"
                                                        class="flex-1 border-0 border-b border-gray-300 px-1 py-1.5 text-sm focus:ring-0 focus:border-[#1a6341] bg-transparent"
                                                        :placeholder="'Opsi ' + (oIdx + 1)">
                                                    {{-- Delete option --}}
                                                    <button type="button" @click="removeOption(qIdx, oIdx)"
                                                        x-show="question.options.length > 2"
                                                        class="opacity-0 group-hover:opacity-100 p-1 text-gray-400 hover:text-red-500 transition-opacity" title="Hapus opsi">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    </button>
                                                </div>
                                            </template>

                                            {{-- Add option --}}
                                            <button type="button" @click="addOption(qIdx)" class="flex items-center gap-2 mt-1 ml-6 text-sm text-gray-500 hover:text-[#1a6341] transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                <span class="border-b border-dashed border-gray-300">Tambahkan opsi</span>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Question Footer: actions --}}
                                    <div class="flex items-center justify-between px-4 py-2.5 border-t border-gray-100 bg-gray-50">
                                        {{-- Left: Add question + Add media --}}
                                        <div class="flex items-center gap-1">
                                            <button type="button" @click="addQuestion(qIdx)"
                                                class="flex items-center justify-center w-9 h-9 rounded-lg bg-[#1a6341] text-white hover:bg-[#155c38] shadow-sm transition" title="Tambah Soal">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            </button>
                                            <label :for="'qImage_' + question.id"
                                                class="flex items-center justify-center w-9 h-9 rounded-lg bg-[#1a6341] text-white hover:bg-[#155c38] shadow-sm transition cursor-pointer" title="Tambah Gambar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </label>
                                        </div>
                                        {{-- Right: Duplicate + Delete --}}
                                        <div class="flex items-center gap-1">
                                            <button type="button" @click="duplicateQuestion(qIdx)"
                                                class="p-2 text-gray-500 hover:text-[#1a6341] hover:bg-white rounded-lg transition" title="Duplikat Soal">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                            </button>
                                            <button type="button" @click="deleteQuestion(qIdx)"
                                                x-show="questions.length > 1"
                                                class="p-2 text-gray-500 hover:text-red-500 hover:bg-red-50 rounded-lg transition" title="Hapus Soal">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-100 bg-gray-50">
                        <a href="{{ route('manage.courses.show', $course) }}" class="px-4 py-2.5 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg hover:bg-white transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-[#1a6341] hover:bg-[#145232] rounded-lg shadow-sm transition-colors">
                            {{ isset($quiz) ? 'Perbarui' : 'Posting' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- QuillJS CDN --}}
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

    <style>
        /* Make Quill editors blend in with the form */
        .quill-question-editor .ql-toolbar {
            border: none !important;
            border-bottom: 1px solid #e5e7eb !important;
            padding: 4px 8px !important;
            background: #f9fafb;
            border-radius: 8px 8px 0 0;
        }
        .quill-question-editor .ql-container {
            border: none !important;
            font-size: 0.875rem;
            min-height: 44px;
        }
        .quill-question-editor .ql-editor {
            padding: 8px 12px;
            min-height: 44px;
        }
        .quill-question-editor .ql-editor.ql-blank::before {
            color: #9ca3af;
            font-style: normal;
        }
        #quizDescriptionEditor .ql-toolbar {
            border: none !important;
            border-bottom: 1px solid #e5e7eb !important;
            padding: 6px 8px !important;
            background: #f9fafb;
            border-radius: 8px 8px 0 0;
        }
        #quizDescriptionEditor .ql-container {
            border: none !important;
            font-size: 0.875rem;
            min-height: 80px;
        }
        #quizDescriptionEditor .ql-editor {
            padding: 10px 14px;
            min-height: 80px;
        }
    </style>

    <script>
        // Quill toolbar preset
        const quillToolbar = [
            ['bold', 'italic', 'underline'],
        ];

        // Description editor (single, outside Alpine)
        let descriptionQuill = null;

        // Existing data passed from server (edit mode)
        const existingQuestions = @json($existingQuestions ?? null);
        const existingDescription = @json($descriptionBody ?? '');
        const existingScoringMode = @json($scoringMode ?? 'auto');

        document.addEventListener('DOMContentLoaded', function() {
            descriptionQuill = new Quill('#quizDescriptionEditor', {
                theme: 'snow',
                modules: { toolbar: quillToolbar },
                placeholder: 'Kuis ini bertujuan untuk menguji pemahaman peserta didik...',
            });
            // Pre-fill description in edit mode
            if (existingDescription) {
                descriptionQuill.root.innerHTML = existingDescription;
            }
        });

        function quizBuilder() {
            const defaultQuestions = [
                {
                    id: 1,
                    text: '',
                    points: 20,
                    imagePreview: null,
                    existingImageUrl: null,
                    correctOption: 0,
                    options: [
                        { id: 1, text: '' },
                        { id: 2, text: '' },
                    ],
                    nextOptionId: 3,
                }
            ];

            return {
                nextId: existingQuestions ? existingQuestions.length + 1 : 2,
                quillInstances: {},
                scoringMode: existingScoringMode || 'auto',

                questions: existingQuestions || defaultQuestions,

                init() {
                    // No sortable init needed
                },

                totalManualPoints() {
                    return this.questions.reduce((sum, q) => sum + (parseInt(q.points) || 0), 0);
                },

                // --- Move question Up/Down ---
                moveQuestionUp(idx) {
                    if (idx <= 0) return;
                    this.swapQuestions(idx, idx - 1);
                },

                moveQuestionDown(idx) {
                    if (idx >= this.questions.length - 1) return;
                    this.swapQuestions(idx, idx + 1);
                },

                swapQuestions(fromIdx, toIdx) {
                    // 1. Save Quill HTML content to state before swapping
                    this.syncAllQuillToState();

                    // 2. Destroy Quill instances for both questions
                    const fromId = this.questions[fromIdx].id;
                    const toId = this.questions[toIdx].id;
                    this.destroyQuill(fromId);
                    this.destroyQuill(toId);

                    // 3. Swap in the array
                    const temp = this.questions[fromIdx];
                    this.questions[fromIdx] = this.questions[toIdx];
                    this.questions[toIdx] = temp;

                    // Force reactivity
                    this.questions = [...this.questions];

                    // 4. Re-initialize Quill after DOM update
                    this.$nextTick(() => {
                        this.initQuill(this.questions[fromIdx].id, this.questions[fromIdx].text);
                        this.initQuill(this.questions[toIdx].id, this.questions[toIdx].text);
                    });
                },

                syncAllQuillToState() {
                    this.questions.forEach((q) => {
                        const quill = this.quillInstances[q.id];
                        if (quill) {
                            q.text = quill.root.innerHTML;
                        }
                    });
                },

                destroyQuill(questionId) {
                    if (this.quillInstances[questionId]) {
                        delete this.quillInstances[questionId];
                    }
                },

                initQuill(questionId, existingHtml) {
                    const editorEl = document.getElementById('questionEditor_' + questionId);
                    if (!editorEl || this.quillInstances[questionId]) return;

                    // Clear any leftover Quill DOM from previous instance
                    const oldToolbar = editorEl.querySelector('.ql-toolbar');
                    const oldContainer = editorEl.querySelector('.ql-container');
                    if (oldToolbar) oldToolbar.remove();
                    if (oldContainer) oldContainer.remove();

                    const quill = new Quill(editorEl, {
                        theme: 'snow',
                        modules: { toolbar: quillToolbar },
                        placeholder: 'Pertanyaan',
                    });

                    // Pre-fill with existing HTML (edit mode or after swap)
                    if (existingHtml && existingHtml !== '<p><br></p>') {
                        quill.root.innerHTML = existingHtml;
                    }

                    this.quillInstances[questionId] = quill;
                },

                addQuestion(afterIdx) {
                    const newQ = {
                        id: this.nextId++,
                        text: '',
                        points: 20,
                        imagePreview: null,
                        existingImageUrl: null,
                        correctOption: 0,
                        options: [
                            { id: 1, text: '' },
                            { id: 2, text: '' },
                        ],
                        nextOptionId: 3,
                    };
                    this.questions.splice(afterIdx + 1, 0, newQ);
                },

                duplicateQuestion(idx) {
                    // Save current Quill state first
                    this.syncAllQuillToState();

                    const src = this.questions[idx];
                    const dupe = {
                        id: this.nextId++,
                        text: src.text,
                        points: src.points,
                        imagePreview: null,
                        existingImageUrl: null,
                        correctOption: src.correctOption,
                        options: src.options.map(o => ({ id: this.nextId++, text: o.text })),
                        nextOptionId: src.options.length + 1,
                    };
                    this.questions.splice(idx + 1, 0, dupe);
                },

                deleteQuestion(idx) {
                    if (this.questions.length <= 1) return;
                    const qId = this.questions[idx].id;
                    delete this.quillInstances[qId];
                    this.questions.splice(idx, 1);
                },

                addOption(qIdx) {
                    const q = this.questions[qIdx];
                    q.options.push({ id: q.nextOptionId++, text: '' });
                },

                removeOption(qIdx, oIdx) {
                    const q = this.questions[qIdx];
                    if (q.options.length <= 2) return;
                    q.options.splice(oIdx, 1);
                    // Adjust correct option index
                    if (q.correctOption >= q.options.length) {
                        q.correctOption = q.options.length - 1;
                    }
                },

                handleImageUpload(event, qIdx) {
                    const file = event.target.files[0];
                    if (!file) return;

                    // Compress the image before showing preview & submitting
                    new Compressor(file, {
                        quality: 0.7,
                        maxWidth: 1200, // Limit max width so it doesn't break layout or memory
                        success: (compressedResult) => {
                            // 1. Show Preview
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.questions[qIdx].imagePreview = e.target.result;
                            };
                            reader.readAsDataURL(compressedResult);

                            // 2. Replace the file inside the actual <input type="file">
                            const dataTransfer = new DataTransfer();
                            // Create a new File object from the compressed blob
                            const compressedFile = new File([compressedResult], file.name, {
                                type: compressedResult.type,
                                lastModified: Date.now(),
                            });
                            dataTransfer.items.add(compressedFile);
                            event.target.files = dataTransfer.files;
                        },
                        error: (err) => {
                            console.error('Image compression failed:', err.message);
                            // Fallback to original
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.questions[qIdx].imagePreview = e.target.result;
                            };
                            reader.readAsDataURL(file);
                        },
                    });
                },

                removeImage(qIdx) {
                    this.questions[qIdx].imagePreview = null;
                    this.questions[qIdx].existingImageUrl = null;
                    // Clear file input
                    const input = document.getElementById('qImage_' + this.questions[qIdx].id);
                    if (input) input.value = '';
                },

                submitForm(event) {
                    // Sync Quill editors to hidden inputs
                    this.questions.forEach((q, idx) => {
                        const quill = this.quillInstances[q.id];
                        const hiddenInput = document.getElementById('questionText_' + q.id);
                        if (quill && hiddenInput) {
                            hiddenInput.value = quill.root.innerHTML;
                        }
                    });

                    // Sync description
                    if (descriptionQuill) {
                        document.getElementById('quizDescriptionInput').value = descriptionQuill.root.innerHTML;
                    }

                    // Submit the actual form
                    event.target.submit();
                },
            };
        }
    </script>
</x-app-layout>
