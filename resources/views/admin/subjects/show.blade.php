<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center">
            <a href="{{ route('admin.subjects.index') }}" class="text-[#1a6341] hover:text-[#238054] mr-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            Detail Mata Pelajaran: {{ $subject->name }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        
        @if(session('success'))
            <div class="p-3 bg-green-50 border border-green-300 text-green-800 rounded-lg text-sm flex items-center shadow-sm">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="p-3 bg-red-50 border border-red-300 text-red-800 rounded-lg text-sm shadow-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                </ul>
            </div>
        @endif

        <!-- Overview Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col md:flex-row md:items-center justify-between">
            <div>
                <h3 class="text-2xl font-extrabold text-gray-800">{{ $subject->name }}</h3>
                <p class="text-gray-500 font-medium">Kode: <span class="text-gray-900 bg-gray-100 px-2 py-0.5 rounded ml-1">{{ $subject->code }}</span></p>
            </div>
            <div class="mt-4 md:mt-0 flex gap-4">
                <div class="text-center bg-blue-50 px-6 py-3 rounded-lg border border-blue-100">
                    <div class="text-3xl font-extrabold text-blue-700">{{ $subject->teachers->count() }}</div>
                    <div class="text-xs font-bold font-medium text-blue-500 mt-1 uppercase">Guru Mampu</div>
                </div>
                <div class="text-center bg-emerald-50 px-6 py-3 rounded-lg border border-emerald-100">
                    <div class="text-3xl font-extrabold text-emerald-700">{{ $classesUsing->total() }}</div>
                    <div class="text-xs font-bold font-medium text-emerald-500 mt-1 uppercase">Kelas Aktif</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left Column: Manage Teachers -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 bg-gray-50">
                        <h3 class="font-bold text-lg text-gray-800">Assign Guru Pengajar</h3>
                        <p class="text-xs text-gray-500 mt-1">Siapa saja guru yang memiliki kompetensi mengajar mapel ini?</p>
                    </div>
                    
                    <div class="p-5">
                        <!-- Add Teacher Form -->
                        <form action="{{ route('admin.subjects.assign-teacher', $subject->id) }}" method="POST" class="mb-6 pb-6 border-b border-gray-100">
                            @csrf
                            <label class="block text-sm font-bold text-gray-700 mb-2">Tambah Guru Kompeten</label>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <select name="teacher_id" class="flex-1 border-gray-300 focus:border-[#1a6341] focus:ring focus:ring-[#1a6341] focus:ring-opacity-50 rounded-md text-sm" required>
                                    <option value="">-- Pilih Guru --</option>
                                    @foreach($availableTeachers as $teacher)
                                        <option value="{{ $teacher->id }}">{{ $teacher->user->full_name ?? 'N/A' }} ({{ $teacher->nip ?? '-' }})</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="bg-[#1a6341] text-white px-4 py-2 flex-shrink-0 text-sm font-bold rounded-md hover:bg-[#238054] transition-colors">
                                    + Assign
                                </button>
                            </div>
                            @if($availableTeachers->isEmpty())
                                <p class="text-xs text-amber-500 mt-2 font-medium">Semua guru di sekolah sudah terhubung ke mapel ini.</p>
                            @endif
                        </form>

                        <!-- List of Connected Teachers -->
                        <h4 class="text-sm font-bold text-gray-700 mb-3">Daftar Guru Terkoneksi ({{ $subject->teachers->count() }})</h4>
                        @if($subject->teachers->isEmpty())
                            <div class="text-center py-6 text-gray-500 text-sm bg-gray-50 rounded border border-dashed border-gray-200">
                                Belum ada guru yang diasosiasikan dengan mapel ini.
                            </div>
                        @else
                            <ul class="divide-y divide-gray-100">
                                @foreach($subject->teachers as $teacher)
                                <li class="py-3 flex justify-between items-center group">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs mr-3">
                                            {{ substr($teacher->user->full_name ?? '?', 0, 1) }}
                                        </div>
                                        <div>
                                            <h5 class="font-bold text-sm text-gray-800">{{ $teacher->user->full_name ?? 'N/A' }}</h5>
                                            <p class="text-xs text-gray-500">{{ $teacher->nip ?? 'NIP: -' }}</p>
                                        </div>
                                    </div>
                                    <form action="{{ route('admin.subjects.remove-teacher', [$subject->id, $teacher->id]) }}" method="POST" onsubmit="return confirm('Lepaskan guru ini dari daftar mampel {{ $subject->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 p-1 bg-red-50 rounded opacity-0 group-hover:opacity-100 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </form>
                                </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: Classes using this subject -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                        <div>
                            <h3 class="font-bold text-lg text-gray-800">Terjadwal di Kelas</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Semua *Course* aktif yang mengadopsi mata pelajaran ini</p>
                        </div>
                        <a href="{{ route('admin.schedules.index') }}" class="text-xs bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 hover:text-blue-600 font-bold py-1.5 px-3 rounded shadow-sm transition-colors">
                            Atur di Matrix
                        </a>
                    </div>
                    
                    <div class="p-0">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-white">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kelas / Rombel</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tahun Ajaran</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Guru Pengampu</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($classesUsing as $cu)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-indigo-700">{{ $cu->schoolClass->name ?? 'Unknown' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 border border-gray-200 bg-gray-50 px-2 pl-0 py-0.5 rounded inline-block">
                                            @if($cu->academicYear && $cu->academicYear->is_active)
                                                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block mr-1 ml-2"></span>
                                            @endif
                                            {{ $cu->academicYear->name ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($cu->teacher)
                                            <div class="text-sm font-bold text-gray-700">{{ $cu->teacher->user->full_name ?? 'Guru Terhapus' }}</div>
                                        @else
                                            <span class="text-xs text-red-500 font-bold bg-red-50 border border-red-100 px-2 py-1 rounded">Diperlukan Perhatian: Belum Di-Set</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-10 text-center text-gray-500 font-medium">Mapel ini belum dijadwalkan / ditugaskan ke kelas manapun.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="px-6 py-3 border-t border-gray-100">
                            {{ $classesUsing->links() }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
