@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-display font-bold text-primary">Ringkasan Sistem</h1>
    <p class="text-text-secondary mt-1">Pantau aktivitas pengguna dan statistik LMS Pas Sulsel.</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    
    <!-- Stat 1 -->
    <div class="bg-white rounded-xl shadow-sm border border-border p-5 hover:border-primary transition-colors">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center text-primary">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
            <p class="text-sm font-medium text-text-secondary">Total Peserta Aktif</p>
        </div>
        <h3 class="text-3xl font-display font-bold text-text-primary ml-13">{{ $totalPengguna }}</h3>
    </div>

    <!-- Stat 2 -->
    <div class="bg-white rounded-xl shadow-sm border border-border p-5 hover:border-warning transition-colors">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-warning/10 rounded-lg flex items-center justify-center text-warning">
                <i data-lucide="activity" class="w-5 h-5"></i>
            </div>
            <p class="text-sm font-medium text-text-secondary">Sesi Evaluasi Aktif</p>
        </div>
        <h3 class="text-3xl font-display font-bold text-text-primary ml-13">{{ $penggunaAktif }}</h3>
    </div>

    <!-- Stat 3 -->
    <div class="bg-white rounded-xl shadow-sm border border-border p-5 hover:border-danger transition-colors relative">
        @if($pendingRequests > 0)
            <div class="absolute top-4 right-4 w-3 h-3 bg-danger rounded-full animate-pulse"></div>
        @endif
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-danger/10 rounded-lg flex items-center justify-center text-danger">
                <i data-lucide="user-plus" class="w-5 h-5"></i>
            </div>
            <p class="text-sm font-medium text-text-secondary">Pendaftaran Pending</p>
        </div>
        <h3 class="text-3xl font-display font-bold text-text-primary ml-13">{{ $pendingRequests }}</h3>
    </div>

    <!-- Stat 4 -->
    <div class="bg-white rounded-xl shadow-sm border border-border p-5 hover:border-success transition-colors">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-success/10 rounded-lg flex items-center justify-center text-success">
                <i data-lucide="bar-chart" class="w-5 h-5"></i>
            </div>
            <p class="text-sm font-medium text-text-secondary">Skor Max / Min</p>
        </div>
        <div class="flex items-baseline gap-2 ml-13">
            <h3 class="text-3xl font-display font-bold text-success">{{ $highestScore }}</h3>
            <span class="text-xl font-bold text-text-secondary">/</span>
            <h3 class="text-3xl font-display font-bold text-danger">{{ $lowestScore }}</h3>
        </div>
    </div>

</div>

<!-- Charts & Activities -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Chart -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-border p-6">
        <h3 class="font-display font-bold text-lg mb-4 text-text-primary">Tren Pendaftaran Peserta (7 Hari Terakhir)</h3>
        <div class="h-[300px] w-full">
            <canvas id="registrationChart"></canvas>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="bg-white rounded-xl shadow-sm border border-border p-6 flex flex-col">
        <h3 class="font-display font-bold text-lg mb-4 flex items-center gap-2">
            <i data-lucide="clock" class="w-5 h-5 text-primary"></i> Evaluasi Terbaru
        </h3>
        
        <div class="flex-1 flex flex-col gap-4 overflow-y-auto pr-2">
            @forelse($recentActivities as $activity)
                <div class="flex items-start gap-3 p-3 rounded-lg border border-border/50 bg-secondary/30 hover:bg-secondary/50 transition-colors">
                    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0 mt-0.5">
                        <i data-lucide="check-circle" class="w-4 h-4 text-primary"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-text-primary">{{ $activity->user->nama ?? 'Unknown' }}</p>
                        <p class="text-xs text-text-secondary mt-0.5">
                            Menyelesaikan kuis <span class="font-medium text-text-primary">{{ $activity->materi->judul ?? 'Unknown' }}</span>
                        </p>
                        <div class="flex items-center gap-2 mt-1.5">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $activity->skor >= 70 ? 'bg-success/10 text-success' : 'bg-warning/10 text-warning' }}">
                                Skor: {{ $activity->skor }}
                            </span>
                            <span class="text-[10px] text-text-secondary">{{ $activity->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center h-full opacity-50 py-8">
                    <i data-lucide="inbox" class="w-10 h-10 mb-2"></i>
                    <p class="text-sm">Belum ada evaluasi diselesaikan.</p>
                </div>
            @endforelse
        </div>
        
        <div class="mt-4 pt-4 border-t border-border">
            <a href="{{ route('admin.statistik.index') }}" class="text-sm font-bold text-primary hover:underline flex items-center justify-center gap-1">
                Lihat Semua Statistik <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('registrationChart').getContext('2d');
        
        // Reverse arrays because we built them from newest to oldest (6 days ago -> today)
        const labels = {!! json_encode(array_reverse($chartDates)) !!};
        const data = {!! json_encode(array_reverse($chartData)) !!};

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendaftaran Baru',
                    data: data,
                    borderColor: '#2563eb', // primary color
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#2563eb',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        titleFont: { size: 13, family: "'Inter', sans-serif" },
                        bodyFont: { size: 14, family: "'Inter', sans-serif", weight: 'bold' },
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0 },
                        grid: { borderDash: [4, 4], color: '#e2e8f0' }
                    },
                    x: {
                        grid: { display: false }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
            }
        });
    });
</script>
@endpush

@endsection
