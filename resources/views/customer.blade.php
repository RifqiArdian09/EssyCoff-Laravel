<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>EssyCoff - Order</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(rgba(42, 26, 10, 0.7), rgba(42, 26, 10, 0.8)), url('/images/coffee-shop-bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
        }
        
        .content-overlay {
            backdrop-filter: blur(2px);
            background: rgba(245, 245, 245, 0.95);
            border-radius: 20px;
            border: 1px solid rgba(212, 167, 106, 0.2);
        }

        /* Success Modal Animations */
        .success-modal {
            animation: modalFadeIn 0.3s ease-out;
        }

        .checkmark-circle {
            animation: scaleIn 0.3s ease-out 0.2s both;
        }

        .checkmark {
            animation: drawCheckmark 0.5s ease-out 0.5s both;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: scale(0.8);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }

            to {
                transform: scale(1);
            }
        }

        @keyframes drawCheckmark {
            from {
                stroke-dashoffset: 100;
            }

            to {
                stroke-dashoffset: 0;
            }
        }

        .checkmark-path {
            stroke-dasharray: 100;
            stroke-dashoffset: 100;
        }

        /* Hide sensitive data from print */
        @media print {
            #chat-container,
            #order-modal,
            #success-modal,
            #clear-cart-modal,
            #chat-bubble,
            .fixed {
                display: none !important;
            }
            
            /* Only show the main menu content when printing */
            body * {
                visibility: hidden;
            }
            
            main, main * {
                visibility: visible;
            }
            
            /* Hide floating elements and modals */
            .fixed,
            [id*="modal"],
            [id*="chat"],
            [id*="cart"] {
                visibility: hidden !important;
                display: none !important;
            }
        }
    </style>
</head>

