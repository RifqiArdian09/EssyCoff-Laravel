# EssyCoff - Sistem POS Laravel

Sistem Point of Sale (POS) modern yang dibangun dengan Laravel 12, Livewire, dan Flux UI. EssyCoff menyediakan solusi lengkap untuk mengelola operasi kedai kopi termasuk manajemen inventori, transaksi penjualan, pelaporan, dan manajemen pengguna.

## 🚀 Fitur

### 📊 **Dashboard & Analitik**
- Ringkasan penjualan real-time
- Pelacakan pendapatan dan statistik
- Grafik dan chart interaktif


### 🛍️ **Point of Sale (POS)**
- Interface kasir yang intuitif
- Pencarian dan pemilihan produk
- Manajemen keranjang belanja
- Berbagai metode pembayaran
- Pembuatan dan pencetakan struk
- Riwayat transaksi

### 📦 **Manajemen Inventori**
- Katalog produk dengan kategori
- Pelacakan dan manajemen stok
- Upload gambar produk
- Manajemen harga
- Peringatan stok rendah

### 👥 **Manajemen Pengguna**
- Kontrol akses berbasis peran
- Profil pengguna dan autentikasi
- Manajemen staff
- Log aktivitas

### 📈 **Sistem Pelaporan**
- Laporan penjualan dengan filter tanggal
- Export ke format PDF dan Excel
- Analisis pendapatan
- Metrik performa produk
- Dukungan tema terang/gelap

### 🎨 **UI/UX Modern**
- Desain responsif dengan Tailwind CSS
- Toggle tema gelap/terang
- Komponen Flux UI
- Interface ramah mobile
- Animasi dan transisi yang halus

## 🛠️ Stack Teknologi

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Livewire 3, Flux UI, Tailwind CSS 4
- **Database**: MySQL
- **File Storage**: Laravel Storage
- **Pembuatan PDF**: DomPDF
- **Export Excel**: Maatwebsite Excel
- **Icons**: Blade Heroicons
- **Build Tool**: Vite

## 📋 Persyaratan

- PHP 8.2 atau lebih tinggi
- Composer
- Node.js & NPM
- Database MySQL

## 🚀 Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/RifqiArdian09/EssyCoff-Laravel.git
cd EssyCoff-Laravel
```

### 2. Install Dependencies

```bash
# Install dependencies PHP
composer install

# Install dependencies Node.js
npm install
```

### 3. Setup Environment

```bash
# Copy file environment
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Konfigurasi Database

Edit file `.env` dengan kredensial database Anda:

**Untuk MySQL:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Migrasi & Seeding Database

```bash
# Jalankan migrasi
php artisan migrate

# Seed database dengan data contoh
php artisan db:seed
```

### 6. Setup Storage

```bash
# Buat symbolic link untuk file storage
php artisan storage:link
```

### 7. Jalankan Development Server

```bash
# Jalankan Laravel development server
php artisan serve

# Di terminal lain, jalankan Vite dev server
npm run dev
```

Kunjungi `http://localhost:8000` untuk mengakses aplikasi.

## 📁 Struktur Project

```
├── app/
│   ├── Exports/           # Kelas export Excel
│   ├── Http/Controllers/  # HTTP controllers
│   ├── Livewire/         # Komponen Livewire
│   │   ├── Auth/         # Komponen autentikasi
│   │   ├── Categories/   # Manajemen kategori
│   │   ├── Pos/          # Komponen sistem POS
│   │   ├── Products/     # Manajemen produk
│   │   └── Report/       # Komponen pelaporan
│   └── Models/           # Model Eloquent
├── database/
│   ├── migrations/       # Migrasi database
│   └── seeders/         # Seeder database
├── resources/
│   ├── css/             # File CSS
│   ├── js/              # File JavaScript
│   └── views/           # Template Blade
└── routes/              # Route aplikasi
```

## 📊 Fitur Pelaporan

- **Laporan Penjualan**: Filter berdasarkan rentang tanggal, export ke PDF/Excel
- **Analitik Produk**: Produk terlaris, level stok
- **Pelacakan Pendapatan**: Laporan pendapatan harian, mingguan, bulanan
- **Opsi Export**: Laporan PDF, spreadsheet Excel

