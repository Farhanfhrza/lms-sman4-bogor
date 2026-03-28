<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            Evaluasi: {{ $teacher->user->full_name ?? $teacher->user->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <x-breadcrumb :items="$breadcrumbs" />

            <!-- Teacher Info -->
            <div class="bg-white rounded-xl shadow-sm border-l-4 border-[#1a6341] overflow-hidden mb-6 flex items-center gap-5 p-5">
                <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-gray-100 shadow-sm flex-shrink-0">
                    <img src="{{ $teacher->user->profile_photo_url }}" alt="Photo" class="w-full h-full object-cover">
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ $teacher->user->full_name ?? $teacher->user->name }}</h3>
                    <p class="text-sm text-gray-500">NIP: {{ $teacher->nip ?? '-' }} &bull; Periode: <strong>{{ $survey->title }}</strong></p>
                    <p class="text-xs text-amber-600 font-medium mt-1">⚠️ Jawaban bersifat anonim dan tidak dapat diubah setelah dikirim.</p>
                </div>
            </div>

            <!-- Error messages -->
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    <strong class="font-bold block mb-1">Pastikan semua pertanyaan sudah dijawab:</strong>
                    <ul class="list-disc pl-5 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Survey Form -->
            <form method="POST" action="{{ route('student.surveys.store', [$survey->id, $teacher->id]) }}">
                @csrf
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl">

                    <!-- Scale legend header -->
                    <div class="bg-gray-50 border-b border-gray-200 px-6 py-3">
                        <p class="text-xs text-gray-500 font-medium">Keterangan skala penilaian:</p>
                        <div class="flex flex-wrap gap-x-5 gap-y-1 mt-1 text-xs text-gray-600">
                            <span><strong class="text-gray-800">1</strong> = Sangat Kurang</span>
                            <span><strong class="text-gray-800">2</strong> = Kurang</span>
                            <span><strong class="text-gray-800">3</strong> = Cukup</span>
                            <span><strong class="text-gray-800">4</strong> = Baik</span>
                            <span><strong class="text-gray-800">5</strong> = Sangat Baik</span>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-[#1a6341] text-white">
                                    <th class="text-left px-6 py-3 font-semibold w-8">#</th>
                                    <th class="text-left px-4 py-3 font-semibold">Pertanyaan</th>
                                    @for($i = 1; $i <= 5; $i++)
                                        <th class="text-center px-4 py-3 font-semibold w-16">{{ $i }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($questions as $index => $q)
                                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-green-50 transition-colors @error('answers.'.$q->id) ring-2 ring-inset ring-red-300 @enderror">
                                        <td class="px-6 py-4 text-gray-400 font-medium align-top">{{ $index + 1 }}</td>
                                        <td class="px-4 py-4 text-gray-800 align-middle leading-relaxed">
                                            {{ $q->question_text }}
                                            @error('answers.'.$q->id)
                                                <span class="block text-red-500 text-xs mt-1">{{ $message }}</span>
                                            @enderror
                                        </td>
                                        @for($i = 1; $i <= 5; $i++)
                                            <td class="text-center px-4 py-4 align-middle">
                                                <label class="flex items-center justify-center cursor-pointer group">
                                                    <input type="radio"
                                                           name="answers[{{ $q->id }}]"
                                                           value="{{ $i }}"
                                                           required
                                                           {{ old('answers.'.$q->id) == $i ? 'checked' : '' }}
                                                           class="w-5 h-5 text-[#1a6341] border-gray-300 focus:ring-[#1a6341] cursor-pointer accent-[#1a6341]">
                                                </label>
                                            </td>
                                        @endfor
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Comment -->
                    <div class="p-6 border-t border-gray-200">
                        <label for="comment" class="block text-sm font-semibold text-gray-700 mb-1">
                            Kesan, Pesan, atau Saran <span class="text-gray-400 font-normal">(Opsional)</span>
                        </label>
                        <textarea name="comment" id="comment" rows="3"
                                  class="w-full border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-lg shadow-sm text-sm"
                                  placeholder="Masukan Anda akan sangat bermanfaat bagi perkembangan pengajaran...">{{ old('comment') }}</textarea>
                    </div>

                    <!-- Submit -->
                    <div class="px-6 pb-6 flex items-center justify-end gap-4">
                        <a href="{{ route('student.surveys.index') }}" class="text-gray-500 hover:text-gray-700 text-sm font-medium transition-colors">
                            Batal
                        </a>
                        <button type="submit"
                                onclick="return confirm('Kirim evaluasi ini? Jawaban tidak dapat diubah setelah dikirim.')"
                                class="bg-[#1a6341] hover:bg-[#238054] text-white px-8 py-2.5 rounded-lg font-bold text-sm shadow hover:shadow-md transition-all">
                            Kirim Evaluasi
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </div>
</x-app-layout>
