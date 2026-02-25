<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Page Title -->
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Courses</h1>
            
            <!-- Controls Area: All Button, Search, Sort -->
            <div class="flex flex-col md:flex-row items-stretch md:items-center gap-3 mb-8">
                
                <!-- All Button -->
                <a href="{{ route('courses.index') }}" 
                   class="bg-[#5cb85c] hover:bg-[#4cae4c] text-white px-6 py-2.5 rounded-md shadow-sm transition-colors text-sm font-medium text-center whitespace-nowrap {{ !request('search') && !request('academic_year') && request('sort_by', 'name') === 'name' ? 'ring-2 ring-offset-2 ring-[#5cb85c]' : '' }}">
                    All
                </a>

                <!-- Search Form -->
                <form method="GET" action="{{ route('courses.index') }}" class="flex-1 md:max-w-xs" id="searchForm">
                    <input type="hidden" name="sort_by" value="{{ request('sort_by', 'name') }}">
                    <input type="hidden" name="academic_year" value="{{ request('academic_year') }}">
                    <div class="relative">
                        <input 
                            type="text" 
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#5cb85c] focus:border-transparent shadow-sm text-sm">
                        <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- Sort Dropdown -->
                <form method="GET" action="{{ route('courses.index') }}" class="md:w-auto">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="academic_year" value="{{ request('academic_year') }}">
                    <div class="relative">
                        <select 
                            name="sort_by"
                            onchange="this.form.submit()"
                            class="appearance-none bg-[#66bb6a] hover:bg-[#5cb85c] text-white pl-4 pr-10 py-2.5 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#5cb85c] text-sm font-medium w-full cursor-pointer transition-colors">
                            <option value="name" {{ request('sort_by', 'name') === 'name' ? 'selected' : '' }}>Sort by course name</option>
                            <option value="date" {{ request('sort_by') === 'date' ? 'selected' : '' }}>Sort by date</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-white">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Content Grid -->
            @if($courses->isEmpty())
                <!-- Empty State -->
                <div class="flex flex-col items-center justify-center py-16 text-gray-500">
                    <svg class="w-20 h-20 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <p class="text-lg font-medium">
                        @if(request('search'))
                            No results found for "{{ request('search') }}"
                        @else
                            No courses available yet
                        @endif
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($courses as $course)
                    <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden cursor-pointer group transform hover:-translate-y-1 transition-transform">
                        <a href="{{ route('courses.show', $course) }}" class="block flex flex-col">
                            <!-- Thumbnail with dynamic gradient background -->
                            @php
                                $colors = ['blue', 'green', 'purple', 'orange', 'pink', 'indigo'];
                                $colorIndex = $course->id % 6;
                            @endphp
                            <div class="h-40 w-full relative overflow-hidden bg-gradient-to-br from-{{ $colors[$colorIndex] }}-400 to-{{ $colors[$colorIndex] }}-600 flex items-center justify-center">
                                <!-- Subject Icon/Text -->
                                <div class="text-white text-center p-4">
                                    <svg class="w-16 h-16 mx-auto mb-2 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <!-- Overlay for hover effect -->
                                <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                            </div>

                            <!-- Card Info -->
                            <div class="p-5 flex-1 flex flex-col items-center text-center">
                                <h3 class="text-xl font-bold text-gray-800 mb-1 group-hover:text-[#1a6341] transition-colors">
                                    {{ $course->subject->name ?? 'Course' }}
                                </h3>
                                <p class="text-sm text-gray-500 font-medium">
                                    {{ $course->schoolClass->name ?? '-' }}
                                </p>
                                @if($course->teacher)
                                <p class="text-xs text-gray-400 mt-2">
                                    {{ $course->teacher->user->full_name ?? 'Teacher' }}
                                </p>
                                @endif
                            </div>
                        </a>

                        {{-- Kelola button for teacher/admin --}}
                        @if(Auth::user()->hasRole('admin') || (Auth::user()->hasRole('teacher') && Auth::user()->teacher && $course->teacher_id === Auth::user()->teacher->id))
                            <div class="px-5 pb-4 pt-0">
                                <a href="{{ route('manage.courses.show', $course) }}" 
                                   class="block w-full text-center py-2 text-sm font-medium text-[#1a6341] border border-[#1a6341] rounded-lg hover:bg-[#1a6341] hover:text-white transition-colors"
                                   onclick="event.stopPropagation()">
                                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Kelola
                                </a>
                            </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
