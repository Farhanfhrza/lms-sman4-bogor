<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Beranda') }}
        </h2>
    </x-slot>

    <!-- Content Card -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-[#1a6341] relative">
        <div class="p-6 text-gray-900">
            
            <!-- Card Header -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 bg-gray-100 p-4 rounded-t-lg border-b-2 border-yellow-400">
                <h3 class="text-lg font-bold text-gray-800 mb-4 md:mb-0">
                    Jadwal Mata Pelajaran yang Diampu TP : 2026/2027
                </h3>
                <button class="bg-[#4ade80] hover:bg-[#22c55e] text-white px-3 py-1.5 rounded shadow-sm transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </button>
            </div>

            <!-- Table Controls -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-4 text-sm text-gray-600">
                <div class="mb-2 md:mb-0 flex items-center">
                    <span class="mr-2">Show</span>
                    <select class="border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm h-9 text-sm">
                        <option>10</option>
                        <option>25</option>
                        <option>50</option>
                    </select>
                    <span class="ml-2">entries</span>
                </div>
                <div class="flex items-center w-full md:w-auto">
                    <span class="mr-2">Search:</span>
                    <input type="text" class="border-gray-300 focus:border-[#1a6341] focus:ring-[#1a6341] rounded-md shadow-sm h-9 px-3 w-full md:w-64">
                </div>
            </div>

            <!-- Responsive Table Wrapper -->
            <div class="overflow-x-auto border-t border-b border-gray-200">
                <style>
                    /* Custom Scrollbar for table */
                    ::-webkit-scrollbar { height: 6px; }
                    ::-webkit-scrollbar-track { background: #f1f1f1; }
                    ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
                    ::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
                </style>
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-gray-800 font-bold uppercase">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left tracking-wider">Hari</th>
                            <th scope="col" class="px-6 py-3 text-left tracking-wider">Jam</th>
                            <th scope="col" class="px-6 py-3 text-left tracking-wider">Mata Pelajaran</th>
                            <th scope="col" class="px-6 py-3 text-left tracking-wider">Kelas</th>
                            <th scope="col" class="px-6 py-3 text-left tracking-wider">Pengajar</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Row 1 -->
                        <tr class="hover:bg-gray-50 transition-colors bg-gray-100">
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-700">Senin</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-500 flex items-center">
                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                07:30:00 - 09:30:00
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">Sejarah Indonesia</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">12 MIPA B</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">Drs. H. Sumarno, S.Pd</td>
                        </tr>
                        <!-- Row 2 -->
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-700">Senin</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-500 flex items-center">
                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                09:45:00 - 12:30:00
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">Matematika Umum</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">12 MIPA B</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">Sri Wahyuni, S.Pd</td>
                        </tr>
                        <!-- Row 3 -->
                        <tr class="hover:bg-gray-50 transition-colors bg-gray-100">
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-700">Selasa</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-500 flex items-center">
                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                07:30:00 - 09:30:00
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">Biologi</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">12 MIPA B</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">Bambang Sutrisno, S.Pd</td>
                        </tr>
                        <!-- Row 4 -->
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-700">Selasa</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-500 flex items-center">
                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                09:45:00 - 12:30:00
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">Bahasa Indonesia</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">12 MIPA B</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">Nurhayati, S.Pd</td>
                        </tr>
                         <!-- Row 5 -->
                         <tr class="hover:bg-gray-50 transition-colors bg-gray-100">
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-700">Rabu</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-500 flex items-center">
                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                07:30:00 - 09:30:00
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">Bahasa Inggris</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">12 MIPA B</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">Ahmad Fauzi, S.Pd</td>
                        </tr>
                    </tbody>
                </table>
                 <!-- Loading / Progress bar simulation at bottom of table (Green line) -->
                <div class="h-1 w-full bg-gray-200">
                    <div class="h-1 bg-[#1a6341] w-1/3"></div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="flex flex-col md:flex-row justify-between items-center mt-4 text-sm text-gray-600">
                <div class="mb-2 md:mb-0">
                    Showing 1 to 5 of 5 entries
                </div>
                <div class="flex items-center space-x-1">
                    <button class="px-3 py-1 border rounded hover:bg-gray-100 disabled:opacity-50" disabled>Previous</button>
                    <button class="px-3 py-1 bg-[#4ade80] text-white border border-[#4ade80] rounded">1</button>
                    <button class="px-3 py-1 border rounded hover:bg-gray-100 disabled:opacity-50" disabled>Next</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
