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
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg shadow-lg mb-6 overflow-hidden">
                <div class="px-6 py-8 md:px-8 md:py-10">
                    <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
                        {{ $assignment->section->classSubject->subject->name ?? 'Course' }}
                    </h1>
                    <p class="text-white/90 text-lg">
                        {{ $assignment->section->classSubject->schoolClass->name ?? '' }}
                    </p>
                </div>
            </div>

            {{-- Success/Error Messages --}}
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                    <p class="font-medium">Please correct the following errors:</p>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- Left Column: Assignment Details --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        
                        {{-- Assignment Header --}}
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                            <div class="flex items-start space-x-3">
                                <div class="bg-orange-500 text-white rounded-full p-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h2 class="text-2xl font-bold text-gray-900">{{ $assignment->title }}</h2>
                                    <p class="text-sm text-gray-500 mt-1">{{ $assignment->section->title ?? 'Assignment' }}</p>
                                    
                                    {{-- Due Date --}}
                                    @if($assignment->due_date)
                                        <div class="mt-3 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $isOverdue ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            Due: {{ \Carbon\Carbon::parse($assignment->due_date)->format('d M Y, H:i') }}
                                            @if($isOverdue)
                                                <span class="ml-1">(Overdue)</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Assignment Instructions --}}
                        <div class="p-6">
                            <h3 class="font-bold text-lg text-gray-900 mb-3">Instructions</h3>
                            <div class="prose prose-sm max-w-none text-gray-700 bg-gray-50 p-4 rounded-lg">
                                {!! nl2br(e($assignment->description ?? 'No instructions provided.')) !!}
                            </div>

                            {{-- Questions List --}}
                            @if($assignment->description && str_contains($assignment->description, "\n"))
                                <div class="mt-6">
                                    <h4 class="font-semibold text-gray-900 mb-3">Tasks:</h4>
                                    <ol class="space-y-2 text-gray-700">
                                        @foreach(explode("\n", $assignment->description) as $index => $line)
                                            @if(trim($line))
                                                <li class="flex items-start">
                                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-orange-100 text-orange-600 text-sm font-medium mr-3 flex-shrink-0">
                                                        {{ $index + 1 }}
                                                    </span>
                                                    <span class="flex-1">{{ $line }}</span>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ol>
                                </div>
                            @endif

                            {{-- Attached Files --}}
                            @if($assignment->file_url)
                                <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                                    <h4 class="font-semibold text-gray-900 mb-2">Attached File</h4>
                                    <a href="{{ asset('storage/' . $assignment->file_url) }}" 
                                       target="_blank"
                                       class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        {{ basename($assignment->file_url) }}
                                    </a>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>

                {{-- Right Column: Submission Panel (Students Only) --}}
                @if(auth()->user()->hasRole('student'))
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow-md overflow-hidden sticky top-6">
                            
                            {{-- Submission Status --}}
                            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                                <h3 class="font-bold text-gray-900">Your Submission</h3>
                                @if($submission)
                                    <div class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Submitted
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ \Carbon\Carbon::parse($submission->submitted_at)->format('d M Y, H:i') }}
                                    </p>
                                @else
                                    <div class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        Not Submitted
                                    </div>
                                @endif
                            </div>

                            {{-- Submission Form --}}
                            <div class="p-6">
                                @if($canSubmit || $submission)
                                    <form action="{{ route('assignments.submit', $assignment) }}" 
                                          method="POST" 
                                          enctype="multipart/form-data"
                                          x-data="{ 
                                              uploading: false, 
                                              uploadType: 'file',
                                              hasInput: {{ $submission ? 'true' : 'false' }},
                                              checkInput() {
                                                  const file = $refs.fileInput ? $refs.fileInput.files.length > 0 : false;
                                                  const drive = $refs.driveInput ? $refs.driveInput.value.trim().length > 0 : false;
                                                  const link = $refs.linkInput ? $refs.linkInput.value.trim().length > 0 : false;
                                                  const text = $refs.textInput ? $refs.textInput.value.trim().length > 0 : false;
                                                  const existing = {{ $submission ? 'true' : 'false' }};
                                                  this.hasInput = file || drive || link || text || existing;
                                              }
                                          }">
                                        @csrf

                                        {{-- File Upload Section --}}
                                        <div class="mb-6">
                                            <label class="block text-sm font-semibold text-gray-700 mb-3">File</label>
                                            
                                            {{-- Show Existing File --}}
                                            @if($submission && $submission->file_url)
                                                <div class="mb-3 p-3 bg-gray-50 rounded border border-gray-200">
                                                    <p class="text-sm text-gray-600 mb-2">Current file:</p>
                                                    <a href="{{ asset('storage/' . $submission->file_url) }}" 
                                                       target="_blank" 
                                                       class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                        </svg>
                                                        {{ basename($submission->file_url) }}
                                                    </a>
                                                </div>
                                            @endif

                                            {{-- Upload Options --}}
                                            <div class="grid grid-cols-3 gap-2 mb-3">
                                                <button type="button" 
                                                        @click="uploadType = 'drive'"
                                                        :class="uploadType === 'drive' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                                        class="flex flex-col items-center justify-center py-3 rounded-lg transition-colors">
                                                    <svg class="w-6 h-6 mb-1" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12.545,10.239v3.821h5.445c-0.712,2.315-2.647,3.972-5.445,3.972c-3.332,0-6.033-2.701-6.033-6.032s2.701-6.032,6.033-6.032c1.498,0,2.866,0.549,3.921,1.453l2.814-2.814C17.503,2.988,15.139,2,12.545,2C7.021,2,2.543,6.477,2.543,12s4.478,10,10.002,10c8.396,0,10.249-7.85,9.426-11.748L12.545,10.239z"></path>
                                                    </svg>
                                                    <span class="text-xs">Drive</span>
                                                </button>
                                                
                                                <button type="button"
                                                        @click="uploadType = 'file'"
                                                        :class="uploadType === 'file' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                                        class="flex flex-col items-center justify-center py-3 rounded-lg transition-colors">
                                                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                                    </svg>
                                                    <span class="text-xs">Upload</span>
                                                </button>
                                                
                                                <button type="button"
                                                        @click="uploadType = 'link'"
                                                        :class="uploadType === 'link' ? 'bg-purple-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                                        class="flex flex-col items-center justify-center py-3 rounded-lg transition-colors">
                                                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                                    </svg>
                                                    <span class="text-xs">Link</span>
                                                </button>
                                            </div>

                                            {{-- Drive Link Input --}}
                                            <div x-show="uploadType === 'drive'" x-cloak>
                                                <input type="url" 
                                                       name="link" 
                                                       x-ref="driveInput"
                                                       @input="checkInput()"
                                                       placeholder="Paste Google Drive link here"
                                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                       :value="'{{ $submission->link_url ?? '' }}'">
                                                <p class="text-xs text-gray-500 mt-1">Make sure the file is shared publicly</p>
                                            </div>

                                            {{-- File Upload Input --}}
                                            <div x-show="uploadType === 'file'" x-cloak>
                                                <input type="file" 
                                                       name="file"
                                                       x-ref="fileInput"
                                                       @change="checkInput(); uploading = true"
                                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                                                <p class="text-xs text-gray-500 mt-1">Max file size: 10MB</p>
                                            </div>

                                            {{-- External Link Input --}}
                                            <div x-show="uploadType === 'link'" x-cloak>
                                                <input type="url" 
                                                       name="link" 
                                                       x-ref="linkInput"
                                                       @input="checkInput()"
                                                       placeholder="Paste any external link here"
                                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                                       :value="'{{ $submission->link_url ?? '' }}'">
                                            </div>
                                        </div>

                                        {{-- Text/Comment Section --}}
                                        <div class="mb-6">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Private Comments</label>
                                            <textarea 
                                                name="submission_text" 
                                                rows="4"
                                                x-ref="textInput"
                                                @input="checkInput()"
                                                placeholder="Add notes or comments (optional)"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none">{{ $submission->submission_text ?? '' }}</textarea>
                                            <p class="text-xs text-gray-500 mt-1">Only visible to you and your teacher</p>
                                        </div>

                                        {{-- Submit Button --}}
                                        <button type="submit"
                                                :disabled="!{{ $canSubmit ? 'true' : 'false' }} || !hasInput"
                                                :class="({{ $canSubmit ? 'true' : 'false' }} && hasInput) ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-400 cursor-not-allowed'"
                                                class="w-full text-white font-bold py-3 rounded-lg transition-colors shadow-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $submission ? 'Update Submission' : 'Submit' }}
                                        </button>

                                        @if($isOverdue)
                                            <p class="text-xs text-red-600 text-center mt-2">
                                                Note: This assignment is overdue. Late submissions may not be accepted.
                                            </p>
                                        @endif
                                    </form>
                                @else
                                    <div class="text-center py-8 text-gray-500">
                                        <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                        <p class="font-medium">Submission Closed</p>
                                        <p class="text-sm mt-1">The deadline has passed</p>
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>
                @endif

            </div>

        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-app-layout>
