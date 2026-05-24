<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Preview Data Import Siswa') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-[#1a6341]">
            <div class="p-6 bg-white border-b border-gray-200">

                <div class="mb-6">
                    <h3 class="text-lg font-bold text-gray-800">Validasi Data Excel</h3>
                    <p class="text-sm text-gray-600 mt-1">Sistem menetapkan mana baris yang siap dimasukkan dan mana yang ditolak. Anda tetap dapat melanjutkan dengan mengabaikan baris yang merah.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-green-50 border border-green-200 p-4 rounded-lg flex items-center">
                        <div class="p-3 bg-green-500 rounded-full text-white mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm text-green-600 font-bold uppercase">Data Valid</p>
                            <h4 class="text-2xl font-extrabold text-green-700">{{ $validCount }} Baris</h4>
                        </div>
                    </div>
                    
                    <div class="bg-red-50 border border-red-200 p-4 rounded-lg flex items-center">
                        <div class="p-3 bg-red-500 rounded-full text-white mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm text-red-600 font-bold uppercase">Data Bermasalah</p>
                            <h4 class="text-2xl font-extrabold text-red-700">{{ $invalidCount }} Baris</h4>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto border rounded-lg mb-6">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-100 font-bold text-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left">Baris Tabel</th>
                                <th class="px-4 py-3 text-left">Nama</th>
                                <th class="px-4 py-3 text-left">NISN</th>
                                <th class="px-4 py-3 text-left">Gender</th>
                                <th class="px-4 py-3 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($previewData as $row)
                                <tr class="{{ $row['status'] == 'invalid' ? 'bg-red-50' : 'hover:bg-gray-50' }}">
                                    <td class="px-4 py-3 font-mono text-gray-500">#{{ $row['row_number'] }}</td>
                                    <td class="px-4 py-3 {{ $row['status'] == 'invalid' && str_contains($row['errors'], 'Nama') ? 'text-red-600 font-semibold' : '' }}">{{ $row['name'] }}</td>
                                    <td class="px-4 py-3 font-mono {{ $row['status'] == 'invalid' && str_contains($row['errors'], 'NISN') ? 'text-red-600 font-semibold' : '' }}">{{ $row['nisn'] }}</td>
                                    <td class="px-4 py-3 {{ $row['status'] == 'invalid' && str_contains($row['errors'], 'Gender') ? 'text-red-600 font-semibold' : '' }}">{{ $row['gender'] }}</td>
                                    <td class="px-4 py-3">
                                        @if($row['status'] == 'valid')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Valid
                                            </span>
                                        @else
                                            <div class="flex flex-col gap-1">
                                                <span class="inline-flex max-w-max items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mb-1">
                                                    Dilewati
                                                </span>
                                                <span class="text-xs text-red-600 font-medium">{{ $row['errors'] }}</span>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">File Excel terlihat kosong atau tidak memiliki baris data di bawah judul kolom.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-between border-t pt-4">
                    <a href="{{ route('admin.students.index') }}" class="text-gray-500 hover:text-gray-700 px-4 py-2">
                        Batal
                    </a>

                    <form method="POST" action="{{ route('admin.students.import.process') }}">
                        @csrf
                        <input type="hidden" name="temp_path" value="{{ $tempPath }}">
                        <input type="hidden" name="enrollment_year" value="{{ $enrollmentYear }}">
                        
                        <button type="submit" class="bg-[#1a6341] hover:bg-[#155034] text-white px-6 py-2.5 rounded-lg shadow-sm font-medium transition-colors disabled:opacity-50" {{ $validCount == 0 ? 'disabled' : '' }}>
                            Simpan {{ $validCount }} Data Valid
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
