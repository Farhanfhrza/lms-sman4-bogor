<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Kelola Survei:') }} {{ $survey->title }}
        </h2>
    </x-slot>

    <div class="pb-6" x-data="{ activeTab: 'questions', showAddQuestionModal: false, showEditModal: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
            
            <x-breadcrumb :items="$breadcrumbs" />

            @if(session('success'))
            <div class="my-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="my-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                {{ session('error') }}
            </div>
            @endif

            <!-- STATUS BANNER + TOGGLE BUTTON -->
            <div class="mb-6 p-4 rounded-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4
                        {{ $survey->is_active ? 'bg-green-50 border-2 border-green-400' : 'bg-yellow-50 border-2 border-yellow-300' }}">
                <div class="flex items-center gap-3">
                    @if($survey->is_active)
                        <span class="w-10 h-10 flex items-center justify-center rounded-full bg-green-500 text-white flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <div>
                            <p class="font-bold text-green-800">Survei Sedang Aktif</p>
                            <p class="text-sm text-green-700">Siswa dapat mengisi survei ini sekarang. Klik "Tutup Survei" untuk menonaktifkan.</p>
                        </div>
                    @else
                        <span class="w-10 h-10 flex items-center justify-center rounded-full bg-yellow-400 text-white flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <div>
                            <p class="font-bold text-yellow-800">Survei Belum Aktif</p>
                            <p class="text-sm text-yellow-700">Tambahkan pertanyaan, lalu klik "Aktifkan Survei" agar siswa dapat mulai mengisi.</p>
                        </div>
                    @endif
                </div>

                <form method="POST" action="{{ route('admin.surveys.toggle-status', $survey) }}" class="flex-shrink-0">
                    @csrf
                    @method('PATCH')
                    @if($survey->is_active)
                        <button type="submit"
                                onclick="return confirm('Nonaktifkan survei ini? Siswa tidak akan bisa mengisi lagi.')"
                                class="bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-2.5 rounded-lg shadow font-bold text-sm flex items-center transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Tutup Survei
                        </button>
                    @else
                        <button type="submit"
                                onclick="return confirm('Aktifkan survei ini? Semua siswa pada tahun ajaran aktif akan diwajibkan mengisi.')"
                                class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-lg shadow font-bold text-sm flex items-center transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            ✅ Aktifkan Survei
                        </button>
                    @endif
                </form>
            </div>

            <!-- Tabs -->
            <div class="border-b border-gray-200 mb-6 bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <nav class="-mb-px flex" aria-label="Tabs">
                    <button @click="activeTab = 'questions'" :class="{'border-[#1a6341] text-[#1a6341]': activeTab === 'questions', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'questions'}" class="w-1/3 md:w-auto py-4 px-8 text-center border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                        Daftar Pertanyaan
                    </button>
                    <button @click="activeTab = 'recap'" :class="{'border-[#1a6341] text-[#1a6341]': activeTab === 'recap', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'recap'}" class="w-1/3 md:w-auto py-4 px-8 text-center border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                        📊 Rekap Hasil
                        @if($responseCount > 0)<span class="ml-1 bg-green-100 text-green-700 text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $responseCount }}</span>@endif
                    </button>
                    <button @click="activeTab = 'settings'" :class="{'border-[#1a6341] text-[#1a6341]': activeTab === 'settings', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'settings'}" class="w-1/2 md:w-auto py-4 px-8 text-center border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                        Pengaturan
                    </button>
                </nav>
            </div>

            <!-- Tab: Recap -->
            <div x-show="activeTab === 'recap'" x-cloak class="bg-white overflow-hidden shadow-sm sm:rounded-xl">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Rekap Hasil Evaluasi Per Guru</h3>
                        <div class="bg-[#1a6341]/10 text-[#1a6341] px-4 py-2 rounded-lg text-sm font-bold">
                            {{ $responseCount }} Siswa Mengisi
                        </div>
                    </div>

                    @if($teacherRecap->isEmpty())
                        <div class="text-center py-12 text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            Belum ada data. Aktifkan survei agar siswa dapat mengisi.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-50 border-b-2 border-gray-200">
                                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Guru</th>
                                        <th class="text-center px-4 py-3 font-semibold text-gray-700">Responden</th>
                                        @foreach($questions as $q)
                                            <th class="text-center px-3 py-3 font-semibold text-gray-700 text-xs max-w-24" title="{{ $q->question_text }}">
                                                P{{ $loop->iteration }}
                                            </th>
                                        @endforeach
                                        <th class="text-center px-4 py-3 font-semibold text-[#1a6341]">Rata-rata</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($teacherRecap as $item)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-3">
                                                    <img src="{{ $item['teacher']->user->profile_photo_url ?? '' }}" class="w-8 h-8 rounded-full object-cover border border-gray-200" alt="">
                                                    <span class="font-medium text-gray-900">{{ $item['teacher']->user->full_name ?? $item['teacher']->user->name }}</span>
                                                </div>
                                            </td>
                                            <td class="text-center px-4 py-3 text-gray-600">{{ $item['responder_count'] }} siswa</td>
                                            @foreach($questions as $q)
                                                @php $score = $item['avg_per_q'][$q->id] ?? 0; @endphp
                                                <td class="text-center px-3 py-3">
                                                    <span class="inline-block w-10 py-1 rounded text-sm font-bold
                                                        {{ $score >= 4 ? 'bg-green-100 text-green-700' : ($score >= 3 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                                        {{ $score }}
                                                    </span>
                                                </td>
                                            @endforeach
                                            <td class="text-center px-4 py-3">
                                                <span class="inline-block px-3 py-1 rounded-lg text-sm font-extrabold
                                                    {{ $item['overall_avg'] >= 4 ? 'bg-green-500 text-white' : ($item['overall_avg'] >= 3 ? 'bg-yellow-400 text-white' : 'bg-red-500 text-white') }}">
                                                    {{ $item['overall_avg'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($questions->isNotEmpty())
                        <div class="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-200 text-xs text-gray-500">
                            <strong>Keterangan kolom:</strong>
                            @foreach($questions as $q)
                                P{{ $loop->iteration }}: {{ Str::limit($q->question_text, 60) }}{{ strlen($q->question_text) > 60 ? '...' : '' }}
                                @if(!$loop->last) &bull; @endif
                            @endforeach
                        </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Tab: Questions -->
            <div x-show="activeTab === 'questions'" x-cloak class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-[#1a6341]">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Pertanyaan Evaluasi ({{ $questions->count() }})</h3>
                        <button @click="showAddQuestionModal = true" class="bg-[#1a6341] hover:bg-[#238054] text-white px-4 py-2 rounded-lg shadow-sm transition-colors flex items-center text-sm font-medium">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Pertanyaan
                        </button>
                    </div>

                    @if($questions->count() == 0)
                        <div class="bg-gray-50 border border-dashed border-gray-300 rounded-lg p-10 text-center text-gray-500">
                            Anda belum menambahkan pertanyaan apa pun untuk survei ini.<br>
                            Siswa akan memberikan rating 1-5 berdasarkan pertanyaan yang Anda buat.
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($questions as $q)
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 flex justify-between items-start gap-4 hover:shadow-sm transition-shadow">
                                    <div class="flex gap-4">
                                        <div class="bg-white border rounded shadow-sm w-10 h-10 flex items-center justify-center font-bold text-[#1a6341]">
                                            {{ $q->order_number }}
                                        </div>
                                        <div>
                                            <p class="text-gray-900 font-medium mb-1">{{ $q->question_text }}</p>
                                            <div class="text-xs text-gray-500 italic">Jawaban berupa rating 1 (Sangat Kurang) hingga 5 (Sangat Baik).</div>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <button @click="$dispatch('edit-question', { id: '{{ $q->id }}', text: '{{ addslashes($q->question_text) }}', order: '{{ $q->order_number }}' })" class="text-blue-600 hover:text-blue-800 p-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <form method="POST" action="{{ route('admin.surveys.questions.destroy', $q) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 p-2" onclick="return confirm('Hapus pertanyaan ini?')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tab: Settings -->
            <div x-show="activeTab === 'settings'" x-cloak class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.surveys.update', $survey) }}" class="space-y-6 max-w-2xl">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Survei <span class="text-red-500">*</span></label>
                            <input type="text" name="title" id="title" value="{{ $survey->title }}" required
                                   class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm">
                        </div>

                        <div>
                                <label for="academic_year_id" class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajaran <span class="text-red-500">*</span></label>
                                <select name="academic_year_id" id="academic_year_id" required class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm">
                                    @foreach($academicYears as $ay)
                                        <option value="{{ $ay->id }}" {{ $survey->academic_year_id == $ay->id ? 'selected' : '' }}>{{ $ay->name }}</option>
                                    @endforeach
                                </select>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                                <input type="date" name="start_date" id="start_date" value="{{ $survey->start_date->format('Y-m-d') }}" required
                                       class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm">
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Tutup <span class="text-red-500">*</span></label>
                                <input type="date" name="end_date" id="end_date" value="{{ $survey->end_date->format('Y-m-d') }}" required
                                       class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm">
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <h4 class="font-medium text-gray-900 mb-2">Info Survei</h4>
                            <p class="text-sm text-gray-600 mb-1">Total Responden Sementara: <span class="font-bold">{{ $responseCount }} siswa</span></p>
                            <p class="text-sm text-gray-600">Status saat ini: 
                                @if($survey->is_active)
                                    <strong class="text-green-600">Aktif</strong>
                                @else
                                    <strong class="text-gray-500">Tidak Aktif</strong>
                                @endif
                            </p>
                        </div>

                        <div class="flex items-center justify-end pt-4 border-t border-gray-200">
                            <button type="submit" class="bg-[#1a6341] hover:bg-[#238054] text-white px-6 py-2.5 rounded-lg shadow-sm transition-colors text-sm font-medium">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <!-- Add Question Modal -->
        <div x-show="showAddQuestionModal" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div @click.away="showAddQuestionModal = false" class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
                <div class="bg-[#1a6341] text-white px-6 py-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold">Tambah Pertanyaan Baru</h3>
                    <button @click="showAddQuestionModal = false" class="text-white/80 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('admin.surveys.questions.store', $survey) }}" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pertanyaan <span class="text-red-500">*</span></label>
                        <textarea name="question_text" rows="3" required class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm" placeholder="Contoh: Apakah gaya mengajar guru ini mudah dipahami?"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Urut <span class="text-red-500">*</span></label>
                        <input type="number" name="order_number" value="{{ ($questions->max('order_number') ?? 0) + 1 }}" required class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm">
                    </div>
                    <div class="flex justify-end pt-4">
                        <button type="button" @click="showAddQuestionModal = false" class="text-gray-500 hover:text-gray-700 mr-4 text-sm font-medium">Batal</button>
                        <button type="submit" class="bg-[#1a6341] hover:bg-[#238054] text-white px-6 py-2.5 rounded-lg text-sm font-medium">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Question Modal via Alpine Event -->
        <div x-data="{ qId: '', qText: '', qOrder: '', open: false }" 
             @edit-question.window="qId = $event.detail.id; qText = $event.detail.text; qOrder = $event.detail.order; open = true">
            <div x-show="open" x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                <div @click.away="open = false" class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
                    <div class="bg-blue-600 text-white px-6 py-4 flex items-center justify-between">
                        <h3 class="text-lg font-bold">Edit Pertanyaan</h3>
                        <button @click="open = false" class="text-white/80 hover:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <form :action="'{{ route('admin.surveys.questions.update', '') }}/' + qId" method="POST" class="p-6 space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pertanyaan <span class="text-red-500">*</span></label>
                            <textarea name="question_text" x-model="qText" rows="3" required class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Urut <span class="text-red-500">*</span></label>
                            <input type="number" name="order_number" x-model="qOrder" required class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm">
                        </div>
                        <div class="flex justify-end pt-4">
                            <button type="button" @click="open = false" class="text-gray-500 hover:text-gray-700 mr-4 text-sm font-medium">Batal</button>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
