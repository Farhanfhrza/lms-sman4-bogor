<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Manajemen Absensi') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        
        <div class="flex flex-col mb-4 mt-2">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Pilih Kelas</h1>
            @if($activeYear)
            <p class="text-gray-600 mt-2">Tahun Ajaran Aktif: <span class="font-semibold">{{ $activeYear->name }}</span></p>
            @endif
        </div>

        <!-- Class Cards List -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($classes as $class)
                <a href="{{ route('manage.attendances.class-subjects', $class) }}" class="block group hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="bg-white border-l-4 border-[#59b88b] rounded-xl p-6 shadow-sm flex flex-col items-center text-center">
                        <div class="bg-gray-100 p-4 rounded-full mb-4 group-hover:bg-[#e0f2eb] transition-colors">
                            <svg class="w-10 h-10 text-[#1a6341]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800">{{ $class->name }}</h3>
                        <p class="text-gray-500 text-sm mt-1">{{ mb_strtoupper($class->major) ?? '-' }} &bull; Grade {{ $class->grade }}</p>
                    </div>
                </a>
            @empty
                <div class="col-span-full bg-white p-8 rounded-xl shadow-sm border border-gray-200 text-center">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <h3 class="text-lg font-medium text-gray-900">Belum Ada Kelas</h3>
                    <p class="mt-1 text-gray-500">Tidak ada data kelas pada tahun ajaran aktif saat ini.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
