<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            primary: '#6f4e37',
                            secondary: '#c0a080',
                            accent: '#e7c9a9',
                            dark: '#4a3c2d',
                        },
                        fontFamily: {
                            'inter': ['Inter', 'sans-serif'],
                        }
                    }
                }
            }
        </script>
    </head>
    <body class="font-inter min-h-screen bg-gradient-to-br from-primary to-secondary antialiased">
        <div class="relative grid h-screen flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
            <!-- Left Side - Coffee Theme -->
            <div class="relative hidden h-full flex-col p-10 text-white lg:flex">
                <div class="absolute inset-0 bg-gradient-to-br from-primary via-dark to-secondary opacity-95"></div>
                
                <!-- Coffee Bean Pattern Background -->
                <div class="absolute inset-0 opacity-20">
                    <div class="absolute top-10 left-10 w-8 h-8 bg-accent rounded-full transform rotate-45"></div>
                    <div class="absolute top-32 right-16 w-6 h-6 bg-accent rounded-full transform rotate-12"></div>
                    <div class="absolute bottom-32 left-16 w-10 h-10 bg-accent rounded-full transform -rotate-12"></div>
                    <div class="absolute bottom-16 right-20 w-4 h-4 bg-accent rounded-full"></div>
                    <div class="absolute top-1/2 left-1/4 w-12 h-12 bg-accent rounded-full transform rotate-45"></div>
                </div>

                <!-- Logo and Brand -->
                <div class="relative z-20 flex flex-col items-center">
                    <img src="{{ asset('images/logo.png') }}" alt="EssyCoff Logo" class="w-60 h-60 mb-3">
                </div>

                <!-- Welcome Message -->
                <div class="relative z-20 flex flex-col justify-center">
                    <div class="text-center">
                        <h2 class="text-4xl font-bold mb-6">Selamat Datang di EssyCoff</h2>
                        <p class="text-xl text-white text-opacity-90 leading-relaxed mb-8">
                            Nikmati pengalaman kopi terbaik dengan berbagai pilihan menu yang menggugah selera
                        </p>
                        
                        <!-- Coffee Cup Animation -->
                        <div class="flex justify-center mb-8">
                            <div class="relative">
                                <i class="fas fa-coffee text-6xl text-accent opacity-90 animate-pulse"></i>
                                <!-- Steam Animation -->
                                <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                                    <div class="w-1 h-4 bg-accent opacity-60 rounded-full animate-bounce absolute -left-2"></div>
                                    <div class="w-1 h-4 bg-accent opacity-60 rounded-full animate-bounce absolute left-0" style="animation-delay: 0.3s;"></div>
                                    <div class="w-1 h-4 bg-accent opacity-60 rounded-full animate-bounce absolute left-2" style="animation-delay: 0.6s;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Quote -->
                <div class="relative z-20">
                    <blockquote class="text-center">
                        <p class="text-lg italic text-white text-opacity-80">"Kopi yang baik adalah awal dari hari yang sempurna"</p>
                        <footer class="mt-2 text-sm text-white text-opacity-60">- EssyCoff Team</footer>
                    </blockquote>
                </div>
            </div>

            <!-- Right Side - Form -->
            <div class="w-full lg:p-8 bg-white lg:bg-transparent">
                <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[400px] bg-white rounded-2xl shadow-2xl p-8 lg:shadow-none">
                    <!-- Mobile Logo -->
                    <div class="flex flex-col items-center gap-3 lg:hidden">
                        <img src="{{ asset('images/logo.png') }}" alt="EssyCoff Logo" class="w-16 h-16">
                        <h1 class="text-2xl font-bold text-primary">EssyCoff</h1>
                    </div>
                    
                    {{ $slot }}
                </div>
            </div>
        </div>

        <!-- Background Decoration for Mobile -->
        <div class="fixed inset-0 pointer-events-none overflow-hidden lg:hidden">
            <div class="absolute top-10 left-10 w-20 h-20 bg-accent bg-opacity-20 rounded-full animate-pulse"></div>
            <div class="absolute top-1/4 right-16 w-16 h-16 bg-secondary bg-opacity-15 rounded-full animate-pulse" style="animation-delay: 1s;"></div>
            <div class="absolute bottom-1/4 left-1/4 w-12 h-12 bg-accent bg-opacity-20 rounded-full animate-pulse" style="animation-delay: 2s;"></div>
            <div class="absolute bottom-16 right-1/3 w-8 h-8 bg-secondary bg-opacity-15 rounded-full animate-pulse" style="animation-delay: 3s;"></div>
        </div>

        @fluxScripts
    </body>
</html>
