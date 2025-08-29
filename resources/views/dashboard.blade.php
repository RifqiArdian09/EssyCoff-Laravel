<x-layouts.app :title="('Dashboard')">
    <div class="p-6 space-y-8 bg-gray-50 dark:bg-zinc-800 min-h-screen text-gray-900 dark:text-white">

        <!-- Ringkasan Hari Ini -->
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Ringkasan Hari Ini
            </h2>
            <div class="grid gap-6 md:grid-cols-2">
                <!-- Total Transaksi -->
                <div class="p-6 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-2xl shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-zinc-300">Total Transaksi</p>
                            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $totalOrdersToday }}</p>
                        </div>
                        <div class="p-3 rounded-xl bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0H17M9 19.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM20 19.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Pendapatan -->
                <div class="p-6 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-2xl shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-zinc-300">Total Pendapatan</p>
                            <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">
                                Rp {{ number_format($totalRevenueToday,0,',','.') }}
                            </p>
                        </div>
                        <div class="p-3 rounded-xl bg-emerald-100 dark:bg-green-500/20 text-emerald-600 dark:text-emerald-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik Omzet 7 Hari -->
        <div class="p-6 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-2xl shadow-lg">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Grafik Omzet 7 Hari Terakhir
            </h3>
            <canvas id="chartOmzet" height="100"></canvas>
        </div>

        <!-- Produk Terlaris -->
        <div class="p-6 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-2xl shadow-lg">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"></path>
                </svg>
                Produk Terlaris
            </h3>
            <ul class="divide-y divide-gray-200 dark:divide-zinc-700">
                @forelse($topProducts as $item)
                    <li class="flex items-center justify-between py-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <img src="{{ $item->product?->image_url ?? 'https://via.placeholder.com/80?text=No+Image' }}" 
                                 class="w-12 h-12 rounded-lg object-cover border border-gray-300 dark:border-gray-600">
                            <span class="truncate text-gray-700 dark:text-zinc-300">{{ $item->product?->name ?? 'Produk Dihapus' }}</span>
                        </div>
                        <span class="font-semibold text-blue-600 dark:text-blue-400 shrink-0">{{ $item->total_sold }} terjual</span>
                    </li>
                @empty
                    <li class="text-gray-500 dark:text-zinc-500 text-center py-2">Belum ada data.</li>
                @endforelse
            </ul>
        </div>

        <!-- Quick Action -->
        <div class="grid gap-6 md:grid-cols-2">
            <flux:button as="a" href="{{ route('pos.cashier') }}" variant="primary" class="w-full p-5 rounded-2xl shadow-lg font-medium flex items-center justify-between transition">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>Buat Transaksi Baru</span>
                </div>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </flux:button>

            <flux:button as="a" href="{{ route('pos.history') }}" variant="primary" class="w-full p-5 rounded-2xl shadow-lg font-medium flex items-center justify-between transition">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Lihat Riwayat Transaksi</span>
            </div>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </flux:button>

        </div>

    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('chartOmzet').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($last7Days->pluck('date')) !!},
                datasets: [{
                    label: 'Omzet',
                    data: {!! json_encode($last7Days->pluck('total')) !!},
                    borderColor: '#60A5FA',
                    backgroundColor: 'rgba(96, 165, 250, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { labels: { color: '#D1D5DB' } }
                },
                scales: {
                    x: {
                        ticks: { color: '#9CA3AF' },
                        grid: { color: 'rgba(75, 85, 99, 0.2)' }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#9CA3AF',
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString();
                            }
                        },
                        grid: { color: 'rgba(75, 85, 99, 0.2)' }
                    }
                }
            }
        });
    </script>
</x-layouts.app>