@php
    $sliderPath = public_path('images/slider');
    $sliderImages = [];
    if (\Illuminate\Support\Facades\File::exists($sliderPath)) {
        $files = \Illuminate\Support\Facades\File::files($sliderPath);
        foreach ($files as $file) {
            if (in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $sliderImages[] = asset('images/slider/' . $file->getFilename());
            }
        }
    }
    
    // Fallback if no images found
    if (empty($sliderImages)) {
        $sliderImages[] = asset('images/backgrounds/school_building.png');
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LMS SMAN 4 BOGOR - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-screen flex flex-col lg:flex-row">

    <!-- Mobile Background Wrapper (visible only on mobile/tablet) -->
    <div class="fixed inset-0 z-[-1] lg:hidden bg-cover bg-center" 
         style="background-image: url('{{ $sliderImages[0] }}');">
    </div>

    <!-- Main Content Wrapper -->
    <div class="w-full lg:w-1/2 flex flex-col justify-center lg:justify-between min-h-screen p-4 md:p-8 lg:p-20 z-10">
        
        <!-- Mobile Card / Desktop Left Column -->
        <div class="bg-white rounded-xl shadow-2xl lg:shadow-none p-6 md:p-10 lg:p-0 w-full max-w-md mx-auto lg:max-w-none lg:mx-0 flex flex-col justify-center h-auto lg:h-full border-[3px] border-[#3b82f6] lg:border-none relative">
            
            <!-- Header Section -->
            <div class="flex flex-col items-center lg:items-start space-y-4 lg:space-y-6 mb-8 lg:mb-0 lg:mt-0 pt-2 lg:pt-0">
                <!-- Logos -->
                <div class="flex items-center justify-center gap-3 lg:gap-4 mb-2 lg:mb-4">
                    <img src="{{ asset('images/logos/instansi_logos.png') }}" alt="Logos Instansi" class="h-10 md:h-12 lg:h-16 object-contain">
                </div>

                <!-- Welcome Text -->
                <div class="text-center lg:text-left space-y-1 lg:space-y-2">
                    <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 leading-snug">
                        Halo, Selamat Datang di
                    </h1>
                    <p class="text-sm md:text-base lg:text-xl font-bold text-gray-800">
                        Learning Management System (LMS) SMAN 4 BOGOR
                    </p>
                </div>
            </div>

            <!-- Form Section -->
            <div class="w-full space-y-6 lg:space-y-8 my-2 lg:my-auto" x-data="{ submitting: false }">
                
                <!-- Global Error Message -->
                @if($errors->has('login_input'))
                    <div class="text-center mb-4">
                        <p class="text-red-600 font-semibold text-sm md:text-base">
                            Invalid Username or password.
                        </p>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-4 lg:space-y-5" @submit="submitting = true">
                    @csrf
                    
                    <div class="space-y-4">
                        <!-- Login Identifier Input -->
                        <div>
                            <input type="text" 
                                   name="login_input" 
                                   value="{{ old('login_input') }}"
                                   placeholder="Email / NIP / NISN" 
                                   class="w-full px-4 py-3 border rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#1a6341] focus:border-transparent transition-all bg-gray-50 lg:bg-white
                                          {{ $errors->has('login_input') ? 'border-red-500' : 'border-gray-400' }}"
                                   required autofocus>
                        </div>

                        <!-- Password Input with Visibility Toggle -->
                        <div x-data="{ show: false }" class="relative">
                            <input :type="show ? 'text' : 'password'" 
                                   name="password" 
                                   placeholder="Password" 
                                   class="w-full px-4 py-3 border rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#1a6341] focus:border-transparent transition-all bg-gray-50 lg:bg-white pr-10
                                          {{ $errors->has('password') || $errors->has('login_input') ? 'border-red-500' : 'border-gray-400' }}"
                                   required>
                            
                            <!-- Toggle Icon -->
                            <button type="button" 
                                    @click="show = !show" 
                                    class="absolute inset-y-0 right-0 px-3 flex items-center focus:outline-none">
                                <!-- Eye Open Icon -->
                                <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-500 hover:text-gray-700">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <!-- Eye Slash Icon -->
                                <svg x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-[#1a6341] hover:text-[#155034]" style="display: none;">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full bg-[#1a6341] hover:bg-[#155034] text-white font-bold py-3 rounded-lg transition-colors duration-300 shadow-lg lg:shadow-md flex justify-center items-center"
                            :class="{ 'opacity-75 cursor-not-allowed': submitting }"
                            :disabled="submitting">
                        <span x-show="!submitting">Sign in</span>
                        <span x-show="submitting" class="flex items-center justify-center" style="display: none;">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Signing in...
                        </span>
                    </button>

                    <div class="text-center lg:text-start">
                        <a href="{{ route('password.request') }}" class="text-blue-900 hover:text-blue-700 hover:underline font-bold text-sm transition-colors">
                            Forgotten your username or password?
                        </a>
                    </div>
                </form>
            </div>

            <!-- Footer for Desktop (Hidden on Mobile) -->
            <div class="hidden lg:block text-xs text-gray-600 font-medium mt-auto">
                &copy; 2026 SMAN 4 Bogor. All rights reserved.
            </div>
        </div>
    </div>

    <!-- Mobile Footer (Fixed at bottom) -->
    <div class="lg:hidden fixed bottom-0 left-0 w-full bg-[#112318] text-white text-center py-3 text-xs font-medium z-20">
        &copy; 2026 SMAN 4 Bogor. All rights reserved.
    </div>

    <!-- Right Column: Visual Image Slider (Desktop Only) -->
    <div class="hidden lg:block lg:w-1/2 relative bg-gray-100 overflow-hidden" id="desktop-slider">
        <!-- Slider Images Container -->
        <div id="slider-images" class="absolute inset-0 w-full h-full">
            @foreach($sliderImages as $index => $image)
            <div class="absolute inset-0 bg-cover bg-center transition-opacity duration-1000 slide-image {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}" 
                 style="background-image: url('{{ $image }}');"></div>
            @endforeach
        </div>

        <!-- Detail Overlay: Fade to White at Top & Bottom -->
        <div class="absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-white via-white/40 to-transparent z-10"></div>
        <div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-white via-white/40 to-transparent z-10"></div>

        <!-- Slider Indicators -->
        @if(count($sliderImages) > 1)
        <div class="absolute bottom-12 left-1/2 transform -translate-x-1/2 flex items-center space-x-3 z-30">
            @foreach($sliderImages as $index => $image)
            <button onclick="goToSlide({{ $index }})" class="slider-dot w-3 h-3 rounded-full transition-all duration-300 {{ $index === 0 ? 'bg-[#1a6341] hover:scale-110' : 'border-2 border-[#1a6341] bg-transparent hover:bg-[#1a6341]/20' }}" aria-label="Slide {{ $index + 1 }}"></button>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Slider JS -->
    <script>
        const slides = document.querySelectorAll('.slide-image');
        const dots = document.querySelectorAll('.slider-dot');
        let currentSlide = 0;
        const totalSlides = slides.length;
        let slideInterval;

        function updateSlider() {
            slides.forEach((slide, index) => {
                if(index === currentSlide) {
                    slide.classList.remove('opacity-0');
                    slide.classList.add('opacity-100');
                } else {
                    slide.classList.remove('opacity-100');
                    slide.classList.add('opacity-0');
                }
            });

            dots.forEach((dot, index) => {
                if(index === currentSlide) {
                    dot.classList.remove('bg-transparent', 'border-2');
                    dot.classList.add('bg-[#1a6341]');
                } else {
                    dot.classList.add('bg-transparent', 'border-2', 'border-[#1a6341]');
                    dot.classList.remove('bg-[#1a6341]');
                }
            });
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            updateSlider();
        }

        function goToSlide(index) {
            currentSlide = index;
            updateSlider();
            resetTimer();
        }

        function resetTimer() {
            clearInterval(slideInterval);
            slideInterval = setInterval(nextSlide, 5000); // 5 seconds per slide
        }

        // Initialize slider if elements exist (Desktop only check implicity handled by CSS hiding, but JS runs)
        if(slides.length > 0) {
            updateSlider();
            resetTimer();
        }
    </script>
</body>
</html>
