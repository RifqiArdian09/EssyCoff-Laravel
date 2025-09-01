<x-layouts.app :title="__('Dashboard')">
    <section class="container mx-auto py-6 px-4">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
        <div>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white">
                        Dashboard
                    </h1>
                    <p class="text-gray-600 dark:text-zinc-300 mt-1">
                        Selamat datang kembali {{ Auth::user()->name }} !
                    </p> 
                </div>
            
        </div>

        <!-- Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">

            <!-- Total Pendapatan -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-gray-200 dark:border-zinc-700/50 p-5 transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
                <div class="flex items-start justify-between">
                    <div>
                        <flux:text class="text-sm text-gray-600 dark:text-zinc-400">Total Pendapatan</flux:text>
                        <flux:heading size="xl" class="mb-1">
                            Rp {{ number_format($totalRevenueToday ?? 0, 0, ',', '.') }}
                        </flux:heading>
                        <div class="flex items-center gap-2">
                            @if($revenueGrowth >= 0)
                                <flux:icon.arrow-trending-up variant="micro" class="text-green-600 dark:text-green-500" />
                                <span class="text-sm text-green-600 dark:text-green-500">+{{ number_format($revenueGrowth, 1) }}%</span>
                            @else
                                <flux:icon.arrow-trending-down variant="micro" class="text-red-600 dark:text-red-500" />
                                <span class="text-sm text-red-600 dark:text-red-500">{{ number_format($revenueGrowth, 1) }}%</span>
                            @endif
                        </div>
                    </div>
                    <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-3">
                        <x-heroicon-o-currency-dollar class="w-6 h-6 text-zinc-600 dark:text-zinc-400" />
                    </div>
                </div>
            </div>

            <!-- Total Transaksi -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-gray-200 dark:border-zinc-700/50 p-5 transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
                <div class="flex items-start justify-between">
                    <div>
                        <flux:text class="text-sm text-gray-600 dark:text-zinc-400">Total Transaksi</flux:text>
                        <flux:heading size="xl" class="mb-1">
                            {{ $totalOrdersToday ?? 0 }}
                        </flux:heading>
                        <div class="flex items-center gap-2">
                            @if($ordersGrowth >= 0)
                                <flux:icon.arrow-trending-up variant="micro" class="text-green-600 dark:text-green-500" />
                                <span class="text-sm text-green-600 dark:text-green-500">+{{ number_format($ordersGrowth, 1) }}%</span>
                            @else
                                <flux:icon.arrow-trending-down variant="micro" class="text-red-600 dark:text-red-500" />
                                <span class="text-sm text-red-600 dark:text-red-500">{{ number_format($ordersGrowth, 1) }}%</span>
                            @endif
                        </div>
                    </div>
                    <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-3">
                        <x-heroicon-o-shopping-cart class="w-6 h-6 text-zinc-600 dark:text-zinc-400" />
                    </div>
                </div>
            </div>

            <!-- Produk Terjual -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-gray-200 dark:border-zinc-700/50 p-5 transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
                <div class="flex items-start justify-between">
                    <div>
                        <flux:text class="text-sm text-gray-600 dark:text-zinc-400">Produk Terjual</flux:text>
                        <flux:heading size="xl" class="mb-1">
                            {{ $totalProductsSold ?? 0 }}
                        </flux:heading>
                        <div class="flex items-center gap-2">
                            @if($productsGrowth >= 0)
                                <flux:icon.arrow-trending-up variant="micro" class="text-green-600 dark:text-green-500" />
                                <span class="text-sm text-green-600 dark:text-green-500">+{{ number_format($productsGrowth, 1) }}%</span>
                            @else
                                <flux:icon.arrow-trending-down variant="micro" class="text-red-600 dark:text-red-500" />
                                <span class="text-sm text-red-600 dark:text-red-500">{{ number_format($productsGrowth, 1) }}%</span>
                            @endif
                        </div>
                    </div>
                    <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-3">
                        <x-heroicon-o-cube class="w-6 h-6 text-zinc-600 dark:text-zinc-400" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Produk Terlaris & Chart Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-8">
            <!-- Produk Terlaris - Smaller -->
            <div class="lg:col-span-2 bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-gray-200 dark:border-zinc-700/50 p-5 transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 rounded-lg bg-zinc-100 dark:bg-zinc-800/50 text-zinc-600 dark:text-zinc-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"></path>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Produk Terlaris Bulan Ini</h3>
                </div>
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    @forelse($topProducts as $index => $item)
                    <div class="flex items-center gap-3 p-2.5 rounded-lg bg-gray-50 dark:bg-zinc-800/50 hover:bg-gray-100 dark:hover:bg-zinc-800 transition-colors">
                        <div class="flex-shrink-0 relative">
                            <img
                                src="{{ $item->product?->image_url ?? 'https://via.placeholder.com/80?text=No+Image' }}"
                                class="w-8 h-8 rounded-md object-cover border-2 border-white dark:border-zinc-700 shadow-sm"
                                alt="{{ $item->product?->name ?? 'No Image' }}">
                            <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
                                {{ $index + 1 }}
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 dark:text-white truncate text-sm">
                                {{ $item->product?->name ?? 'Produk Dihapus' }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-zinc-400">
                                {{ $item->total_sold }} terjual
                            </p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6">
                        <div class="w-10 h-10 mx-auto mb-3 rounded-full bg-gray-100 dark:bg-zinc-800 flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-zinc-500">Belum ada data penjualan</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Grafik Omzet -->
            <div class="lg:col-span-3 bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-gray-200 dark:border-zinc-700/50 p-5 transition-all duration-300 hover:shadow-md">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pendapatan Bulan Ini</h3>
                <div class="h-64">
                    <canvas id="chartPendapatan" class="w-full h-full"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-gray-200 dark:border-zinc-700/50 p-5">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Transaksi Terakhir</h3>
                <flux:button
                    href="{{ route('pos.history') }}"
                    variant="outline"
                    icon:trailing="arrow-up-right"
                    class="text-gray-700 dark:text-zinc-300 hover:bg-gray-50 dark:hover:bg-zinc-700/50">
                    View All
                </flux:button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                    <thead class="bg-gray-50 dark:bg-zinc-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase tracking-wider">No. Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-zinc-300 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                        @forelse($recentOrders as $order)
                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700/50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $order->no_order }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400">
                                {{ $order->customer_name ?? 'Umum' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400">
                                {{ $order->created_at->format('d M, H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $order->status === 'paid'
                                            ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
                                            : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' }}">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <flux:button
                                    variant="primary"
                                    size="sm"
                                    icon="document-text"
                                    href="{{ route('pos.detail', $order) }}">
                                    Detail
                                </flux:button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-zinc-400">
                                Belum ada transaksi.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('livewire:init', () => {
        // Inisialisasi saat pertama kali
        initChart();

        // Jika ada update dari Livewire (misalnya navigasi internal)
        document.addEventListener('livewire:dom:updated', initChart);
    });

    function initChart() {
        const chartEl = document.getElementById('chartPendapatan');
        if (!chartEl) return;

        const ctx = chartEl.getContext('2d');

        // Cegah duplikasi chart
        if (chartEl.chart) {
            chartEl.chart.destroy();
        }

        const labels = @json($currentMonthDays->pluck('date'));
        const data = @json($currentMonthDays->pluck('total'));

        chartEl.chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan Harian (Rp)',
                    data: data,
                    borderColor: 'Green',
                    backgroundColor: 'rgba(96, 165, 250, 0.2)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: '#4B5563',
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            color: '#6B7280'
                        },
                        grid: {
                            color: 'rgba(75, 85, 99, 0.1)'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#6B7280',
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString();
                            }
                        },
                        grid: {
                            color: 'rgba(75, 85, 99, 0.1)'
                        }
                    }
                }
            }
        });
    }
</script>
</x-layouts.app>