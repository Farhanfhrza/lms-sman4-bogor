<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Manajemen Survei Guru') }}
        </h2>
    </x-slot>

    <div x-data="{ showCreateModal: false }" class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-[#1a6341] relative">
        <div class="p-6 text-gray-900">
            
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 bg-gray-100 p-4 rounded-t-lg border-b-2 border-yellow-400">
                <h3 class="text-lg font-bold text-gray-800 mb-4 md:mb-0">
                    Daftar Periode Survei
                </h3>
                <div class="flex items-center space-x-2">
                    <button @click="showCreateModal = true" class="bg-[#1a6341] hover:bg-[#238054] text-white px-4 py-2 rounded-lg shadow-sm transition-colors flex items-center text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Buat Periode Baru
                    </button>
                </div>
            </div>

            @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                {{ session('error') }}
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($periods as $period)
                    <div class="bg-white border rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow relative">
                        <!-- Status Badge -->
                        <div class="absolute top-4 right-4">
                            @if($period->is_active)
                                <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded-full flex items-center">
                                    <span class="w-2 h-2 mr-1 bg-green-500 rounded-full animate-pulse"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="bg-gray-100 text-gray-800 text-xs font-semibold px-2.5 py-0.5 rounded-full flex items-center">
                                    <span class="w-2 h-2 mr-1 bg-gray-500 rounded-full"></span>
                                    Tidak Aktif
                                </span>
                            @endif
                        </div>

                        <div class="p-5 border-b border-gray-100">
                            <h4 class="text-xl font-bold text-[#1a6341] mb-1 pr-20 break-words">{{ $period->title }}</h4>
                            <p class="text-sm text-gray-500 mb-4">
                                {{ $period->academicYear->name ?? 'Tahun Ajaran Tidak Diketahui' }} - Semester {{ $period->semester }}
                            </p>
                            
                            <div class="space-y-2 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span>Mulai: <span class="font-medium text-gray-900">{{ $period->start_date->format('d M Y') }}</span></span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Tutup: <span class="font-medium text-gray-900">{{ $period->end_date->format('d M Y') }}</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3 flex justify-between items-center sm:px-6">
                            <a href="{{ route('admin.surveys.show', $period) }}" class="text-[#1a6341] hover:text-[#238054] font-medium text-sm flex items-center">
                                Kelola & Lihat Hasil
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg p-6 text-center">
                        <svg class="w-12 h-12 mx-auto text-yellow-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="font-medium text-lg mb-1">Belum ada periode survei</p>
                        <p class="text-sm text-yellow-700">Silakan buat periode survei baru untuk memulai mendapatkan evaluasi dari siswa.</p>
                    </div>
                @endforelse
            </div>
            
            <div class="mt-6">
                {{ $periods->links() }}
            </div>
        </div>

        <!-- Create Modal -->
        <div x-show="showCreateModal" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div @click.away="showCreateModal = false"
                 class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                
                <div class="bg-[#1a6341] text-white px-6 py-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold">Buat Periode Survei Baru</h3>
                    <button @click="showCreateModal = false" class="text-white/80 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.surveys.store') }}" class="p-6 space-y-4">
                    @csrf
                    
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Survei <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" required placeholder="Contoh: Evaluasi Kinerja Guru Sem 1"
                               class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm">
                        <p class="text-xs text-gray-500 mt-1">Nama deskriptif untuk survei ini.</p>
                    </div>

                    <div>
                        <label for="academic_year_id" class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajaran <span class="text-red-500">*</span></label>
                        <select name="academic_year_id" id="academic_year_id" required class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm">
                            @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}">{{ $ay->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                            <input type="date" name="start_date" id="start_date" required min="{{ date('Y-m-d') }}"
                                   class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Tutup <span class="text-red-500">*</span></label>
                            <input type="date" name="end_date" id="end_date" required min="{{ date('Y-m-d') }}"
                                   class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm">
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-4 border-t border-gray-200 mt-6">
                        <button type="button" @click="showCreateModal = false" class="text-gray-500 hover:text-gray-700 mr-4 text-sm font-medium">Batal</button>
                        <button type="submit" class="bg-[#1a6341] hover:bg-[#238054] text-white px-6 py-2.5 rounded-lg shadow-sm transition-colors text-sm font-medium">
                            Buat Survei
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
