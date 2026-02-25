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
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-800">{{ isset($material) ? 'Edit Materi' : 'Tambah Materi' }}</h2>
                    </div>

                    {{-- Action Dropdown --}}
                    <div x-data="{ open: false }" class="relative flex items-center gap-2">
                        <a href="{{ route('manage.courses.show', $course) }}" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-50" title="Kembali">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </a>
                    </div>
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
                    action="{{ isset($material) ? route('manage.courses.materials.update', [$course, $material]) : route('manage.courses.materials.store', $course) }}" 
                    method="POST" 
                    enctype="multipart/form-data"
                    id="materialForm">
                    @csrf
                    @if(isset($material))
                        @method('PUT')
                    @endif
                    <input type="hidden" name="action" id="formAction" value="publish">

                    <div class="p-5 space-y-5">

                        {{-- Section Selector --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">BAB <span class="text-red-500">*</span></label>
                            <select name="section_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1a6341] focus:border-transparent bg-white">
                                <option value="">Pilih BAB</option>
                                @foreach($course->sections as $sec)
                                    <option value="{{ $sec->id }}" {{ (old('section_id', $sectionId ?? ($material->section_id ?? '')) == $sec->id) ? 'selected' : '' }}>
                                        {{ $sec->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Title --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Judul Materi <span class="text-red-500">*</span></label>
                            <input type="text" name="title" value="{{ old('title', $material->title ?? '') }}" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1a6341] focus:border-transparent"
                                placeholder="Masukkan judul materi">
                        </div>

                        {{-- Description --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi Materi (Opsional)</label>
                            <textarea name="description" rows="4" 
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1a6341] focus:border-transparent resize-y"
                                placeholder="Masukkan deskripsi materi...">{{ old('description', $material->description ?? '') }}</textarea>
                        </div>

                        {{-- Content Type --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tipe Konten <span class="text-red-500">*</span></label>
                            <select name="content_type" id="contentType" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1a6341] focus:border-transparent bg-white"
                                onchange="toggleAttachmentFields()">
                                <option value="video" {{ old('content_type', $material->content_type ?? '') == 'video' ? 'selected' : '' }}>Video (YouTube)</option>
                                <option value="pdf" {{ old('content_type', $material->content_type ?? '') == 'pdf' ? 'selected' : '' }}>PDF / Dokumen</option>
                                <option value="link" {{ old('content_type', $material->content_type ?? '') == 'link' ? 'selected' : '' }}>Link Eksternal</option>
                                <option value="image" {{ old('content_type', $material->content_type ?? '') == 'image' ? 'selected' : '' }}>Gambar</option>
                            </select>
                        </div>

                        {{-- Attachment Section --}}
                        <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-700 mb-4">Lampiran</h3>
                            
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                                <button type="button" onclick="setAttachmentType('youtube')" id="btnYoutube"
                                    class="flex flex-col items-center gap-2 p-3 rounded-lg border-2 border-transparent bg-white hover:border-red-300 transition-colors cursor-pointer attachment-btn">
                                    <svg class="w-8 h-8 text-red-600" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                    <span class="text-xs font-medium text-gray-600">YouTube</span>
                                </button>
                                <button type="button" onclick="setAttachmentType('upload')" id="btnUpload"
                                    class="flex flex-col items-center gap-2 p-3 rounded-lg border-2 border-transparent bg-white hover:border-blue-300 transition-colors cursor-pointer attachment-btn">
                                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    <span class="text-xs font-medium text-gray-600">Upload</span>
                                </button>
                                <button type="button" onclick="setAttachmentType('link')" id="btnLink"
                                    class="flex flex-col items-center gap-2 p-3 rounded-lg border-2 border-transparent bg-white hover:border-green-300 transition-colors cursor-pointer attachment-btn">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                    <span class="text-xs font-medium text-gray-600">Link</span>
                                </button>
                                <button type="button" onclick="setAttachmentType('drive')" id="btnDrive"
                                    class="flex flex-col items-center gap-2 p-3 rounded-lg border-2 border-transparent bg-white hover:border-yellow-300 transition-colors cursor-pointer attachment-btn">
                                    <svg class="w-8 h-8 text-yellow-500" viewBox="0 0 24 24" fill="currentColor"><path d="M7.71 3.5L1.15 15l3.43 5.94h6.28L17.43 15H4.57l3.14-5.44V3.5zm8.57 0l-3.43 5.94L16.28 15h6.57l-3.43-5.94L15.86 3.5h.42zm-4.28 7.44L8.57 17h8.57L13.71 11H12z"/></svg>
                                    <span class="text-xs font-medium text-gray-600">Drive</span>
                                </button>
                            </div>

                            {{-- YouTube URL Field --}}
                            <div id="youtubeField" class="hidden">
                                <label class="block text-xs font-medium text-gray-500 mb-1">URL YouTube</label>
                                <input type="url" name="link_url" id="youtubeUrl" value="{{ old('link_url', $material->link_url ?? '') }}" disabled
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-red-400 focus:border-transparent"
                                    placeholder="https://www.youtube.com/watch?v=...">
                            </div>

                            {{-- Upload Field --}}
                            <div id="uploadField" class="hidden">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Upload File</label>
                                <input type="file" name="file" id="fileInput" disabled
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                @if(isset($material) && $material->file_url)
                                    <p class="text-xs text-gray-400 mt-1">File saat ini: {{ basename($material->file_url) }}</p>
                                @endif
                            </div>

                            {{-- Link Field --}}
                            <div id="linkField" class="hidden">
                                <label class="block text-xs font-medium text-gray-500 mb-1">URL Link Eksternal</label>
                                <input type="url" name="link_url" id="linkUrl" value="{{ old('link_url', $material->link_url ?? '') }}" disabled
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-green-400 focus:border-transparent"
                                    placeholder="https://example.com/resource">
                            </div>

                            {{-- Drive Link Field --}}
                            <div id="driveField" class="hidden">
                                <label class="block text-xs font-medium text-gray-500 mb-1">URL Google Drive</label>
                                <input type="url" name="link_url" id="driveUrl" value="{{ old('link_url', $material->link_url ?? '') }}" disabled
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                                    placeholder="https://drive.google.com/file/d/...">
                            </div>
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-100 bg-gray-50">
                        <a href="{{ route('manage.courses.show', $course) }}" class="px-4 py-2.5 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg hover:bg-white transition-colors">
                            Batal
                        </a>
                        <div x-data="{ open: false }" class="relative">
                            <div class="flex">
                                <button type="submit" onclick="document.getElementById('formAction').value='publish'" 
                                    class="px-5 py-2.5 text-sm font-medium text-white bg-[#1a6341] hover:bg-[#145232] rounded-l-lg shadow-sm transition-colors">
                                    Posting
                                </button>
                                <button type="button" @click="open = !open" class="px-2 py-2.5 text-white bg-[#145232] hover:bg-[#0f3d26] rounded-r-lg border-l border-[#1a6341]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                            </div>
                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 bottom-full mb-1 w-40 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-20" style="display: none;">
                                <button type="submit" onclick="document.getElementById('formAction').value='publish'" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Posting</button>
                                <button type="submit" onclick="document.getElementById('formAction').value='draft'" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Simpan Draf</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function setAttachmentType(type) {
            // Hide all fields
            document.getElementById('youtubeField').classList.add('hidden');
            document.getElementById('uploadField').classList.add('hidden');
            document.getElementById('linkField').classList.add('hidden');
            document.getElementById('driveField').classList.add('hidden');

            // Disable all inputs to prevent collision
            document.getElementById('youtubeUrl').disabled = true;
            document.getElementById('fileInput').disabled = true;
            document.getElementById('linkUrl').disabled = true;
            document.getElementById('driveUrl').disabled = true;

            // Remove active state from all buttons
            document.querySelectorAll('.attachment-btn').forEach(btn => {
                btn.classList.remove('border-red-400', 'border-blue-400', 'border-green-400', 'border-yellow-400', 'bg-red-50', 'bg-blue-50', 'bg-green-50', 'bg-yellow-50');
                btn.classList.add('border-transparent');
            });

            // Show selected field, enable input, and highlight button
            if (type === 'youtube') {
                document.getElementById('youtubeField').classList.remove('hidden');
                document.getElementById('youtubeUrl').disabled = false;
                document.getElementById('btnYoutube').classList.remove('border-transparent');
                document.getElementById('btnYoutube').classList.add('border-red-400', 'bg-red-50');
                document.getElementById('contentType').value = 'video';
            } else if (type === 'upload') {
                document.getElementById('uploadField').classList.remove('hidden');
                document.getElementById('fileInput').disabled = false;
                document.getElementById('btnUpload').classList.remove('border-transparent');
                document.getElementById('btnUpload').classList.add('border-blue-400', 'bg-blue-50');
                // Content type inferred from file type usually, but we set to document/image/pdf based on backend logic or just 'pdf' as general 'file'
                // For now keep it simple, backend handles file type detection or just stores it. But we have a dropdown. 
                // Let's set it to 'pdf' as default for upload, or keep user selection?
                // The dropdown logic in toggleAttachmentFields handles the reverse.
                // Here we just set UI.
            } else if (type === 'link') {
                document.getElementById('linkField').classList.remove('hidden');
                document.getElementById('linkUrl').disabled = false;
                document.getElementById('btnLink').classList.remove('border-transparent');
                document.getElementById('btnLink').classList.add('border-green-400', 'bg-green-50');
                document.getElementById('contentType').value = 'link';
            } else if (type === 'drive') {
                document.getElementById('driveField').classList.remove('hidden');
                document.getElementById('driveUrl').disabled = false;
                document.getElementById('btnDrive').classList.remove('border-transparent');
                document.getElementById('btnDrive').classList.add('border-yellow-400', 'bg-yellow-50');
                document.getElementById('contentType').value = 'link';
            }
        }

        function toggleAttachmentFields() {
            var contentType = document.getElementById('contentType').value;
            if (contentType === 'video') {
                setAttachmentType('youtube');
            } else if (contentType === 'link') {
                setAttachmentType('link');
            } else {
                setAttachmentType('upload');
            }
        }

        // Initialize on load
        document.addEventListener('DOMContentLoaded', function() {
            var contentType = document.getElementById('contentType').value;
            @if(isset($material) && $material->link_url)
                @if(Str::contains($material->link_url, ['youtube.com', 'youtu.be']))
                    setAttachmentType('youtube');
                @elseif(Str::contains($material->link_url, 'drive.google.com'))
                    setAttachmentType('drive');
                @else
                    setAttachmentType('link');
                @endif
            @elseif(isset($material) && $material->file_url)
                setAttachmentType('upload');
            @else
                if (contentType === 'video') setAttachmentType('youtube');
                else if (contentType === 'link') setAttachmentType('link');
                else setAttachmentType('upload'); // Default
            @endif
        });
    </script>
</x-app-layout>
