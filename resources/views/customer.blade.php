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
                        primary: '#6f4e37',
                        secondary: '#c0a080',
                        accent: '#e7c9a9',
                        dark: '#4a3c2d',
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f7f5;
        }
        
        .menu-card {
            transition: all 0.3s ease;
        }
        
        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .category-btn.active {
            background-color: #6f4e37;
            color: white;
        }
        
        /* Chat bubble styles */
        .chat-bubble {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: #6f4e37;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            z-index: 100;
            transition: all 0.3s ease;
        }
        
        .chat-bubble:hover {
            transform: scale(1.05);
        }
        
        .chat-bubble .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #ef4444;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }
        
        .chat-container {
            position: fixed;
            bottom: 90px;
            right: 20px;
            width: 350px;
            height: 450px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            z-index: 99;
            overflow: hidden;
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(20px);
            pointer-events: none;
        }
        
        .chat-container.open {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }
        
        .chat-header {
            background-color: #6f4e37;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .chat-body {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
        }
        
        .chat-footer {
            padding: 15px;
            border-top: 1px solid #e5e5e5;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .history-panel {
            position: fixed;
            top: 0;
            right: 0;
            width: 350px;
            height: 100%;
            background-color: white;
            box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
            z-index: 101;
            overflow-y: auto;
            padding: 20px;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }
        
        .history-panel.open {
            transform: translateX(0);
        }
        
        .tab-container {
            display: flex;
            border-bottom: 1px solid #e5e5e5;
            margin-bottom: 20px;
        }
        
        .tab {
            padding: 10px 15px;
            cursor: pointer;
            border-bottom: 2px solid transparent;
        }
        
        .tab.active {
            border-bottom-color: #6f4e37;
            color: #6f4e37;
            font-weight: 500;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .history-item {
            padding: 15px;
            border-radius: 8px;
            background-color: #f8f8f8;
            margin-bottom: 10px;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50">
    <!-- Header Minimal -->
    <header class="bg-primary text-white shadow-md">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="w-16 h-16 flex items-center justify-center rounded-full bg-white shadow-md">
                    <img src="{{ asset('images/logo.png') }}" alt="EssyCoff Logo" class="w-16 h-16 animate-pulse-slow">
                </div>
                <h1 class="text-2xl font-bold">EssyCoff</h1>
            </div>
            <button id="history-btn" class="px-4 py-2 bg-white bg-opacity-20 rounded-lg hover:bg-opacity-30 transition">
                <i class="fas fa-history mr-2"></i>Riwayat
            </button>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <div class="mb-8 text-center">
            <h2 class="text-3xl font-bold text-gray-800">Selamat Datang di EssyCoff</h2>
            <p class="text-gray-600 mt-2">Pesan makanan dan minuman favorit Anda dengan mudah</p>
        </div>

        <!-- Category Filter -->
        <div class="flex flex-wrap gap-2 mb-6 justify-center">
            <a href="{{ route('customer', ['search' => $search ?? '', 'category' => 'all']) }}" 
               class="category-btn px-4 py-2 rounded-full text-sm font-medium transition {{ ($category ?? 'all') === 'all' ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Semua
            </a>
            @foreach($categories as $cat)
            <a href="{{ route('customer', ['search' => $search ?? '', 'category' => strtolower($cat->name)]) }}" 
               class="category-btn px-4 py-2 rounded-full text-sm font-medium transition {{ ($category ?? 'all') === strtolower($cat->name) ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                {{ $cat->name }}
            </a>
            @endforeach
        </div>
        
        <!-- Search Box -->
        <form method="GET" action="{{ route('customer') }}" class="relative mb-8 max-w-md mx-auto">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari menu..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            <input type="hidden" name="category" value="{{ $category ?? 'all' }}">
            <button type="submit" class="absolute right-3 top-3.5 text-gray-400 hover:text-primary">
                <i class="fas fa-search"></i>
            </button>
        </form>
        
        <!-- Menu Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="menu-container">
            @forelse($products as $product)
            <div class="menu-card bg-white border border-gray-200 rounded-xl overflow-hidden" data-category="{{ strtolower($product->category->name ?? 'uncategorized') }}">
                <div class="relative">
                    @if($product->image_url)
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-image text-gray-400 text-3xl"></i>
                        </div>
                    @endif
                    <span class="absolute top-2 right-2 bg-white rounded-full px-2 py-1 text-xs font-semibold text-gray-700">Stok: {{ $product->stock }}</span>
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-gray-800 text-lg">{{ $product->name }}</h3>
                    <p class="text-gray-600 text-sm mt-1">{{ $product->category->name ?? 'Kategori tidak tersedia' }}</p>
                    <div class="flex justify-between items-center mt-3">
                        <span class="text-primary font-bold text-xl">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        <button class="add-to-cart bg-primary text-white px-4 py-2 rounded-lg text-sm hover:bg-dark transition" 
                                data-id="{{ $product->id }}" 
                                data-name="{{ $product->name }}" 
                                data-price="{{ $product->price }}"
                                @if($product->stock <= 0) disabled @endif>
                            <i class="fas fa-plus mr-1"></i> 
                            @if($product->stock <= 0) Habis @else Tambah @endif
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <i class="fas fa-coffee text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">Belum Ada Produk</h3>
                <p class="text-gray-500">Produk akan segera tersedia</p>
            </div>
            @endforelse
        </div>
    </main>

    <!-- Chat Bubble -->
    <div class="chat-bubble" id="chat-bubble">
        <i class="fas fa-shopping-cart text-xl"></i>
        <div class="badge" id="cart-count">0</div>
    </div>

    <!-- Chat Container -->
    <div class="chat-container" id="chat-container">
        <div class="chat-header">
            <h3 class="font-bold">Keranjang Pesanan</h3>
            <button id="close-chat" class="text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="chat-body" id="chat-body">
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-shopping-cart text-3xl mb-3"></i>
                <p class="font-medium">Keranjang masih kosong</p>
                <p class="text-sm mt-1">Klik produk untuk menambah ke keranjang</p>
            </div>
        </div>
        <div class="chat-footer">
            <div class="flex justify-between mb-3">
                <span class="font-medium">Total:</span>
                <span class="font-bold text-primary">Rp <span id="cart-total">0</span></span>
            </div>
            <button id="checkout-btn" class="w-full bg-primary hover:bg-dark text-white font-medium py-2 px-4 rounded-lg transition" disabled>
                Pesan Sekarang
            </button>
        </div>
    </div>

    <!-- History Panel -->
    <div class="history-panel" id="history-panel">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold">Riwayat Pesanan</h2>
            <button id="close-history" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="tab-container">
            <div class="tab active" data-tab="favorites">Favorit</div>
        </div>
        
        <div class="tab-content active" id="favorites-tab">
            <div class="mb-4">
                <button id="load-favorites" class="w-full bg-primary text-white px-4 py-2 rounded-lg hover:bg-dark transition">
                    <i class="fas fa-heart mr-2"></i>Lihat Item Favorit
                </button>
            </div>
            <div id="favorites-content">
                <div class="text-center py-10 text-gray-500">
                    <i class="fas fa-heart text-3xl mb-3"></i>
                    <p class="font-medium">Klik tombol untuk melihat favorit</p>
                    <p class="text-sm mt-1">Favorit berdasarkan semua riwayat pesanan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Modal -->
    <div id="order-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-2xl p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Lengkapi Pesanan</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pemesan</label>
                    <input type="text" id="customer-name" placeholder="Masukkan nama Anda" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                
                <div class="bg-gray-100 p-4 rounded-lg">
                    <div class="flex justify-between">
                        <span class="font-medium">Total Pesanan:</span>
                        <span class="font-bold text-primary">Rp <span id="modal-total">0</span></span>
                    </div>
                </div>
            </div>
            
            <div class="flex space-x-3 mt-6">
                <button id="cancel-order" class="flex-1 bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button id="confirm-order" class="flex-1 bg-primary hover:bg-dark text-white font-medium py-2 px-4 rounded-lg transition">
                    Konfirmasi Pesanan
                </button>
            </div>
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
            const closeChat = document.getElementById('close-chat');
            const historyBtn = document.getElementById('history-btn');
            const historyPanel = document.getElementById('history-panel');
            const closeHistory = document.getElementById('close-history');
            const tabs = document.querySelectorAll('.tab');
            const orderModal = document.getElementById('order-modal');
            const modalTotal = document.getElementById('modal-total');
            const cancelOrder = document.getElementById('cancel-order');
            const confirmOrder = document.getElementById('confirm-order');
            const categoryBtns = document.querySelectorAll('.category-btn');
            const menuCards = document.querySelectorAll('.menu-card');
            
            // Add to cart functionality
            document.querySelectorAll('.add-to-cart').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    const price = parseInt(this.dataset.price);
                    
                    // Check if item already in cart
                    const existingItem = cart.find(item => item.id === id);
                    
                    if (existingItem) {
                        existingItem.quantity += 1;
                    } else {
                        cart.push({
                            id: id,
                            name: name,
                            price: price,
                            quantity: 1
                        });
                    }
                    
                    updateCart();
                    showNotification(`${name} ditambahkan ke keranjang`);
                    
                    // Open chat if closed
                    if (!chatContainer.classList.contains('open')) {
                        chatContainer.classList.add('open');
                    }
                });
            });
            
            // Update cart display
            function updateCart() {
                const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
                const totalAmount = cart.reduce((total, item) => total + (item.price * item.quantity), 0);
                
                // Update cart count
                cartCount.textContent = totalItems;
                
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
                        orderItem.className = 'order-item';
                        orderItem.innerHTML = `
                            <div>
                                <h4 class="font-medium">${item.name}</h4>
                                <p class="text-sm text-gray-600">Rp ${parseInt(item.price).toLocaleString('id-ID')} x ${item.quantity}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="font-bold text-primary">Rp ${parseInt(itemTotal).toLocaleString('id-ID')}</span>
                                <div class="flex items-center space-x-1">
                                    <button class="decrease-item w-6 h-6 bg-gray-200 rounded text-sm hover:bg-gray-300" data-id="${item.id}">-</button>
                                    <button class="increase-item w-6 h-6 bg-gray-200 rounded text-sm hover:bg-gray-300" data-id="${item.id}">+</button>
                                    <button class="remove-item text-red-500 hover:text-red-700" data-id="${item.id}">
                                        <i class="fas fa-trash"></i>
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
            
            // Toggle chat container
            chatBubble.addEventListener('click', function() {
                chatContainer.classList.toggle('open');
            });
            
            // Close chat container
            closeChat.addEventListener('click', function() {
                chatContainer.classList.remove('open');
            });
            
            // Toggle history panel
            historyBtn.addEventListener('click', function() {
                historyPanel.classList.toggle('open');
            });
            
            // Close history panel
            closeHistory.addEventListener('click', function() {
                historyPanel.classList.remove('open');
            });
            
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
                orderModal.classList.remove('hidden');
            });
            
            // Cancel order
            cancelOrder.addEventListener('click', function() {
                orderModal.classList.add('hidden');
            });
            
            // Confirm order
            confirmOrder.addEventListener('click', function() {
                const customerName = document.getElementById('customer-name').value;
                if (!customerName) {
                    showNotification('Harap masukkan nama pemesan', 'error');
                    return;
                }
                
                // Prepare order data
                const orderData = {
                    customer_name: customerName,
                    items: cart,
                    total: cart.reduce((total, item) => total + (parseInt(item.price) * item.quantity), 0)
                };
                
                // Send order to server
                sendOrderToServer(orderData);
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
                        orderModal.classList.add('hidden');
                        showNotification('Pesanan berhasil dikirim! Status: Pending Payment', 'success');
                        
                        // Clear cart
                        cart = [];
                        updateCart();
                        
                        // Close chat
                        chatContainer.classList.remove('open');
                        
                        // Reset form
                        document.getElementById('customer-name').value = '';
                        
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
            
            // Load favorites functionality
            document.getElementById('load-favorites').addEventListener('click', function() {
                loadFavorites();
            });
            
            // Load favorites from server
            function loadFavorites() {
                const favoritesContent = document.getElementById('favorites-content');
                favoritesContent.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i><p class="mt-2 text-gray-500">Memuat favorit...</p></div>';
                
                fetch(`{{ route('customer.history') }}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.favorites.length > 0) {
                        displayFavorites(data.favorites);
                    } else {
                        favoritesContent.innerHTML = `
                            <div class="text-center py-10 text-gray-500">
                                <i class="fas fa-heart-broken text-3xl mb-3"></i>
                                <p class="font-medium">Belum ada item favorit</p>
                                <p class="text-sm mt-1">Lakukan pemesanan untuk membuat daftar favorit</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    favoritesContent.innerHTML = `
                        <div class="text-center py-10 text-red-500">
                            <i class="fas fa-exclamation-triangle text-3xl mb-3"></i>
                            <p class="font-medium">Gagal memuat favorit</p>
                            <p class="text-sm mt-1">Silakan coba lagi</p>
                        </div>
                    `;
                });
            }
            
            // Display favorites
            function displayFavorites(favorites) {
                const favoritesContent = document.getElementById('favorites-content');
                favoritesContent.innerHTML = '';
                
                favorites.forEach((item, index) => {
                    const favoriteItem = document.createElement('div');
                    favoriteItem.className = 'bg-gray-50 rounded-lg p-4 mb-3';
                    favoriteItem.innerHTML = `
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-800">${item.product.name}</h4>
                                <p class="text-sm text-gray-600">Rp ${parseInt(item.product.price).toLocaleString('id-ID')}</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-heart text-red-400"></i> 
                                    Dipesan ${item.total_ordered}x dalam ${item.order_count} pesanan
                                </p>
                            </div>
                            <button class="add-favorite-to-cart bg-primary text-white px-3 py-2 rounded-lg text-sm hover:bg-dark transition" 
                                    data-id="${item.product.id}" 
                                    data-name="${item.product.name}" 
                                    data-price="${item.product.price}"
                                    ${item.product.stock <= 0 ? 'disabled' : ''}>
                                <i class="fas fa-plus mr-1"></i> 
                                ${item.product.stock <= 0 ? 'Habis' : 'Tambah'}
                            </button>
                        </div>
                    `;
                    favoritesContent.appendChild(favoriteItem);
                });
                
                // Add event listeners to favorite buttons
                document.querySelectorAll('.add-favorite-to-cart').forEach(button => {
                    button.addEventListener('click', function() {
                        const id = this.dataset.id;
                        const name = this.dataset.name;
                        const price = parseInt(this.dataset.price);
                        
                        // Check if item already in cart
                        const existingItem = cart.find(item => item.id === id);
                        
                        if (existingItem) {
                            existingItem.quantity += 1;
                        } else {
                            cart.push({
                                id: id,
                                name: name,
                                price: price,
                                quantity: 1
                            });
                        }
                        
                        updateCart();
                        showNotification(`${name} ditambahkan ke keranjang dari favorit`);
                        
                        // Open chat if closed
                        if (!chatContainer.classList.contains('open')) {
                            chatContainer.classList.add('open');
                        }
                    });
                });
            }
            
            // Show notification
            function showNotification(message, type = 'success') {
                // Remove existing notification if any
                const existingNotification = document.querySelector('.fixed-notification');
                if (existingNotification) {
                    existingNotification.remove();
                }
                
                const notification = document.createElement('div');
                notification.className = `fixed-notification fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg z-50 transform transition-transform duration-300 ${
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