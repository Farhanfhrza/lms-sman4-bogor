<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Manajemen Slider Login') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">

        <div class="flex items-center justify-between mb-4 mt-2">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Manajemen Slider Login</h1>
        </div>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 shadow-sm" role="alert">
            <span class="block sm:inline whitespace-pre-wrap">{{ session('success') }}</span>
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 shadow-sm" role="alert">
            <strong class="font-bold">Ada kesalahan:</strong>
            <ul class="list-disc pl-5 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Form Upload Card -->
            <div class="col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Unggah Gambar Baru</h3>
                    
                    <form action="{{ route('admin.slider.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Gambar</label>
                            <!-- Custom File Upload -->
                            <div class="flex items-center justify-center w-full">
                                <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Klik untuk memilih</span></p>
                                        <p class="text-xs text-gray-500">JPG, PNG, WEBP (Max: 5MB)</p>
                                    </div>
                                    <input id="dropzone-file" type="file" name="image" class="hidden" accept="image/png, image/jpeg, image/jpg, image/webp" onchange="document.getElementById('file-name').textContent = this.files[0].name" />
                                </label>
                            </div>
                            <p id="file-name" class="mt-2 text-sm text-[#1a6341] font-medium text-center"></p>
                        </div>
                        
                        <button type="submit" class="w-full bg-[#1a6341] hover:bg-[#155034] text-white font-bold py-2 px-4 rounded-lg shadow-sm transition-colors flex justify-center items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Unggah Gambar
                        </button>
                    </form>
                    <div class="mt-4 text-xs text-gray-500 bg-blue-50 p-3 rounded-lg border border-blue-100">
                        <svg class="w-4 h-4 text-blue-500 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <strong>Info:</strong> Gambar rekomendasi memiliki resolusi tinggi dengan orientasi portrait (berdiri) atau square agar proporsional pada bagian kanan halaman login desktop.
                    </div>
                </div>
            </div>

            <!-- List Images Card -->
            <div class="col-span-1 md:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h3 class="text-lg font-bold text-gray-800">Daftar Gambar Slider Saat Ini</h3>
                        <span class="bg-green-100 text-[#1a6341] text-xs font-bold px-2.5 py-0.5 rounded-full border border-green-200">{{ count($images) }} Gambar</span>
                    </div>

                    @if(count($images) > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($images as $image)
                                <div class="relative group rounded-lg overflow-hidden border border-gray-200 shadow-sm">
                                    <div class="aspect-w-3 aspect-h-4 bg-gray-100">
                                        <img src="{{ $image['path'] }}" alt="{{ $image['name'] }}" class="object-cover w-full h-48">
                                    </div>
                                    <div class="p-3 bg-white border-t border-gray-200">
                                        <p class="text-xs text-gray-500 truncate" title="{{ $image['name'] }}">{{ $image['name'] }}</p>
                                        <p class="text-[10px] text-gray-400 mt-1">{{ $image['size'] }}</p>
                                    </div>
                                    
                                    <!-- Delete Button Overlay -->
                                    <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <form action="{{ route('admin.slider.destroy', $image['name']) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus gambar ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-full shadow-lg transform transition-transform hover:scale-110">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 border-2 border-dashed border-gray-300 rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-semibold text-gray-900">Belum ada gambar kustom</h3>
                            <p class="mt-1 text-sm text-gray-500">Halaman login saat ini menggunakan gambar default bawaan sistem.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
