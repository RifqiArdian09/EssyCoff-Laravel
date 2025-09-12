<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EssyCoff - Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
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
            background: linear-gradient(rgba(42, 26, 10, 0.7), rgba(42, 26, 10, 0.8)), url('/images/coffee-shop-bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
        }
        .logo-text {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            background: linear-gradient(135deg, #D4A76A 0%, #BF8B45 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .content-overlay {
            backdrop-filter: blur(2px);
            background: rgba(42, 26, 10, 0.3);
            border-radius: 20px;
            border: 1px solid rgba(212, 167, 106, 0.2);
        }
        .feature-card {
            backdrop-filter: blur(10px);
            background: rgba(62, 40, 19, 0.8);
            border: 1px solid rgba(212, 167, 106, 0.3);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center pt-6">
    <div class="w-full max-w-4xl mx-auto text-center flex flex-col justify-between min-h-[90vh] py-8">
        <div>
            <div class="mb-6" data-aos="zoom-in" data-aos-duration="1000">
                <i class="fas fa-mug-hot text-7xl text-coffee-gold"></i>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold text-coffee-cream mb-6 leading-tight" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000">
                Selamat Datang di <span class="logo-text">EssyCoff</span>
            </h1>

            <p class="text-lg md:text-xl text-coffee-gold mb-10 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="400" data-aos-duration="1000">
                Temukan kenikmatan kopi terbaik dengan cara yang mudah.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
                <div class="feature-card p-6 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:scale-105" data-aos="fade-right" data-aos-delay="600" data-aos-duration="800">
                    <i class="fas fa-coffee text-coffee-gold text-2xl mb-3"></i>
                    <h3 class="text-coffee-gold font-semibold text-lg mb-2">Kopi Premium</h3>
                    <p class="text-coffee-cream text-sm opacity-90">Biji pilihan terbaik dengan racikan khusus barista kami</p>
                </div>
                <div class="feature-card p-6 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:scale-105" data-aos="fade-left" data-aos-delay="800" data-aos-duration="800">
                    <i class="fas fa-bolt text-coffee-gold text-2xl mb-3"></i>
                    <h3 class="text-coffee-gold font-semibold text-lg mb-2">Pesan Cepat</h3>
                    <p class="text-coffee-cream text-sm opacity-90">Tanpa antri, cukup pesan dari genggaman tangan</p>
                </div>
               
            </div>
            
            <div class="text-center mt-8">
                <a href="{{ route('customer') }}" class="inline-block w-full max-w-sm mx-auto px-8 py-4 bg-gradient-to-r from-coffee-gold to-amber-700 text-coffee-dark font-bold text-lg text-center rounded-lg shadow-lg hover:shadow-xl hover:scale-105 transform transition-all duration-300 ease-in-out" data-aos="fade-up" data-aos-delay="1000" data-aos-duration="800">
                    Mulai Pesanan <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            mirror: false,
            offset: 100
        });
    </script>
</body>
</html>
