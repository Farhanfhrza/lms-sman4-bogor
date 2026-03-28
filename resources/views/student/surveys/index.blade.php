<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Survei Penilaian Guru') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <x-breadcrumb :items="$breadcrumbs" />

            @if(session('survey_gate_message'))
            <div class="my-4 p-4 bg-red-50 border-2 border-red-400 text-red-800 rounded-xl flex items-start shadow-md">
                <svg class="w-8 h-8 mr-3 mt-0.5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <div class="text-sm">
                    <strong class="text-base block mb-1">🔒 Akses Fitur Terkunci!</strong>
                    {!! session('survey_gate_message') !!}
                    <p class="mt-1">Selesaikan semua survei evaluasi guru di bawah ini untuk membuka kembali akses semua fitur.</p>
                </div>
            </div>
            @elseif(session('success'))
            <div class="my-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm flex items-start">
                <svg class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div><strong class="font-bold">Berhasil!</strong><p>{{ session('success') }}</p></div>
            </div>
            @elseif(session('error'))
            <div class="my-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm flex items-start">
                <svg class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div><strong class="font-bold">Perhatian!</strong><p>{{ session('error') }}</p></div>
            </div>
            @endif

            @if(empty($surveysData))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-[#1a6341]">
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        <h3 class="text-xl font-bold text-gray-700 mb-2">Tidak Ada Survei Aktif</h3>
                        <p class="text-gray-500 max-w-md mx-auto">Saat ini tidak ada periode survei yang sedang berlangsung. Silakan kerjakan tugas dari guru Anda di menu lainnya.</p>
                    </div>
                </div>
            @else
                @foreach($surveysData as $data)
                    @php 
                        $period = $data['period'];
                        $teachers = $data['teachers'];
                        $isCompletedAll = $data['completed_count'] == $data['total_teachers'] && $data['total_teachers'] > 0;
                    @endphp
                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 {{ $isCompletedAll ? 'border-green-500' : 'border-[#1a6341]' }} mb-8 relative">
                        @if($isCompletedAll)
                            <div class="absolute top-0 right-0">
                                <span class="bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-bl-lg shadow-sm">
                                    SELESAI SEMUA
                                </span>
                            </div>
                        @endif

                        <div class="p-6 md:p-8">
                            <div class="mb-6 pb-6 border-b border-gray-100 flex flex-col md:flex-row md:justify-between md:items-end gap-4">
                                <div>
                                    <h3 class="text-xl font-extrabold text-[#1a6341] block">{{ $period->title }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Tahun Ajaran {{ $period->academicYear->name ?? '-' }} (Semester {{ $period->semester }})
                                    </p>
                                </div>
                                <div class="bg-gray-50 px-4 py-2 rounded-lg border border-gray-200 inline-block">
                                    <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1 w-full text-center">Batas Pengisian</p>
                                    <p class="font-bold text-red-600 text-center">{{ $period->end_date->format('d M Y') }}</p>
                                </div>
                            </div>
                            
                            <div class="mb-6">
                                <div class="flex justify-between items-center mb-2">
                                    <p class="text-sm font-semibold text-gray-700">Progres Evaluasi Anda</p>
                                    <p class="text-sm font-bold text-[#1a6341]">{{ $data['completed_count'] }} / {{ $data['total_teachers'] }} Guru</p>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    @php $progressPercent = $data['total_teachers'] > 0 ? round(($data['completed_count'] / $data['total_teachers']) * 100) : 0; @endphp
                                    <div class="bg-[#1a6341] h-2.5 rounded-full" style="width: {{ $progressPercent }}%"></div>
                                </div>
                            </div>

                            @if($data['total_teachers'] === 0)
                                <div class="text-center py-6 text-gray-500 text-sm">
                                    Anda tidak memiliki guru yang harus dinilai pada periode ini.
                                </div>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($teachers as $item)
                                        @php 
                                            $teacher = $item['teacher'];
                                            $hasResponded = $item['has_responded'];
                                            $subjectList = $item['subjects']->implode(', ');
                                        @endphp
                                        
                                        <div class="border rounded-xl p-4 flex flex-col items-center text-center transition-all {{ $hasResponded ? 'bg-gray-50 border-gray-200 opacity-80' : 'bg-white border-[#1a6341]/30 hover:shadow-md hover:border-[#1a6341] relative' }}">
                                            @if(!$hasResponded)
                                                <div class="absolute -top-2 -right-2">
                                                    <span class="flex h-4 w-4 relative">
                                                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                                      <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500"></span>
                                                    </span>
                                                </div>
                                            @endif
                                            
                                            <div class="w-16 h-16 rounded-full overflow-hidden border-2 mb-3 {{ $hasResponded ? 'border-gray-300' : 'border-[#1a6341]' }}">
                                                <img src="{{ $teacher->user->profile_photo_url }}" alt="Photo" class="w-full h-full object-cover">
                                            </div>
                                            
                                            <h4 class="font-bold text-sm text-gray-900 line-clamp-1 w-full" title="{{ $teacher->user->full_name ?? $teacher->user->name }}">
                                                {{ $teacher->user->full_name ?? $teacher->user->name }}
                                            </h4>
                                            
                                            <p class="text-xs text-gray-500 mt-1 mb-4 h-8 overflow-hidden line-clamp-2" title="{{ $subjectList }}">
                                                {{ $subjectList }}
                                            </p>
                                            
                                            <div class="mt-auto w-full">
                                                @if($hasResponded)
                                                    <div class="w-full py-2 bg-green-100 text-green-700 text-xs font-bold rounded-lg flex items-center justify-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                        Selesai Dinilai
                                                    </div>
                                                @else
                                                    <a href="{{ route('student.surveys.fill', [$period->id, $teacher->id]) }}" class="block w-full py-2 bg-[#1a6341] hover:bg-[#238054] text-white text-xs font-bold rounded-lg transition-colors">
                                                        Mulai Evaluasi
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</x-app-layout>
