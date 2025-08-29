# EssyCoff - Laravel POS System

A modern Point of Sale (POS) system built with Laravel 12, Livewire, and Flux UI. EssyCoff provides a complete solution for managing coffee shop operations including inventory management, sales transactions, reporting, and user management.

## 🚀 Features

### 📊 **Dashboard & Analytics**
- Real-time sales overview
- Revenue tracking and statistics
- Interactive charts and graphs
- Key performance indicators (KPIs)

### 🛍️ **Point of Sale (POS)**
- Intuitive cashier interface
- Product search and selection
- Cart management
- Multiple payment methods
- Receipt generation and printing
- Transaction history

### 📦 **Inventory Management**
- Product catalog with categories
- Stock tracking and management
- Product image uploads
- Price management
- Low stock alerts

### 👥 **User Management**
- Role-based access control
- User profiles and authentication
- Staff management
- Activity logging

### 📈 **Reporting System**
- Sales reports with date filtering
- Export to PDF and Excel formats
- Revenue analysis
- Product performance metrics
- Light/Dark theme support

### 🎨 **Modern UI/UX**
- Responsive design with Tailwind CSS
- Dark/Light theme toggle
- Flux UI components
- Mobile-friendly interface
- Smooth animations and transitions

## 🛠️ Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Livewire 3, Flux UI, Tailwind CSS 4
- **Database**: MySQL
- **File Storage**: Laravel Storage
- **PDF Generation**: DomPDF
- **Excel Export**: Maatwebsite Excel
- **Icons**: Blade Heroicons
- **Build Tool**: Vite

## 📋 Requirements

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL database

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/RifqiArdian09/EssyCoff-Laravel.git
cd EssyCoff-Laravel
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Environment Setup

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Configuration

Edit your `.env` file with your database credentials:

**For MySQL:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=essycoff_pos
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Database Migration & Seeding

```bash
# Run migrations
php artisan migrate

# Seed the database with sample data
php artisan db:seed
```

### 6. Storage Setup

```bash
# Create symbolic link for file storage
php artisan storage:link
```

### 7. Start Development Server

```bash
# Start Laravel development server
php artisan serve

# In another terminal, start Vite dev server
npm run dev
```

Visit `http://localhost:8000` to access the application.

## 🔧 Development Commands

```bash
# Run all development services (Laravel + Queue + Vite)
composer run dev

# Run tests
composer run test
php artisan test

# Code formatting
./vendor/bin/pint

# Build for production
npm run build
```

## 📁 Project Structure

```
├── app/
│   ├── Exports/           # Excel export classes
│   ├── Http/Controllers/  # HTTP controllers
│   ├── Livewire/         # Livewire components
│   │   ├── Auth/         # Authentication components
│   │   ├── Categories/   # Category management
│   │   ├── Pos/          # POS system components
│   │   ├── Products/     # Product management
│   │   └── Report/       # Reporting components
│   └── Models/           # Eloquent models
├── database/
│   ├── migrations/       # Database migrations
│   └── seeders/         # Database seeders
├── resources/
│   ├── css/             # Stylesheets
│   ├── js/              # JavaScript files
│   └── views/           # Blade templates
└── routes/              # Application routes
```


## 📊 Reporting Features

- **Sales Reports**: Filter by date range, export to PDF/Excel
- **Product Analytics**: Best-selling products, stock levels
- **Revenue Tracking**: Daily, weekly, monthly revenue reports
- **Export Options**: PDF reports, Excel spreadsheets

