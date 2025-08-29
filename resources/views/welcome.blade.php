<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EssyCoff - Selamat Datang</title>
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
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 1s ease-out',
                        'fade-in-up-delay-1': 'fadeInUp 1s ease-out 0.3s both',
                        'fade-in-up-delay-2': 'fadeInUp 1s ease-out 0.6s both',
                        'bounce-slow': 'bounce 3s infinite',
                        'pulse-slow': 'pulse 4s infinite',
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': {
                                opacity: '0',
                                transform: 'translateY(30px)',
                            },
                            '100%': {
                                opacity: '1',
                                transform: 'translateY(0)',
                            },
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="font-inter bg-gradient-to-br from-primary to-secondary min-h-screen flex items-center justify-center">
    <div class="text-center max-w-md mx-auto px-6">
        <!-- Logo Container -->
        <div class="animate-fade-in-up mb-8">
            <div class="relative inline-block">
                <img src="{{ asset('images/logo.png') }}" alt="EssyCoff Logo" class="w-32 h-32 mx-auto mb-4 animate-pulse-slow">
                <!-- Coffee Steam Animation -->
                <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-4">
                    <div class="w-0.5 h-5 bg-white bg-opacity-30 rounded-full animate-bounce-slow absolute left-2"></div>
                    <div class="w-0.5 h-5 bg-white bg-opacity-30 rounded-full animate-bounce-slow absolute left-1/2 transform -translate-x-1/2" style="animation-delay: 0.5s;"></div>
                    <div class="w-0.5 h-5 bg-white bg-opacity-30 rounded-full animate-bounce-slow absolute right-2" style="animation-delay: 1s;"></div>
                </div>
            </div>
            <h1 class="text-4xl font-bold text-white mb-2">EssyCoff</h1>
            <p class="text-white text-opacity-90 text-lg">Kopi Berkualitas untuk Hari yang Sempurna</p>
        </div>
        
        <!-- Welcome Text -->
        <div class="animate-fade-in-up-delay-1 mb-8">
            <h2 class="text-2xl font-semibold text-white mb-4">Selamat Datang!</h2>
            <p class="text-white text-opacity-80 leading-relaxed">
                Nikmati pengalaman kopi terbaik dengan berbagai pilihan menu yang menggugah selera. 
                Bergabunglah dengan kami untuk memulai perjalanan rasa yang tak terlupakan.
            </p>
        </div>
        
        <!-- Login Button -->
        <div class="animate-fade-in-up-delay-2">
            <a href="{{ route('login') }}" class="inline-flex items-center justify-center w-full bg-white text-primary font-semibold py-4 px-8 rounded-xl shadow-lg hover:bg-gray-50 hover:-translate-y-0.5 hover:shadow-2xl transition-all duration-300 transform">
                <i class="fas fa-sign-in-alt mr-3"></i>
                Masuk ke Akun Anda
            </a>
        </div>
    </div>
    
    <!-- Background Decoration -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-10 left-10 w-20 h-20 bg-white bg-opacity-10 rounded-full animate-pulse"></div>
        <div class="absolute top-1/4 right-16 w-16 h-16 bg-white bg-opacity-5 rounded-full animate-pulse" style="animation-delay: 1s;"></div>
        <div class="absolute bottom-1/4 left-1/4 w-12 h-12 bg-white bg-opacity-10 rounded-full animate-pulse" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-16 right-1/3 w-8 h-8 bg-white bg-opacity-5 rounded-full animate-pulse" style="animation-delay: 3s;"></div>
        <div class="absolute top-1/2 left-8 w-6 h-6 bg-white bg-opacity-5 rounded-full animate-bounce" style="animation-delay: 1.5s;"></div>
        <div class="absolute top-3/4 right-8 w-10 h-10 bg-white bg-opacity-10 rounded-full animate-bounce" style="animation-delay: 2.5s;"></div>
    </div>
</body>
</html>