@extends('layouts.app')
@section('title', 'Statistik Belajar - STRAPSUSPAS')

@section('content')
<div class="space-y-6 py-2">

    {{-- Page Header --}}
    <div>
        <h1 class="text-2xl font-extrabold text-[var(--text-primary)]">Statistik & Riwayat Belajar</h1>
        <p class="text-xs text-[var(--text-secondary)] mt-1">Pantau perkembangan pemahaman hukum disiplin, capaian materi, dan histori skor evaluasi Anda.</p>
    </div>

    {{-- Chart Nilai --}}
    @if(count($chartData) > 0)
    <div class="rounded-2xl border border-[var(--border)] bg-[var(--card)] p-6 shadow-xs">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-sm text-[var(--text-primary)] flex items-center gap-2">
                <i data-lucide="trending-up" class="w-4 h-4 text-indigo-600"></i> Tren Nilai Evaluasi Kuis
            </h3>
            <span class="text-xs text-[var(--text-secondary)]">Batas Minimum: 70</span>
        </div>
        <div class="h-[280px] w-full">
            <canvas id="skorChart"></canvas>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Progres Materi Dipelajari --}}
        <div class="rounded-2xl border border-[var(--border)] bg-[var(--card)] overflow-hidden shadow-xs">
            <div class="p-4 border-b border-[var(--border)] bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between">
                <h3 class="font-bold text-sm text-[var(--text-primary)] flex items-center gap-2">
                    <i data-lucide="book-open" class="w-4 h-4 text-indigo-600"></i> Modul Materi Dipelajari
                </h3>
                <span class="text-xs text-[var(--text-secondary)]">{{ $progresMateris->count() }} Modul</span>
            </div>
            <div class="p-4 overflow-y-auto max-h-[420px] divide-y divide-[var(--border)]">
                @forelse($progresMateris as $progres)
                    <div class="py-3.5 first:pt-0 last:pb-0 flex items-center justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-xs text-[var(--text-primary)] truncate">{{ $progres->materi->judul ?? 'Materi' }}</p>
                            <p class="text-[11px] text-[var(--text-secondary)] mt-0.5">{{ $progres->updated_at->translatedFormat('d M Y, H:i') }} &bull; {{ $progres->materi->pelatihan->judul ?? 'Pelatihan' }}</p>
                        </div>
                        <div>
                            @if($progres->status === 'selesai')
                                <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                    <i data-lucide="check" class="w-3 h-3"></i> Selesai
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                    Proses
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-[var(--text-muted)]">Belum ada modul yang selesai dipelajari.</div>
                @endforelse
            </div>
        </div>

        {{-- Riwayat Evaluasi Kuis --}}
        <div class="rounded-2xl border border-[var(--border)] bg-[var(--card)] overflow-hidden shadow-xs">
            <div class="p-4 border-b border-[var(--border)] bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between">
                <h3 class="font-bold text-sm text-[var(--text-primary)] flex items-center gap-2">
                    <i data-lucide="award" class="w-4 h-4 text-indigo-600"></i> Riwayat Skor Evaluasi
                </h3>
                <span class="text-xs text-[var(--text-secondary)]">{{ $riwayatEvaluasi->count() }} Sesi</span>
            </div>
            <div class="p-4 overflow-y-auto max-h-[420px] space-y-2.5">
                @forelse($riwayatEvaluasi as $riwayat)
                    <a href="{{ route('peserta.evaluasi.hasil', $riwayat->id) }}" class="flex items-center justify-between p-3.5 border border-[var(--border)] rounded-xl hover:bg-[var(--card-hover)] hover:border-indigo-300 dark:hover:border-indigo-700 transition-colors group">
                        <div class="min-w-0 pr-3">
                            <p class="font-semibold text-xs text-[var(--text-primary)] group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors truncate">
                                {{ $riwayat->materi->judul ?? 'Kuis' }}
                            </p>
                            <p class="text-[11px] text-[var(--text-secondary)] mt-0.5">
                                {{ $riwayat->selesai_at ? $riwayat->selesai_at->translatedFormat('d M Y, H:i') : '-' }} &bull; {{ $riwayat->benar }}/{{ $riwayat->total_soal }} benar
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-lg font-black {{ $riwayat->skor >= 70 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                {{ number_format($riwayat->skor, 0) }}
                            </span>
                            <span class="block text-[10px] font-semibold {{ $riwayat->skor >= 70 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                {{ $riwayat->skor >= 70 ? 'Lulus' : 'Belum' }}
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="py-8 text-center text-xs text-[var(--text-muted)]">Belum ada riwayat kuis yang diselesaikan.</div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Diagnostik Pretest & Pemetaan Kompetensi --}}
    @if(isset($pretestResults) && $pretestResults->isNotEmpty())
    <div class="rounded-2xl border border-[var(--border)] bg-[var(--card)] p-6 shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-[var(--border)]">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-600 flex items-center justify-center shrink-0">
                    <i data-lucide="target" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="font-bold text-sm text-[var(--text-primary)]">Diagnostik Pretest & Pemetaan Kompetensi</h3>
                    <p class="text-xs text-[var(--text-secondary)]">Pemahaman materi disiplin ASN berdasarkan indikator topik asesmen awal</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($pretestResults as $hasil)
                @php 
                    $isWeak = $hasil->topik && $hasil->skor < $hasil->topik->batas_nilai;
                @endphp
                <div class="p-4 rounded-xl border transition-all space-y-2 {{ $isWeak ? 'border-rose-500/25 bg-rose-500/5' : 'border-emerald-500/25 bg-emerald-500/5' }}">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs font-semibold text-[var(--text-primary)] truncate">
                            {{ $hasil->topik->nama_topik ?? 'Topik Evaluasi' }}
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase shrink-0 {{ $isWeak ? 'bg-rose-500/15 text-rose-700 dark:text-rose-300' : 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300' }}">
                            {{ $isWeak ? 'Perlu Penguatan' : 'Kompeten' }}
                        </span>
                    </div>

                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl font-bold {{ $isWeak ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                            {{ $hasil->skor }}
                        </span>
                        <span class="text-xs text-[var(--text-muted)]">
                            / Standar: {{ $hasil->topik->batas_nilai ?? 70 }}
                        </span>
                    </div>

                    @if($isWeak && $hasil->topik && $hasil->topik->pelatihan)
                        <div class="pt-2 border-t border-[var(--border)]">
                            <a href="{{ route('peserta.pelatihan.show', $hasil->topik->pelatihan_id) }}"
                               class="text-xs font-semibold text-primary hover:underline flex items-center gap-1.5">
                                <i data-lucide="book-open" class="w-3.5 h-3.5"></i>
                                <span>Pelajari Modul Terkait &rarr;</span>
                            </a>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Rekapitulasi Nilai & Status Kelulusan --}}
    @if(isset($rekapNilais) && $rekapNilais->isNotEmpty())
    <div class="rounded-2xl border border-[var(--border)] bg-[var(--card)] shadow-xs overflow-hidden">
        <div class="p-5 border-b border-[var(--border)] flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0">
                <i data-lucide="award" class="w-4 h-4"></i>
            </div>
            <div>
                <h3 class="font-bold text-sm text-[var(--text-primary)]">Rekapitulasi Nilai & Status Kelulusan</h3>
                <p class="text-xs text-[var(--text-secondary)]">Nilai akhir dan predikat kelulusan pelatihan yang telah Anda ikuti</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[var(--muted)]/40 text-[var(--text-secondary)] uppercase text-[10px] font-bold tracking-wider border-b border-[var(--border)]">
                    <tr>
                        <th class="px-5 py-3.5">Pelatihan</th>
                        <th class="px-5 py-3.5 text-center">Pretest</th>
                        <th class="px-5 py-3.5 text-center">Kuis</th>
                        <th class="px-5 py-3.5 text-center">Tugas</th>
                        <th class="px-5 py-3.5 text-center">Posttest</th>
                        <th class="px-5 py-3.5 text-center font-bold text-primary">Nilai Akhir</th>
                        <th class="px-5 py-3.5 text-center">Predikat</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    @foreach($rekapNilais as $rn)
                    <tr class="hover:bg-[var(--muted)]/20 transition-colors">
                        <td class="px-5 py-4 font-semibold text-[var(--text-primary)]">
                            {{ $rn->pelatihan->judul ?? '-' }}
                        </td>
                        <td class="px-5 py-4 text-center font-medium">{{ $rn->nilai_pretest !== null ? number_format($rn->nilai_pretest, 1) : '-' }}</td>
                        <td class="px-5 py-4 text-center font-medium">{{ $rn->nilai_quiz !== null ? number_format($rn->nilai_quiz, 1) : '-' }}</td>
                        <td class="px-5 py-4 text-center font-medium">{{ $rn->nilai_tugas !== null ? number_format($rn->nilai_tugas, 1) : '-' }}</td>
                        <td class="px-5 py-4 text-center font-medium">{{ $rn->nilai_posttest !== null ? number_format($rn->nilai_posttest, 1) : '-' }}</td>
                        <td class="px-5 py-4 text-center font-bold text-primary text-sm">{{ $rn->nilai_akhir !== null ? number_format($rn->nilai_akhir, 1) : '-' }}</td>
                        <td class="px-5 py-4 text-center">
                            <span class="inline-block px-2.5 py-0.5 rounded text-xs font-bold {{ $rn->predikat_color }}">
                                {{ $rn->predikat ?? '-' }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($rn->status_lulus)
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">LULUS</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">BELUM</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

@push('scripts')
@if(count($chartData) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('skorChart')?.getContext('2d');
        if (!ctx) return;
        
        const labels = {!! json_encode($chartLabels) !!};
        const data = {!! json_encode($chartData) !!};

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Skor Evaluasi',
                    data: data,
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.08)',
                    borderWidth: 2.5,
                    pointBackgroundColor: function(context) {
                        const index = context.dataIndex;
                        const value = context.dataset.data[index];
                        return value >= 70 ? '#10b981' : '#f43f5e';
                    },
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    fill: true,
                    tension: 0.35
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        padding: 10,
                        titleFont: { size: 12, family: "'Plus Jakarta Sans', sans-serif" },
                        bodyFont: { size: 13, family: "'Plus Jakarta Sans', sans-serif", weight: 'bold' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: { stepSize: 20 },
                        grid: { borderDash: [4, 4], color: 'rgba(150, 150, 150, 0.15)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    });
</script>
@endif
@endpush

@endsection
