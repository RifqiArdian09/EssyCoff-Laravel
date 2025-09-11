<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EssyCoff - Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'coffee-dark': '#2A1A0A',
                        'coffee-medium': '#3E2813',
                        'coffee-light': '#523728',
                        'coffee-gold': '#D4A76A',
                        'coffee-cream': '#F5F5F5',
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            background: #2A1A0A;
        }
        .logo-text {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            background: linear-gradient(135deg, #D4A76A 0%, #BF8B45 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center pt-6">
    <div class="w-full max-w-4xl mx-auto text-center flex flex-col justify-between min-h-[90vh] py-8">
        <div>
            <div class="mb-6 animate-bounce">
                <i class="fas fa-mug-hot text-7xl text-coffee-gold"></i>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold text-coffee-cream mb-6 leading-tight">
                Selamat Datang di <span class="logo-text">EssyCoff</span>
            </h1>

            <p class="text-lg md:text-xl text-coffee-gold mb-10 max-w-2xl mx-auto">
                Temukan kenikmatan kopi terbaik dengan cara yang mudah.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                <div class="bg-coffee-light p-6 rounded-xl shadow-lg">
                    <i class="fas fa-coffee text-coffee-gold text-2xl mb-3"></i>
                    <h3 class="text-coffee-gold font-semibold text-lg mb-2">Kopi Premium</h3>
                    <p class="text-coffee-cream text-sm opacity-80">Biji pilihan terbaik dengan racikan khusus barista kami</p>
                </div>
                <div class="bg-coffee-light p-6 rounded-xl shadow-lg">
                    <i class="fas fa-bolt text-coffee-gold text-2xl mb-3"></i>
                    <h3 class="text-coffee-gold font-semibold text-lg mb-2">Pesan Cepat</h3>
                    <p class="text-coffee-cream text-sm opacity-80">Tanpa antri, cukup pesan dari genggaman tangan</p>
                </div>
                <div class="bg-coffee-light p-6 rounded-xl shadow-lg">
                    <i class="fas fa-tag text-coffee-gold text-2xl mb-3"></i>
                    <h3 class="text-coffee-gold font-semibold text-lg mb-2">Promo Spesial</h3>
                    <p class="text-coffee-cream text-sm opacity-80">Penawaran menarik setiap hari untuk pelanggan setia</p>
                </div>
            </div>
        </div>

        <div>
            <a href="{{ route('customer') }}" class="inline-block w-full max-w-sm mx-auto px-8 py-4 bg-gradient-to-r from-coffee-gold to-amber-700 text-coffee-dark font-bold text-lg text-center rounded-lg shadow-lg hover:shadow-xl hover:scale-105 transform transition-all duration-300 ease-in-out">
                Mulai Pesanan <i class="fas fa-arrow-right ml-2"></i>
            </a>
            <p class="text-xs text-coffee-gold mt-6 opacity-70">
                Dengan melanjutkan, Anda menyetujui Syarat & Ketentuan kami
            </p>
        </div>
    </div>
</body>
</html>
