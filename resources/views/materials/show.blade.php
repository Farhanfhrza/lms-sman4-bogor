<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Breadcrumbs --}}
            <nav class="mb-6" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 text-sm text-gray-600">
                    @foreach($breadcrumbs as $index => $crumb)
                        @if($loop->last)
                            <li class="text-gray-900 font-medium">{{ $crumb['label'] }}</li>
                        @else
                            <li>
                                <a href="{{ $crumb['url'] ?? '#' }}" class="hover:text-[#1a6341] transition-colors">
                                    {{ $crumb['label'] }}
                                </a>
                                <svg class="inline-block w-4 h-4 mx-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </li>
                        @endif
                    @endforeach
                </ol>
            </nav>

            {{-- Header with Course Banner --}}
            <div class="bg-gradient-to-r from-[#1a6341] to-[#2d8659] rounded-lg shadow-lg mb-6 overflow-hidden">
                <div class="px-6 py-8 md:px-8 md:py-10">
                    <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
                        {{ $material->section->classSubject->subject->name ?? 'Course' }}
                    </h1>
                    <p class="text-white/90 text-lg">
                        {{ $material->section->classSubject->schoolClass->name ?? '' }}
                    </p>
                </div>
            </div>

            {{-- Success/Error Messages --}}
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            @endif

            {{-- Material Content Card --}}
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                
                {{-- Material Header --}}
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="bg-[#1a6341] text-white rounded-full p-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">{{ $material->title }}</h2>
                                <p class="text-sm text-gray-500 mt-1">{{ $material->section->title ?? 'Material' }}</p>
                            </div>
                        </div>
                        
                        @if(auth()->user()->hasRole('student'))
                            <form action="{{ route('materials.complete', $material) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 rounded-lg font-medium transition-all duration-200 {{ $isCompleted ? 'bg-green-100 text-green-700 border-2 border-green-500' : 'bg-gray-100 text-gray-700 border-2 border-gray-300 hover:bg-gray-200' }}">
                                    @if($isCompleted)
                                        <svg class="w-5 h-5 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Completed
                                    @else
                                        <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Mark as Complete
                                    @endif
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Material Description --}}
                @if($material->description)
                    <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
                        <h3 class="font-semibold text-gray-900 mb-2">Description</h3>
                        <div class="text-gray-700 prose prose-sm max-w-none">
                            {!! nl2br(e($material->description)) !!}
                        </div>
                    </div>
                @endif

                {{-- Material Content Viewer --}}
                <div class="p-6">
                    @if($material->file_url)
                        @php
                            $fileExtension = strtolower(pathinfo($material->file_url, PATHINFO_EXTENSION));
                            $isPdf = $fileExtension === 'pdf';
                            $isVideo = in_array($fileExtension, ['mp4', 'webm', 'ogg']);
                            $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        @endphp

                        @if($isPdf)
                            {{-- PDF Viewer --}}
                            <div class="bg-gray-100 rounded-lg overflow-hidden" style="height: 600px;">
                                <iframe 
                                    src="{{ asset('storage/' . $material->file_url) }}" 
                                    class="w-full h-full border-0"
                                    title="{{ $material->title }}">
                                </iframe>
                            </div>
                            <div class="mt-4 text-center">
                                <a href="{{ asset('storage/' . $material->file_url) }}" 
                                   download 
                                   class="inline-flex items-center px-6 py-3 bg-[#1a6341] hover:bg-[#155034] text-white font-medium rounded-lg transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Download PDF
                                </a>
                            </div>

                        @elseif($isVideo)
                            {{-- Video Player --}}
                            <div class="bg-black rounded-lg overflow-hidden">
                                <video controls class="w-full" style="max-height: 600px;">
                                    <source src="{{ asset('storage/' . $material->file_url) }}" type="video/{{ $fileExtension }}">
                                    Your browser does not support the video tag.
                                </video>
                            </div>

                        @elseif($isImage)
                            {{-- Image Viewer --}}
                            <div class="text-center">
                                <img src="{{ asset('storage/' . $material->file_url) }}" 
                                     alt="{{ $material->title }}" 
                                     class="max-w-full h-auto rounded-lg shadow-lg mx-auto">
                            </div>

                        @else
                            {{-- Generic File Download --}}
                            <div class="text-center py-12">
                                <svg class="w-20 h-20 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-gray-600 mb-4">File: {{ basename($material->file_url) }}</p>
                                <a href="{{ asset('storage/' . $material->file_url) }}" 
                                   download 
                                   class="inline-flex items-center px-6 py-3 bg-[#1a6341] hover:bg-[#155034] text-white font-medium rounded-lg transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Download File
                                </a>
                            </div>
                        @endif

                    @elseif($material->link_url)
                        @php
                            // Detect YouTube URLs and extract video ID
                            $isYouTube = false;
                            $youtubeId = null;
                            $linkUrl = $material->link_url;

                            if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|v\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $linkUrl, $matches)) {
                                $isYouTube = true;
                                $youtubeId = $matches[1];
                            }
                        @endphp

                        @if($isYouTube && $youtubeId)
                            {{-- YouTube Embedded Player --}}
                            <div class="space-y-4">
                                <div class="relative w-full rounded-xl overflow-hidden shadow-lg bg-black" style="padding-top: 56.25%;">
                                    <iframe 
                                        src="https://www.youtube.com/embed/{{ $youtubeId }}?rel=0&modestbranding=1" 
                                        class="absolute inset-0 w-full h-full"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                        allowfullscreen
                                        title="{{ $material->title }}"
                                    ></iframe>
                                </div>
                                <div class="flex items-center justify-center">
                                    <a href="{{ $linkUrl }}" 
                                       target="_blank" 
                                       rel="noopener noreferrer"
                                       class="inline-flex items-center px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                                        <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                        </svg>
                                        Buka di YouTube
                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @else
                            {{-- Non-YouTube External Link --}}
                            <div class="text-center py-12">
                                <svg class="w-20 h-20 mx-auto text-blue-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                                <p class="text-gray-600 mb-4">This material is hosted externally</p>
                                <a href="{{ $material->link_url }}" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                    Open External Link
                                </a>
                            </div>
                        @endif

                    @else
                        {{-- No Content --}}
                        <div class="text-center py-12 text-gray-500">
                            <svg class="w-20 h-20 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p>No content available for this material.</p>
                        </div>
                    @endif
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
