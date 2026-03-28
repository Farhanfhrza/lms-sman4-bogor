<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Hasil Survei Mengajar Saya') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-breadcrumb :items="$breadcrumbs" />

            @if(empty($periods) || $periods->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-[#1a6341]">
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        <h3 class="text-xl font-bold text-gray-700 mb-2">Belum Ada Data Survei</h3>
                        <p class="text-gray-500 max-w-md mx-auto">Belum ada siswa yang menyelesaikan survei untuk Anda. Hasil evaluasi akan tampil di sini setelah ada yang mengisi.</p>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($periods as $period)
                        <a href="{{ route('teacher.surveys.show', $period) }}" class="block bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md hover:border-[#1a6341] transition-all p-6 group">
                            <div class="flex items-start justify-between mb-4">
                                <div class="w-12 h-12 rounded-full bg-[#1a6341]/10 flex items-center justify-center group-hover:bg-[#1a6341]/20 transition-colors">
                                    <svg class="w-6 h-6 text-[#1a6341]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-[#1a6341] group-hover:translate-x-1 transform transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-1 group-hover:text-[#1a6341] transition-colors">{{ $period->title }}</h3>
                            <p class="text-sm text-gray-500">{{ $period->academicYear->name ?? '-' }} &bull; Semester {{ $period->semester }}</p>
                            <div class="mt-4 pt-4 border-t border-gray-100 text-xs text-gray-500">
                                {{ $period->start_date->format('d M Y') }} — {{ $period->end_date->format('d M Y') }}
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