<body class="min-h-screen bg-coffee-cream">

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Hero Section -->
        <div class="text-center mb-12">
            <!-- Logo and Title -->
            <div class="flex items-center justify-center mb-4">
                <div class="w-16 h-16 bg-gradient-to-br from-coffee-gold to-coffee-medium rounded-2xl flex items-center justify-center mr-4 shadow-lg">
                    <img src="{{ asset('images/logo2.png') }}" alt="EssyCoff Logo" class="w-10 h-10 rounded-xl">
                </div>
                <h1 class="text-4xl sm:text-5xl font-bold text-gray-900">
                    <span class="text-coffee-gold">EssyCoff</span>
                </h1>
            </div>
            <div class="inline-flex items-center px-4 py-2 bg-coffee-gold bg-opacity-20 text-white rounded-full text-sm font-medium mb-4">
                <i class="fas fa-fire mr-2"></i>
                Menu Terbaru & Terlezat
            </div>
            <p class="text-xl text-white max-w-3xl mx-auto leading-relaxed">
                Nikmati pengalaman kuliner terbaik dengan menu pilihan berkualitas premium
            </p>
        </div>

        <!-- Search & Filter Section with Extended Background -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
            <!-- Search Box -->
            <form method="GET" action="{{ route('customer') }}" class="mb-6">
                <div class="relative max-w-2xl mx-auto">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                        placeholder="Cari menu favorit Anda..."
                        class="block w-full pl-12 pr-20 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-coffee-gold focus:border-coffee-gold text-lg placeholder-gray-400 shadow-md focus:shadow-lg transition-shadow">
                    <input type="hidden" name="category" value="{{ $category ?? 'all' }}">
                    <button type="submit" class="absolute inset-y-0 right-0 flex items-center px-6 bg-coffee-medium hover:bg-coffee-dark text-white rounded-r-xl transition-colors">
                        Cari
                    </button>
                </div>
            </form>

            <!-- Category Filter -->
            <div class="flex flex-wrap gap-2 justify-center mb-8">
                <a href="{{ route('customer', ['search' => $search ?? '', 'category' => 'all']) }}"
                    class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all {{ ($category ?? 'all') === 'all' ? 'bg-coffee-medium text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-th-large mr-2"></i>Semua
                </a>
                @foreach($categories as $cat)
                <a href="{{ route('customer', ['search' => $search ?? '', 'category' => strtolower($cat->name)]) }}"
                    class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all {{ ($category ?? 'all') === strtolower($cat->name) ? 'bg-coffee-medium text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    @php
                    $categoryName = strtolower($cat->name);
                    $icon = 'fa-utensils'; // default icon

                    

                    // Mapping kategori ke ikon yang sesuai
                    if (strpos($categoryName, 'kopi') !== false || strpos($categoryName, 'coffee') !== false) {
                        $icon = 'fa-mug-saucer';
                    } elseif (strpos($categoryName, 'teh') !== false || strpos($categoryName, 'tea') !== false) {
                        $icon = 'fa-mug-hot';
                    } elseif (strpos($categoryName, 'minuman') !== false || strpos($categoryName, 'drink') !== false || strpos($categoryName, 'beverage') !== false) {
                        $icon = 'fa-wine-glass';
                    } elseif (strpos($categoryName, 'makanan') !== false || strpos($categoryName, 'food') !== false) {
                        $icon = 'fa-hamburger';
                    } elseif (strpos($categoryName, 'nasi') !== false || strpos($categoryName, 'rice') !== false) {
                        $icon = 'fa-bowl-rice';
                    } elseif (strpos($categoryName, 'mie') !== false || strpos($categoryName, 'noodle') !== false || strpos($categoryName, 'pasta') !== false) {
                        $icon = 'fa-bowl-food';
                    } elseif (strpos($categoryName, 'ayam') !== false || strpos($categoryName, 'chicken') !== false) {
                        $icon = 'fa-drumstick-bite';
                    } elseif (strpos($categoryName, 'pizza') !== false) {
                        $icon = 'fa-pizza-slice';
                    } elseif (strpos($categoryName, 'sandwich') !== false || strpos($categoryName, 'burger') !== false) {
                        $icon = 'fa-hamburger';
                    } elseif (strpos($categoryName, 'snack') !== false || strpos($categoryName, 'cemilan') !== false) {
                        $icon = 'fa-cookie';
                    } elseif (strpos($categoryName, 'dessert') !== false || strpos($categoryName, 'manis') !== false || strpos($categoryName, 'cake') !== false) {
                        $icon = 'fa-cake-candles';
                    } elseif (strpos($categoryName, 'es krim') !== false || strpos($categoryName, 'ice cream') !== false) {
                        $icon = 'fa-ice-cream';
                    } elseif (strpos($categoryName, 'sarapan') !== false || strpos($categoryName, 'breakfast') !== false) {
                        $icon = 'fa-egg';
                    } elseif (strpos($categoryName, 'salad') !== false) {
                        $icon = 'fa-leaf';
                    } elseif (strpos($categoryName, 'soup') !== false || strpos($categoryName, 'sop') !== false) {
                        $icon = 'fa-bowl-hot';
                    } else {
                        $icon = 'fa-utensils'; // fallback
                    }
                    @endphp
                    <i class="fas {{ $icon }} mr-2"></i>{{ $cat->name }}
                </a>
                @endforeach
                <a href="{{ route('customer', ['search' => $search ?? '', 'category' => 'favorite']) }}"
                    class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all {{ ($category ?? 'all') === 'favorite' ? 'bg-red-500 text-white shadow-md' : 'bg-red-50 text-red-600 hover:bg-red-100' }}">
                    <i class="fas fa-heart mr-2"></i>Favorit
                </a>
            </div>

            <!-- Menu Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="menu-container">
            @forelse($products as $product)
            <div class="group bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-200 {{ $product->stock <= 0 ? 'opacity-60' : '' }}" data-category="{{ strtolower($product->category->name ?? 'uncategorized') }}">
                <div class="relative">
                    @if($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                    @else
                    <div class="w-full h-48 bg-gradient-to-br from-coffee-cream to-coffee-gold bg-opacity-30 flex items-center justify-center">
                        <i class="fas fa-utensils text-coffee-gold text-3xl"></i>
                    </div>
                    @endif

                    @if($product->stock <= 0)
                        <div class="absolute inset-0 bg-black bg-opacity-60 flex items-center justify-center">
                        <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                            Habis
                        </span>
                </div>
                @else
                <div class="absolute top-2 right-2 bg-green-500 text-white rounded-lg px-2 py-1 text-xs font-semibold">
                    Stok: {{ $product->stock }}
                </div>
                @endif
            </div>

            <div class="p-4">
                <div class="mb-3">
                    <h3 class="font-semibold text-gray-900 text-lg mb-1">{{ $product->name }}</h3>
                    <div class="flex items-center justify-between mb-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-coffee-gold bg-opacity-20 text-coffee-dark">
                            {{ $product->category->name ?? 'Kategori tidak tersedia' }}
                        </span>
                        @if($product->favorite_data['total_ordered'] > 0)
                        <div class="flex items-center text-xs text-red-500">
                            <i class="fas fa-heart mr-1"></i>
                            <span class="font-medium">{{ $product->favorite_data['total_ordered'] }}x dipesan</span>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="text-lg font-bold text-gray-900">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </div>

                    @if($product->stock > 0)
                    <button class="add-to-cart bg-coffee-medium hover:bg-coffee-dark text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                        data-id="{{ $product->id }}"
                        data-name="{{ $product->name }}"
                        data-price="{{ $product->price }}"
                        data-stock="{{ $product->stock }}">
                        <i class="fas fa-plus mr-1"></i>Tambah
                    </button>
                    @else
                    <span class="text-gray-400 text-sm font-medium">
                        Tidak Tersedia
                    </span>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <div class="bg-white rounded-xl shadow-sm p-8 max-w-md mx-auto">
                <i class="fas fa-coffee text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Produk</h3>
                <p class="text-gray-500">Menu akan segera tersedia</p>
            </div>
        </div>
        @endforelse
        </div>
        </div>
    </main>

    <!-- Floating Cart Button -->
    <div class="fixed bottom-6 right-6 z-10">
        <button id="chat-bubble" class="bg-coffee-medium hover:bg-coffee-dark text-white w-14 h-14 rounded-full shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center">
            <i class="fas fa-shopping-cart text-lg"></i>
            <span id="cart-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 items-center justify-center" style="display: none;">0</span>
        </button>
    </div>

    <!-- Cart Sidebar -->
    <div class="fixed inset-y-0 right-0 w-96 bg-white shadow-xl z-40 transform translate-x-full transition-transform duration-300 ease-in-out" id="chat-container">
        <div class="flex flex-col h-full">
            <!-- Cart Header -->
            <div class="bg-coffee-medium text-white p-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold">Keranjang Belanja</h3>
                <button id="close-chat" class="text-white hover:bg-coffee-dark p-2 rounded-lg transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Cart Items -->
            <div class="flex-1 overflow-y-auto p-4" id="chat-body">
                <div class="text-center py-16 text-gray-500">
                    <i class="fas fa-shopping-cart text-5xl mb-4 text-gray-300"></i>
                    <p class="text-lg font-medium mb-2">Keranjang Kosong</p>
                    <p class="text-sm text-gray-400">Tambahkan produk untuk mulai berbelanja</p>
                </div>
            </div>

            <!-- Cart Footer -->
            <div class="border-t border-gray-200 p-4 bg-gray-50">
                <!-- Customer Name Input -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Pemesan</label>
                    <input type="text" id="cart-customer-name" placeholder="Masukkan nama Anda" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-coffee-gold focus:border-coffee-gold text-sm">
                </div>
                
                <div class="flex justify-between items-center mb-4">
                    <span class="text-gray-700 font-medium">Total:</span>
                    <span class="text-2xl font-bold text-gray-900">Rp <span id="cart-total">0</span></span>
                </div>
                
                <div class="space-y-2">
                    <button id="checkout-btn" class="w-full bg-coffee-medium hover:bg-coffee-dark text-white font-semibold py-3 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                        Checkout
                    </button>
                    <button id="clear-cart-btn" class="w-full bg-red-500 hover:bg-red-600 text-white font-medium py-2 rounded-lg transition-colors">
                        <i class="fas fa-trash mr-2"></i>Kosongkan Semua
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- History Sidebar Removed -->

    <!-- Order Confirmation Modal -->
    <div id="order-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-xl p-6 max-w-md w-full mx-4">
            <div class="text-center mb-6">
                <div class="w-12 h-12 bg-coffee-medium rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-receipt text-white"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Konfirmasi Pesanan</h3>
                <p class="text-gray-600 text-sm mt-1">Apakah Anda yakin ingin melanjutkan pesanan?</p>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-600">Nama Pemesan:</span>
                    <span class="font-medium text-gray-900" id="modal-customer-name">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="font-medium text-gray-700">Total:</span>
                    <span class="font-bold text-xl text-gray-900">Rp <span id="modal-total">0</span></span>
                </div>
            </div>

            <div class="flex space-x-3">
                <button id="cancel-order" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-3 px-4 rounded-lg transition-colors">
                    Batal
                </button>
                <button id="confirm-order" class="flex-1 bg-coffee-medium hover:bg-coffee-dark text-white font-medium py-3 px-4 rounded-lg transition-colors">
                    Konfirmasi Pesanan
                </button>
            </div>
        </div>
    </div>

    <!-- Clear Cart Confirmation Modal -->
    <div id="clear-cart-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-xl p-6 max-w-sm w-full mx-4">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-trash text-red-500 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Kosongkan Keranjang?</h3>
                <p class="text-gray-600 text-sm">Semua produk dalam keranjang akan dihapus. Tindakan ini tidak dapat dibatalkan.</p>
            </div>

            <div class="flex space-x-3">
                <button id="cancel-clear-cart" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-3 px-4 rounded-lg transition-colors">
                    Batal
                </button>
                <button id="confirm-clear-cart" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                    <i class="fas fa-trash mr-2"></i>Kosongkan
                </button>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="success-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="success-modal bg-white rounded-2xl shadow-2xl p-8 max-w-sm w-full mx-4 text-center">
            <!-- Animated Checkmark -->
            <div class="checkmark-circle w-20 h-20 mx-auto mb-6 relative">
                <svg class="w-20 h-20" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="45" fill="#10B981" class="checkmark-circle" />
                    <path class="checkmark checkmark-path" d="M25 50 L40 65 L75 30"
                        stroke="white" stroke-width="6" fill="none" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>

            <!-- Success Message -->
            <h3 class="text-2xl font-bold text-gray-900 mb-3">Pesanan Berhasil!</h3>
            <p class="text-gray-600 mb-2">Pesanan Anda telah dikirim</p>
            <div class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium mb-6">
                <i class="fas fa-clock mr-2"></i>
                Status: Pending Payment
            </div>

            <!-- Order Details -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-600">Nama Pemesan:</span>
                    <span class="font-medium text-gray-900" id="success-customer-name">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Pembayaran:</span>
                    <span class="font-bold text-lg text-coffee-medium">Rp <span id="success-total">0</span></span>
                </div>
            </div>

            <!-- Action Button -->
            <button id="close-success-modal" class="w-full bg-coffee-medium hover:bg-coffee-dark text-white font-semibold py-3 rounded-lg transition-colors">
                <i class="fas fa-check mr-2"></i>Selesai
            </button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let cart = [];
            const chatBubble = document.getElementById('chat-bubble');
            const chatContainer = document.getElementById('chat-container');
            const chatBody = document.getElementById('chat-body');
            const cartCount = document.getElementById('cart-count');
            const cartTotal = document.getElementById('cart-total');
            const checkoutBtn = document.getElementById('checkout-btn');
            const clearCartBtn = document.getElementById('clear-cart-btn');
            const closeChat = document.getElementById('close-chat');
            // History panel variables removed
            const tabs = document.querySelectorAll('.tab');
            const orderModal = document.getElementById('order-modal');
            const modalTotal = document.getElementById('modal-total');
            const cancelOrder = document.getElementById('cancel-order');
            const confirmOrder = document.getElementById('confirm-order');
            const categoryBtns = document.querySelectorAll('.category-btn');
            const menuCards = document.querySelectorAll('.menu-card');
            const successModal = document.getElementById('success-modal');
            const closeSuccessModal = document.getElementById('close-success-modal');
            const clearCartModal = document.getElementById('clear-cart-modal');
            const cancelClearCart = document.getElementById('cancel-clear-cart');
            const confirmClearCart = document.getElementById('confirm-clear-cart');

            // Add to cart functionality
            document.querySelectorAll('.add-to-cart').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    const price = parseInt(this.dataset.price);
                    const stock = parseInt(this.dataset.stock);

                    // Check if item already in cart
                    const existingItem = cart.find(item => item.id === id);
                    const currentQuantityInCart = existingItem ? existingItem.quantity : 0;

                    // Check stock availability
                    if (currentQuantityInCart >= stock) {
                        showNotification(`Stok ${name} tidak mencukupi. Tersisa ${stock} item`, 'error');
                        return;
                    }

                    if (existingItem) {
                        existingItem.quantity += 1;
                        existingItem.stock = stock; // Store stock info
                    } else {
                        cart.push({
                            id: id,
                            name: name,
                            price: price,
                            quantity: 1,
                            stock: stock
                        });
                    }

                    updateCart();
                    showNotification(`${name} ditambahkan ke keranjang`);

                    // Always open cart when item is added
                    chatContainer.classList.remove('translate-x-full');
                    chatContainer.classList.add('translate-x-0');
                });
            });

            // Update cart display
            function updateCart() {
                const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
                const totalAmount = cart.reduce((total, item) => total + (item.price * item.quantity), 0);

                // Update cart count and visibility
                cartCount.textContent = totalItems;
                if (totalItems > 0) {
                    cartCount.style.display = 'flex';
                } else {
                    cartCount.style.display = 'none';
                }

                // Update cart total
                cartTotal.textContent = parseInt(totalAmount).toLocaleString('id-ID');
                if (modalTotal) modalTotal.textContent = parseInt(totalAmount).toLocaleString('id-ID');

                // Enable/disable checkout button
                checkoutBtn.disabled = cart.length === 0;

                // Update cart items
                if (cart.length === 0) {
                    chatBody.innerHTML = `
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-shopping-cart text-3xl mb-3"></i>
                            <p class="font-medium">Keranjang masih kosong</p>
                            <p class="text-sm mt-1">Klik produk untuk menambah ke keranjang</p>
                        </div>
                    `;
                } else {
                    chatBody.innerHTML = '';
                    cart.forEach(item => {
                        const itemTotal = item.price * item.quantity;
                        const orderItem = document.createElement('div');
                        orderItem.className = 'flex justify-between items-center py-3 border-b border-gray-100 last:border-b-0';
                        orderItem.innerHTML = `
                            <div>
                                <h4 class="font-medium">${item.name}</h4>
                                <p class="text-sm text-gray-600">Rp ${parseInt(item.price).toLocaleString('id-ID')} x ${item.quantity}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="font-bold text-gray-900">Rp ${parseInt(itemTotal).toLocaleString('id-ID')}</span>
                                <div class="flex items-center space-x-1">
                                    <button class="decrease-item w-7 h-7 bg-gray-200 hover:bg-gray-300 rounded text-sm font-medium transition-colors" data-id="${item.id}">-</button>
                                    <button class="increase-item w-7 h-7 bg-gray-200 hover:bg-gray-300 rounded text-sm font-medium transition-colors" data-id="${item.id}">+</button>
                                    <button class="remove-item text-red-500 hover:text-red-700 p-1 hover:bg-red-50 rounded transition-colors" data-id="${item.id}">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                        chatBody.appendChild(orderItem);
                    });

                    // Add event listeners to cart item buttons
                    document.querySelectorAll('.decrease-item').forEach(button => {
                        button.addEventListener('click', function() {
                            const id = this.dataset.id;
                            const item = cart.find(item => item.id === id);

                            if (item.quantity > 1) {
                                item.quantity -= 1;
                            } else {
                                cart = cart.filter(item => item.id !== id);
                            }

                            updateCart();
                        });
                    });

                    document.querySelectorAll('.increase-item').forEach(button => {
                        button.addEventListener('click', function() {
                            const id = this.dataset.id;
                            const item = cart.find(item => item.id === id);
                            item.quantity += 1;
                            updateCart();
                        });
                    });

                    document.querySelectorAll('.remove-item').forEach(button => {
                        button.addEventListener('click', function() {
                            const id = this.dataset.id;
                            cart = cart.filter(item => item.id !== id);
                            updateCart();
                            showNotification('Item dihapus dari keranjang');
                        });
                    });
                }
            }

            // Toggle cart sidebar
            chatBubble.addEventListener('click', function() {
                if (chatContainer.classList.contains('translate-x-full')) {
                    chatContainer.classList.remove('translate-x-full');
                    chatContainer.classList.add('translate-x-0');
                } else {
                    chatContainer.classList.add('translate-x-full');
                    chatContainer.classList.remove('translate-x-0');
                }
            });

            // Close cart sidebar
            closeChat.addEventListener('click', function() {
                chatContainer.classList.add('translate-x-full');
                chatContainer.classList.remove('translate-x-0');
            });

            // History panel functionality removed

            // Tab functionality
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabName = this.dataset.tab;

                    // Update active tab
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');

                    // Show corresponding content
                    document.querySelectorAll('.tab-content').forEach(content => {
                        content.classList.remove('active');
                    });
                    document.getElementById(`${tabName}-tab`).classList.add('active');
                });
            });

            // Checkout button
            checkoutBtn.addEventListener('click', function() {
                const customerName = document.getElementById('cart-customer-name').value;
                if (!customerName.trim()) {
                    showNotification('Harap masukkan nama pemesan', 'error');
                    return;
                }
                
                // Update modal with customer name and total
                document.getElementById('modal-customer-name').textContent = customerName;
                const totalAmount = cart.reduce((total, item) => total + (parseInt(item.price) * item.quantity), 0);
                document.getElementById('modal-total').textContent = parseInt(totalAmount).toLocaleString('id-ID');
                
                orderModal.classList.remove('hidden');
            });
            
            // Clear cart button
            clearCartBtn.addEventListener('click', function() {
                if (cart.length === 0) {
                    showNotification('Keranjang sudah kosong', 'error');
                    return;
                }
                
                clearCartModal.classList.remove('hidden');
            });
            
            // Cancel clear cart
            cancelClearCart.addEventListener('click', function() {
                clearCartModal.classList.add('hidden');
            });
            
            // Confirm clear cart
            confirmClearCart.addEventListener('click', function() {
                cart = [];
                updateCart();
                clearCartModal.classList.add('hidden');
                showNotification('Keranjang berhasil dikosongkan');
            });

            // Cancel order
            cancelOrder.addEventListener('click', function() {
                orderModal.classList.add('hidden');
            });

            // Confirm order
            confirmOrder.addEventListener('click', function() {
                const customerName = document.getElementById('cart-customer-name').value;
                
                // Prepare order data
                const orderData = {
                    customer_name: customerName,
                    items: cart,
                    total: cart.reduce((total, item) => total + (parseInt(item.price) * item.quantity), 0)
                };

                // Send order to server
                sendOrderToServer(orderData);
            });

            // Close success modal
            closeSuccessModal.addEventListener('click', function() {
                successModal.classList.add('hidden');
            });
            
            // Close modals when clicking outside
            clearCartModal.addEventListener('click', function(e) {
                if (e.target === clearCartModal) {
                    clearCartModal.classList.add('hidden');
                }
            });

            // Category filter functionality
            categoryBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const category = this.dataset.category;

                    // Update active button
                    categoryBtns.forEach(b => b.classList.remove('active', 'bg-primary', 'text-white'));
                    this.classList.add('active', 'bg-primary', 'text-white');

                    // Filter menu items
                    menuCards.forEach(card => {
                        if (category === 'all' || card.dataset.category === category) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            });

            // Function to send order to server
            function sendOrderToServer(orderData) {
                // Add CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                fetch('{{ route("customer.order") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken || '',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(orderData)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Hide order modal
                            orderModal.classList.add('hidden');

                            // Show success modal with order details
                            const customerName = document.getElementById('cart-customer-name').value;
                            const totalAmount = cart.reduce((total, item) => total + (parseInt(item.price) * item.quantity), 0);

                            document.getElementById('success-customer-name').textContent = customerName;
                            document.getElementById('success-total').textContent = parseInt(totalAmount).toLocaleString('id-ID');

                            successModal.classList.remove('hidden');

                            // Clear cart
                            cart = [];
                            updateCart();

                            // Close cart
                            chatContainer.classList.add('translate-x-full');
                            chatContainer.classList.remove('translate-x-0');

                            // Reset form
                            document.getElementById('cart-customer-name').value = '';

                            // Re-enable checkout button for future orders
                            checkoutBtn.disabled = true;
                        } else {
                            showNotification('Gagal mengirim pesanan: ' + (data.message || 'Unknown error'), 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Terjadi kesalahan saat mengirim pesanan', 'error');
                    });
            }

            // Favorites functionality removed

            // Show notification
            function showNotification(message, type = 'success') {
                // Remove existing notification if any
                const existingNotification = document.querySelector('.fixed-notification');
                if (existingNotification) {
                    existingNotification.remove();
                }

                const notification = document.createElement('div');
                notification.className = `fixed-notification fixed top-4 left-4 px-4 py-3 rounded-lg shadow-lg z-50 transform transition-transform duration-300 ${
                    type === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'
                }`;
                notification.textContent = message;

                document.body.appendChild(notification);

                // Animate in
                setTimeout(() => {
                    notification.classList.remove('translate-y-[-100px]');
                    notification.classList.add('translate-y-0');
                }, 10);

                // Remove after 3 seconds
                setTimeout(() => {
                    notification.classList.remove('translate-y-0');
                    notification.classList.add('translate-y-[-100px]');
                    setTimeout(() => {
                        notification.remove();
                    }, 300);
                }, 3000);
            }
        });
    </script>
</body>

</html>