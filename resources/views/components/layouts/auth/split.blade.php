 
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body class="font-sans min-h-screen antialiased overflow-y-auto no-scrollbar">
    <div class="grid h-screen lg:grid-cols-2">
        <!-- Left Side - Background Image Only -->
        <div class="hidden lg:block bg-cover bg-center" style="background-image: url('{{ asset('images/background.jpg') }}');">
        </div>

        <!-- Right Side - Form -->
        
        <div class="flex w-full items-center justify-center bg-white p-6 lg:p-8">
         
            <div class="mx-auto w-full sm:w-[500px] lg:w-[550px] flex flex-col justify-center space-y-8">

                <!-- Slot langsung tanpa card -->
                {{ $slot }}
            </div>
        </div>
    </div>

    @fluxScripts
</body>
</html>
