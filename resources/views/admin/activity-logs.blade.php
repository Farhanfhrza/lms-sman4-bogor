<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Log Aktivitas') }}
        </h2>
    </x-slot>

    <!-- Content Card -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-[#1a6341] relative">
        <div class="p-6 text-gray-900">
            
            <!-- Card Header -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 bg-gray-100 p-4 rounded-t-lg border-b-2 border-yellow-400">
                <h3 class="text-lg font-bold text-gray-800 mb-4 md:mb-0">
                    Riwayat Aktivitas Sistem
                </h3>
                <div class="text-sm text-gray-500">
                    Total: {{ $logs->total() }} log tercatat
                </div>
            </div>

            <!-- Table Controls -->
            <form method="GET" action="{{ route('admin.activity-logs') }}" class="flex flex-col md:flex-row justify-between items-center mb-4 text-sm text-gray-600">
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
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama, email, deskripsi..."
                           class="border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm h-9 px-3 w-full md:w-64"
                           onchange="this.form.submit()">
                </div>
            </form>

            <!-- Responsive Table Wrapper -->
            <div class="overflow-x-auto border-t border-b border-gray-200">
                <style>
                    /* Custom Scrollbar for table */
                    .log-table-wrapper::-webkit-scrollbar { height: 6px; }
                    .log-table-wrapper::-webkit-scrollbar-track { background: #f1f1f1; }
                    .log-table-wrapper::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
                    .log-table-wrapper::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
                </style>
                <div class="log-table-wrapper overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-gray-800 font-bold uppercase">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left tracking-wider">Waktu</th>
                                <th scope="col" class="px-6 py-3 text-left tracking-wider">Pelaku</th>
                                <th scope="col" class="px-6 py-3 text-left tracking-wider">Aksi</th>
                                <th scope="col" class="px-6 py-3 text-left tracking-wider">Deskripsi</th>
                                <th scope="col" class="px-6 py-3 text-left tracking-wider">Kelas</th>
                                <th scope="col" class="px-6 py-3 text-left tracking-wider">IP Address</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($logs as $index => $log)
                            <tr class="hover:bg-gray-50 transition-colors {{ $index % 2 === 0 ? 'bg-gray-100' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ $log->created_at->format('d M Y, H:i') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-700">{{ $log->user->full_name ?? '-' }}</div>
                                    <div class="text-xs text-gray-400">{{ $log->user->login_identifier ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $actionColors = [
                                            'created' => 'bg-green-100 text-green-800',
                                            'updated' => 'bg-blue-100 text-blue-800',
                                            'deleted' => 'bg-red-100 text-red-800',
                                            'graded'  => 'bg-purple-100 text-purple-800',
                                            'submitted' => 'bg-yellow-100 text-yellow-800',
                                        ];
                                        $color = $actionColors[$log->action] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600 max-w-xs truncate" title="{{ $log->description }}">
                                    {{ $log->description ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                    {{ $log->course->subject->name ?? 'Global' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-400 text-xs font-mono">
                                    {{ $log->ip_address ?? '-' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    Belum ada log aktivitas tercatat.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Loading / Progress bar simulation at bottom of table (Green line) -->
                <div class="h-1 w-full bg-gray-200">
                    <div class="h-1 bg-[#1a6341]" style="width: {{ $logs->total() > 0 ? min(100, ($logs->currentPage() / $logs->lastPage()) * 100) : 0 }}%"></div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="flex flex-col md:flex-row justify-between items-center mt-4 text-sm text-gray-600">
                <div class="mb-2 md:mb-0">
                    Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} entries
                </div>
                <div>
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
