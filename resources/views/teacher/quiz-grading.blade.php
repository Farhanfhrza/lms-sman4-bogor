<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ $course->subject->name ?? 'Mata Pelajaran' }}
        </h2>
    </x-slot>

    {{-- Breadcrumbs --}}
    @if(!empty($breadcrumbs))
        <nav class="mb-6 text-sm text-gray-500">
            @foreach($breadcrumbs as $i => $crumb)
                @if(!$loop->last)
                    <a href="{{ $crumb['url'] ?? '#' }}" class="hover:text-[#1a6341] transition">{{ $crumb['label'] }}</a>
                    <span class="mx-1.5">&rsaquo;</span>
                @else
                    <span class="text-gray-800 font-medium">{{ $crumb['label'] }}</span>
                @endif
            @endforeach
        </nav>
    @endif

    {{-- Class Label --}}
    <p class="text-gray-500 font-medium text-sm mb-6">{{ $course->schoolClass->name ?? 'Kelas' }}</p>

    {{-- Quiz Detail Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8 mb-8">
        <div class="flex items-start gap-4">
            {{-- Icon --}}
            <div class="shrink-0 w-12 h-12 rounded-full bg-[#1a6341] flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-xl font-bold text-[#1a6341] mb-3">{{ $quiz->title }}</h3>
                @if($quiz->description)
                    <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed mb-3">
                        {!! $quiz->description !!}
                    </div>
                @endif
                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ $totalQuestions }} Soal
                    </span>
                    @if($quiz->time_limit)
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ $quiz->time_limit }} menit
                        </span>
                    @endif
                    @if($quiz->end_at)
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Batas: {{ $quiz->end_at->format('d M Y, H:i') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Rekap Nilai Siswa --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200" x-data="quizGradingTable()" x-cloak>

        {{-- Card Header --}}
        <div class="px-6 py-4 border-b border-gray-200">
            <h4 class="text-lg font-bold text-gray-800">Rekap Nilai Kuis</h4>
        </div>

        {{-- Controls Row --}}
        <div class="px-6 py-3 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            {{-- Page Size --}}
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <span>Show</span>
                <select x-model.number="pageSize" @change="currentPage = 1"
                        class="border border-gray-300 rounded-md px-2 py-1 text-sm focus:ring-[#1a6341] focus:border-[#1a6341]">
                    <option value="10">10</option>
                    <option value="20" selected>20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span>entries</span>
            </div>

            {{-- Search --}}
            <div class="flex items-center gap-2 text-sm">
                <span class="text-gray-600">Search:</span>
                <input type="text" x-model="searchQuery" @input="currentPage = 1"
                       class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-[#1a6341] focus:border-[#1a6341] w-48">
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-y border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs w-14">No</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Nama Siswa</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">NISN</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 uppercase tracking-wider text-xs">Keterangan</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 uppercase tracking-wider text-xs">Nilai</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 uppercase tracking-wider text-xs w-20">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="(row, idx) in paginatedRows" :key="idx">
                        <tr class="hover:bg-gray-50 transition-colors">
                            {{-- NO --}}
                            <td class="px-4 py-3 text-gray-700 font-medium" x-text="row.no"></td>

                            {{-- NAMA SISWA --}}
                            <td class="px-4 py-3 text-gray-800 font-medium" x-text="row.name"></td>

                            {{-- NISN --}}
                            <td class="px-4 py-3 text-gray-600" x-text="row.nisn"></td>

                            {{-- KETERANGAN --}}
                            <td class="px-4 py-3 text-center">
                                <span :class="{
                                          'text-emerald-600 font-semibold': row.keteranganColor === 'green',
                                          'text-red-600 font-semibold': row.keteranganColor === 'red',
                                      }"
                                      x-text="row.keterangan"></span>
                            </td>

                            {{-- NILAI --}}
                            <td class="px-4 py-3 text-center">
                                <span :class="{
                                          'text-red-600 font-semibold italic': row.nilaiColor === 'red',
                                          'text-gray-800 font-semibold': row.nilaiColor === 'default',
                                      }"
                                      x-text="row.nilaiLabel"></span>
                            </td>

                            {{-- DETAIL --}}
                            <td class="px-4 py-3 text-center">
                                <template x-if="row.hasAttempt">
                                    <a :href="'/quiz-attempts/' + row.attemptUuid"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-[#1a6341] text-white hover:bg-[#155c38] transition shadow-sm"
                                       title="Lihat Jawaban">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                </template>
                                <template x-if="!row.hasAttempt">
                                    <span class="text-gray-300">—</span>
                                </template>
                            </td>
                        </tr>
                    </template>
                    <template x-if="paginatedRows.length === 0">
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">Tidak ada data siswa.</td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Footer: Showing X to Y of Z entries + Pagination --}}
        <div class="px-6 py-3 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm text-gray-600">
            <div>
                Showing <span x-text="showingFrom"></span> to <span x-text="showingTo"></span> of <span x-text="filteredRows.length"></span> entries
            </div>
            <div class="flex items-center gap-1">
                <button @click="prevPage()" :disabled="currentPage === 1"
                        :class="currentPage === 1 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-100'"
                        class="px-3 py-1.5 rounded-md border border-gray-300 text-gray-600 text-sm transition">Previous</button>
                <template x-for="p in totalPages" :key="p">
                    <button @click="currentPage = p"
                            :class="currentPage === p ? 'bg-[#1a6341] text-white border-[#1a6341]' : 'hover:bg-gray-100 border-gray-300 text-gray-700'"
                            class="px-3 py-1.5 rounded-md border text-sm transition"
                            x-text="p"></button>
                </template>
                <button @click="nextPage()" :disabled="currentPage >= totalPages"
                        :class="currentPage >= totalPages ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-100'"
                        class="px-3 py-1.5 rounded-md border border-gray-300 text-gray-600 text-sm transition">Next</button>
            </div>
        </div>
    </div>

    <script>
    function quizGradingTable() {
        const allRows = @json($rows->values());

        return {
            allRows: allRows,
            searchQuery: '',
            pageSize: 20,
            currentPage: 1,

            get filteredRows() {
                if (!this.searchQuery.trim()) return this.allRows;
                const q = this.searchQuery.toLowerCase();
                return this.allRows.filter(row =>
                    row.name.toLowerCase().includes(q) ||
                    row.nisn.toLowerCase().includes(q) ||
                    row.keterangan.toLowerCase().includes(q)
                );
            },

            get totalPages() {
                return Math.max(1, Math.ceil(this.filteredRows.length / this.pageSize));
            },

            get paginatedRows() {
                const start = (this.currentPage - 1) * this.pageSize;
                return this.filteredRows.slice(start, start + this.pageSize);
            },

            get showingFrom() {
                if (this.filteredRows.length === 0) return 0;
                return (this.currentPage - 1) * this.pageSize + 1;
            },

            get showingTo() {
                return Math.min(this.currentPage * this.pageSize, this.filteredRows.length);
            },

            prevPage() {
                if (this.currentPage > 1) this.currentPage--;
            },

            nextPage() {
                if (this.currentPage < this.totalPages) this.currentPage++;
            },
        };
    }
    </script>
</x-app-layout>
