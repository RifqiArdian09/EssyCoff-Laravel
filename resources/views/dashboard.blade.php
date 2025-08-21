<x-layouts.app :title="__('Dashboard')">
    <div class="flex flex-col gap-8">

        <!-- Ringkasan Hari Ini -->
        <div>
            <h2 class="text-xl font-semibold text-gray-200 mb-4">📊 Ringkasan Hari Ini</h2>
            <div class="grid gap-6 md:grid-cols-3">
                <!-- Total Transaksi -->
                <x-card class="p-6 bg-gray-800 border border-gray-700 rounded-2xl shadow-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400">Total Transaksi</p>
                            <p class="text-3xl font-bold text-blue-400">{{ $totalOrdersToday }}</p>
                        </div>
                        <div class="p-3 rounded-xl bg-blue-500/20 text-blue-400 text-2xl">
                            🛒
                        </div>
                    </div>
                </x-card>

                <!-- Total Omzet -->
                <x-card class="p-6 bg-gray-800 border border-gray-700 rounded-2xl shadow-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400">Total Omzet</p>
                            <p class="text-3xl font-bold text-green-400">
                                Rp {{ number_format($totalRevenueToday,0,',','.') }}
                            </p>
                        </div>
                        <div class="p-3 rounded-xl bg-green-500/20 text-green-400 text-2xl">
                            💰
                        </div>
                    </div>
                </x-card>

                <!-- Pesanan -->
                <x-card class="p-6 bg-gray-800 border border-gray-700 rounded-2xl shadow-md">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm text-gray-400">Status Pesanan</p>
                            <div class="mt-3 space-y-1 text-sm">
                                <p class="text-yellow-400 font-semibold">⏳ Pending: {{ $statusCounts['pending'] }}</p>
                                <p class="text-blue-400 font-semibold">🔄 Diproses: {{ $statusCounts['diproses'] }}</p>
                                <p class="text-green-400 font-semibold">✅ Selesai: {{ $statusCounts['selesai'] }}</p>
                            </div>
                        </div>
                        <div class="p-3 rounded-xl bg-gray-500/20 text-gray-400 text-2xl">
                            📋
                        </div>
                    </div>
                </x-card>
            </div>
        </div>

        <!-- Grafik Omzet 7 Hari -->
        <x-card class="p-6 bg-gray-800 border border-gray-700 rounded-2xl shadow-md">
            <h3 class="text-lg font-semibold text-gray-200 mb-4 flex items-center gap-2">
                📈 Grafik Omzet 7 Hari Terakhir
            </h3>
            <canvas id="chartOmzet" height="100"></canvas>
        </x-card>

        <!-- Produk Terlaris -->
        <x-card class="p-6 bg-gray-800 border border-gray-700 rounded-2xl shadow-md">
            <h3 class="text-lg font-semibold text-gray-200 mb-4">🔥 Produk Terlaris</h3>
            <ul class="divide-y divide-gray-700">
                @forelse($topProducts as $item)
                    <li class="flex items-center justify-between py-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <img src="{{ $item->product?->image_url ?? 'https://via.placeholder.com/80?text=No+Image' }}" 
                                 class="w-12 h-12 rounded-lg object-cover border border-gray-600">
                            <span class="truncate text-gray-300">{{ $item->product?->name ?? 'Produk Dihapus' }}</span>
                        </div>
                        <span class="font-semibold text-blue-400 shrink-0">{{ $item->total_sold }} terjual</span>
                    </li>
                @empty
                    <li class="text-gray-500 text-center py-2">Belum ada data.</li>
                @endforelse
            </ul>
        </x-card>

        <!-- Quick Action -->
        <div class="grid gap-6 md:grid-cols-2">
            <a href="{{ route('pos.cashier') }}" class="p-5 bg-blue-600 text-white rounded-2xl shadow hover:bg-blue-700 font-medium flex items-center justify-between">
                <div class="flex items-center gap-3">
                    ➕ <span>Buat Transaksi Baru</span>
                </div>
                ➡️
            </a>
            <a href="{{ route('pos.history') }}" class="p-5 bg-green-600 text-white rounded-2xl shadow hover:bg-green-700 font-medium flex items-center justify-between">
                <div class="flex items-center gap-3">
                    ⏰ <span>Lihat Riwayat Transaksi</span>
                </div>
                ➡️
            </a>
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
