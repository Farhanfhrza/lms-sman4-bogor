<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Data Guru') }}
        </h2>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-[#1a6341] relative">
        <div class="p-6 text-gray-900">
            
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 bg-gray-100 p-4 rounded-t-lg border-b-2 border-yellow-400">
                <h3 class="text-lg font-bold text-gray-800 mb-4 md:mb-0">
                    Daftar Guru — Total: {{ $teachers->total() }}
                </h3>
                <a href="{{ route('admin.teachers.create') }}" class="bg-[#1a6341] hover:bg-[#238054] text-white px-4 py-2 rounded-lg shadow-sm transition-colors flex items-center text-sm font-medium">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Guru
                </a>
            </div>

            @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                {{ session('success') }}
            </div>
            @endif

            <form method="GET" action="{{ route('admin.teachers.index') }}" class="flex flex-col md:flex-row justify-between items-center mb-4 text-sm text-gray-600">
                <div class="mb-2 md:mb-0 flex items-center">
                    <span class="mr-2">Show</span>
                    <select name="per_page" onchange="this.form.submit()" class="border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm h-9 text-sm">
                        @foreach([10, 25, 50] as $pp)
                            <option value="{{ $pp }}" {{ request('per_page', 25) == $pp ? 'selected' : '' }}>{{ $pp }}</option>
                        @endforeach
                    </select>
                    <span class="ml-2">entries</span>
                </div>
                <div class="flex items-center w-full md:w-auto">
                    <span class="mr-2">Search:</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, NIP..."
                           class="border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm h-9 px-3 w-full md:w-64"
                           onchange="this.form.submit()">
                </div>
            </form>

            <div class="overflow-x-auto border-t border-b border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-gray-800 font-bold uppercase">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left tracking-wider">No</th>
                            <th scope="col" class="px-6 py-3 text-left tracking-wider">Nama</th>
                            <th scope="col" class="px-6 py-3 text-left tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-3 text-left tracking-wider">NIP</th>
                            <th scope="col" class="px-6 py-3 text-left tracking-wider">Spesialisasi</th>
                            <th scope="col" class="px-6 py-3 text-center tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($teachers as $index => $teacher)
                        <tr class="hover:bg-gray-50 transition-colors {{ $index % 2 === 0 ? 'bg-gray-100' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $teachers->firstItem() + $index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-700">{{ $teacher->user?->full_name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $teacher->user?->email ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $teacher->nip ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $teacher->specialization ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('admin.teachers.edit', $teacher) }}" class="text-blue-600 hover:text-blue-800 transition-colors" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <button @click="$dispatch('open-confirm-modal-del-teacher-{{ $teacher->id }}')" class="text-red-500 hover:text-red-700 transition-colors" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                    <x-confirm-delete-modal
                                        :id="'del-teacher-'.$teacher->id"
                                        title="Hapus Guru?"
                                        :description="'Data guru &quot;'.($teacher->user?->full_name ?? 'Guru').'&quot; beserta akun dan semua riwayat mengajarnya akan terhapus permanen.'"
                                        :confirmText="$teacher->user?->full_name ?? 'Guru'"
                                        :action="route('admin.teachers.destroy', $teacher)"
                                        buttonLabel="Ya, Hapus Guru"
                                    />
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                Belum ada data guru.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="h-1 w-full bg-gray-200">
                    <div class="h-1 bg-[#1a6341]" style="width: {{ $teachers->total() > 0 ? min(100, ($teachers->currentPage() / $teachers->lastPage()) * 100) : 0 }}%"></div>
                </div>
            </div>

            <div class="flex flex-col md:flex-row justify-between items-center mt-4 text-sm text-gray-600">
                <div class="mb-2 md:mb-0">
                    Showing {{ $teachers->firstItem() ?? 0 }} to {{ $teachers->lastItem() ?? 0 }} of {{ $teachers->total() }} entries
                </div>
                <div>
                    {{ $teachers->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
