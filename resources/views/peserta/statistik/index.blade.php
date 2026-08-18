@extends('layouts.app')

@section('title', 'Statistik Belajar')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-display font-bold text-primary">Statistik Belajar</h1>
    <p class="text-text-secondary mt-1">Pantau riwayat pembelajaran dan nilai evaluasi Anda.</p>
</div>

<!-- Chart Nilai -->
@if(count($chartData) > 0)
<div class="bg-white rounded-xl shadow-sm border border-border p-6 mb-6">
    <h3 class="font-display font-bold text-lg mb-4 text-text-primary">Perkembangan Nilai Kuis</h3>
    <div class="h-[300px] w-full">
        <canvas id="skorChart"></canvas>
    </div>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    
    <!-- Progres Materi -->
    <div class="bg-white rounded-xl shadow-sm border border-border overflow-hidden">
        <div class="p-4 border-b border-border flex items-center justify-between bg-secondary/50">
            <h3 class="font-display font-bold text-text-primary flex items-center gap-2">
                <i data-lucide="book-open" class="w-5 h-5 text-primary"></i> Materi Dipelajari
            </h3>
        </div>
        <div class="p-4 overflow-y-auto max-h-[400px]">
            @forelse($progresMateris as $progres)
                <div class="flex items-center justify-between p-3 border-b border-border last:border-0">
                    <div>
                        <p class="font-medium text-text-primary">{{ $progres->materi->judul ?? 'Materi Tidak Ditemukan' }}</p>
                        <p class="text-xs text-text-secondary">{{ $progres->updated_at->format('d M Y, H:i') }} &bull; {{ $progres->materi->pelatihan->judul ?? 'Pelatihan' }}</p>
                    </div>
                    <div>
                        @if($progres->status === 'selesai')
                            <span class="inline-flex items-center gap-1 bg-success/10 text-success text-xs font-bold px-2.5 py-1 rounded-full">
                                <i data-lucide="check" class="w-3 h-3"></i> Selesai
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 bg-primary/10 text-primary text-xs font-bold px-2.5 py-1 rounded-full">
                                Sedang Dipelajari
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-text-secondary text-sm text-center py-4">Belum ada materi yang dipelajari.</p>
            @endforelse
        </div>
    </div>

    <!-- Riwayat Evaluasi -->
    <div class="bg-white rounded-xl shadow-sm border border-border overflow-hidden">
        <div class="p-4 border-b border-border flex items-center justify-between bg-secondary/50">
            <h3 class="font-display font-bold text-text-primary flex items-center gap-2">
                <i data-lucide="award" class="w-5 h-5 text-accent"></i> Riwayat Nilai Kuis
            </h3>
        </div>
        <div class="p-4 overflow-y-auto max-h-[400px]">
            @forelse($riwayatEvaluasi as $riwayat)
                <a href="{{ route('peserta.evaluasi.hasil', $riwayat->id) }}" class="block flex items-center justify-between p-3 border border-border rounded-lg mb-2 hover:border-primary transition-colors">
                    <div>
                        <p class="font-bold text-text-primary">{{ $riwayat->materi->judul ?? 'Kuis' }}</p>
                        <p class="text-xs text-text-secondary">{{ $riwayat->selesai_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xl font-display font-bold {{ $riwayat->skor >= 70 ? 'text-success' : 'text-danger' }}">{{ $riwayat->skor }}</p>
                        <p class="text-xs text-text-secondary">{{ $riwayat->benar }} dari {{ $riwayat->total_soal }} benar</p>
                    </div>
                </a>
            @empty
                <p class="text-text-secondary text-sm text-center py-4">Belum ada riwayat kuis.</p>
            @endforelse
        </div>
    </div>

</div>

@push('scripts')
@if(count($chartData) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('skorChart').getContext('2d');
        
        const labels = {!! json_encode($chartLabels) !!};
        const data = {!! json_encode($chartData) !!};

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Skor Kuis',
                    data: data,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: function(context) {
                        const index = context.dataIndex;
                        const value = context.dataset.data[index];
                        return value >= 70 ? '#10b981' : '#ef4444'; // Green if pass, red if fail
                    },
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    fill: true,
                    tension: 0.3
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
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'Skor: ' + context.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: { stepSize: 20 },
                        grid: { borderDash: [4, 4], color: '#e2e8f0' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                }
            }
        });
    });
</script>
@endif
@endpush

@endsection
