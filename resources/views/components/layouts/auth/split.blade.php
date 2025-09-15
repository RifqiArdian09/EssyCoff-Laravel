 
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body class="font-sans min-h-screen antialiased">
    <div class="grid h-screen lg:grid-cols-2">
        <!-- Left Side - Background Image Only -->
        <div class="hidden lg:block bg-cover bg-center" style="background-image: url('{{ asset('images/background.jpg') }}');">
        </div>

        <!-- Right Side - Form -->
        
        <div class="flex w-full items-center justify-center bg-white p-6 lg:p-8">
         
            <div class="mx-auto w-full sm:w-[500px] lg:w-[550px] flex flex-col justify-center space-y-8">
                <!-- Mobile Logo -->
                <div class="flex flex-col items-center gap-4 lg:hidden">
                    <img src="{{ asset('images/logo.png') }}" alt="EssyCoff Logo" class="w-20 h-20">
                    <h1 class="text-5xl font-bold text-primary">EssyCoff</h1>
                </div>

                <!-- Slot langsung tanpa card -->
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
