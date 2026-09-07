@extends('layouts.app')

@section('title', 'Dashboard Kinerja Pembelajaran HUKDIS')

@section('content')
<!-- Header Section -->
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <span class="inline-block bg-primary/10 text-primary text-xs font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                Monitoring & Evaluasi HUKDIS
            </span>
            <span class="text-xs text-text-secondary">&bull; Terkini: {{ now()->translatedFormat('l, d F Y') }}</span>
        </div>
        <h1 class="text-2xl font-bold text-text-primary">Dashboard Kinerja & Statistik LMS</h1>
        <p class="text-xs text-text-secondary mt-1">Pantau perkembangan pemahaman disiplin ASN, analisis kelemahan kompetensi, dan hasil penilaian.</p>
    </div>

    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.pelatihan.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-border bg-[var(--card)] hover:bg-secondary text-text-primary text-xs font-semibold shadow-sm transition-colors">
            <i data-lucide="book-open" class="w-4 h-4 text-primary"></i> Kelola Kursus
        </a>
        <a href="{{ route('admin.akun.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-border bg-[var(--card)] hover:bg-secondary text-text-primary text-xs font-semibold shadow-sm transition-colors">
            <i data-lucide="users" class="w-4 h-4 text-primary"></i> Pengguna ({{ $totalPengguna }})
        </a>
    </div>
</div>

<!-- Alert Tugas Pending Review jika ada -->
@if($countPendingTugas > 0)
<div class="mb-6 p-4 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-amber-500/20 text-amber-600 flex items-center justify-center shrink-0">
            <i data-lucide="bell" class="w-5 h-5"></i>
        </div>
        <div>
            <h4 class="text-sm font-bold text-amber-900 dark:text-amber-300">Ada {{ $countPendingTugas }} Penugasan Menunggu Penilaian</h4>
            <p class="text-xs text-amber-700 dark:text-amber-400">Peserta telah mengunggah telaah kasus hukdis atau sertifikat eksternal yang memerlukan review Anda.</p>
        </div>
    </div>
    <a href="#tugas-pending-section" class="px-3.5 py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold transition-colors shrink-0">
        Review Sekarang &darr;
    </a>
</div>
@endif

<!-- Key Metrics Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
    <!-- Total Peserta -->
    <div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-semibold text-text-secondary uppercase">Peserta Aktif</span>
            <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-600 flex items-center justify-center">
                <i data-lucide="users" class="w-4 h-4"></i>
            </div>
        </div>
        <div class="text-2xl font-extrabold text-text-primary">{{ number_format($totalPengguna) }}</div>
        <div class="text-[11px] text-text-secondary mt-1 flex items-center gap-1">
            <span class="text-green-600 font-semibold">{{ $penggunaAktif }} sedang aktif</span> sesi
        </div>
    </div>

    <!-- Rata-rata Pretest -->
    <div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-semibold text-text-secondary uppercase">Pretest (Awal)</span>
            <div class="w-8 h-8 rounded-lg bg-purple-500/10 text-purple-600 flex items-center justify-center">
                <i data-lucide="clipboard-check" class="w-4 h-4"></i>
            </div>
        </div>
        <div class="text-2xl font-extrabold text-purple-600">{{ $rataPretest }}</div>
        <div class="text-[11px] text-text-secondary mt-1">Rata-rata pemahaman awal</div>
    </div>

    <!-- Rata-rata Kuis Formatif -->
    <div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-semibold text-text-secondary uppercase">Kuis Formatif</span>
            <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-600 flex items-center justify-center">
                <i data-lucide="help-circle" class="w-4 h-4"></i>
            </div>
        </div>
        <div class="text-2xl font-extrabold text-indigo-600">{{ $rataQuiz }}</div>
        <div class="text-[11px] text-text-secondary mt-1">Rata-rata kuis per modul</div>
    </div>

    <!-- Rata-rata Tugas / Kasus -->
    <div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-semibold text-text-secondary uppercase">Praktik Kasus</span>
            <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center">
                <i data-lucide="file-text" class="w-4 h-4"></i>
            </div>
        </div>
        <div class="text-2xl font-extrabold text-amber-600">{{ $rataTugas }}</div>
        <div class="text-[11px] text-text-secondary mt-1">Rata-rata tugas & sertifikat</div>
    </div>

    <!-- Posttest & Kelulusan -->
    <div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-xl p-4 bg-primary/5">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-semibold text-primary uppercase">Posttest (Akhir)</span>
            <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                <i data-lucide="award" class="w-4 h-4"></i>
            </div>
        </div>
        <div class="text-2xl font-extrabold text-primary">{{ $rataPosttest }}</div>
        <div class="text-[11px] text-green-700 font-semibold mt-1">
            {{ $lulusRate }}% Kelulusan ({{ $totalLulus }}/{{ $totalRekap }})
        </div>
    </div>
</div>

<!-- Section 1: Pretest Diagnostic & Grade Distribution -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Analisis Kelemahan Pretest (Topik Lemah) -->
    <div class="lg:col-span-2 bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-xl overflow-hidden">
        <div class="p-5 border-b border-border bg-secondary/30 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="p-1.5 rounded-lg bg-purple-500/10 text-purple-600">
                    <i data-lucide="target" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="font-bold text-text-primary text-sm">Analisis Diagnostik Pretest (Indikator Kelemahan Peserta)</h3>
                    <p class="text-[11px] text-text-secondary">Area materi disiplin dengan skor terendah yang membutuhkan atensi khusus</p>
                </div>
            </div>
            <a href="{{ route('admin.pretest.index') }}" class="text-xs font-semibold text-primary hover:underline">
                Kelola Pretest &rarr;
            </a>
        </div>

        <div class="p-5">
            @if($topikKelemahan->isEmpty())
                <div class="text-center py-8 text-xs text-text-secondary">
                    <i data-lucide="check-circle-2" class="w-8 h-8 text-green-500 mx-auto mb-2"></i>
                    Belum ada data evaluasi pretest topik tercatat.
                </div>
            @else
                <div class="space-y-4">
                    @foreach($topikKelemahan as $item)
                        @php 
                            $skor = round($item->avg_skor, 1);
                            $isWeak = $skor < 70;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <span class="font-semibold text-text-primary flex items-center gap-2">
                                    <i data-lucide="{{ $isWeak ? 'alert-circle' : 'check' }}" class="w-3.5 h-3.5 {{ $isWeak ? 'text-red-500' : 'text-green-500' }}"></i>
                                    {{ $item->topik->nama_topik ?? 'Topik #' . $item->topik_pelatihan_id }}
                                </span>
                                <div class="flex items-center gap-2">
                                    <span class="font-extrabold {{ $isWeak ? 'text-red-600' : 'text-green-600' }}">
                                        Rata-rata: {{ $skor }}
                                    </span>
                                    <span class="text-[10px] text-text-secondary">({{ $item->total_test }} peserta)</span>
                                </div>
                            </div>
                            <div class="w-full bg-secondary rounded-full h-2 overflow-hidden">
                                <div class="h-2 rounded-full {{ $skor < 60 ? 'bg-red-500' : ($skor < 75 ? 'bg-amber-500' : 'bg-green-500') }}" style="width: {{ min($skor, 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Distribusi Predikat (A / B / C / D) -->
    <div class="lg:col-span-1 bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-xl p-5">
        <h3 class="font-bold text-text-primary text-sm mb-4 flex items-center gap-2">
            <i data-lucide="pie-chart" class="w-4 h-4 text-primary"></i> Distribusi Predikat Nilai
        </h3>

        <div class="space-y-3">
            <!-- Predikat A -->
            <div class="p-3 rounded-lg border border-border bg-[var(--card)] flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-7 h-7 rounded bg-green-100 text-green-700 font-extrabold text-xs flex items-center justify-center">A</span>
                    <div>
                        <span class="text-xs font-bold text-text-primary">Sangat Baik</span>
                        <span class="text-[10px] text-text-secondary block">Nilai &ge; 85</span>
                    </div>
                </div>
                <span class="text-base font-extrabold text-green-700">{{ $predikatCounts['A'] }}</span>
            </div>

            <!-- Predikat B -->
            <div class="p-3 rounded-lg border border-border bg-[var(--card)] flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-7 h-7 rounded bg-blue-100 text-blue-700 font-extrabold text-xs flex items-center justify-center">B</span>
                    <div>
                        <span class="text-xs font-bold text-text-primary">Baik</span>
                        <span class="text-[10px] text-text-secondary block">Nilai 75 - 84</span>
                    </div>
                </div>
                <span class="text-base font-extrabold text-blue-700">{{ $predikatCounts['B'] }}</span>
            </div>

            <!-- Predikat C -->
            <div class="p-3 rounded-lg border border-border bg-[var(--card)] flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-7 h-7 rounded bg-amber-100 text-amber-700 font-extrabold text-xs flex items-center justify-center">C</span>
                    <div>
                        <span class="text-xs font-bold text-text-primary">Cukup</span>
                        <span class="text-[10px] text-text-secondary block">Nilai 60 - 74</span>
                    </div>
                </div>
                <span class="text-base font-extrabold text-amber-700">{{ $predikatCounts['C'] }}</span>
            </div>

            <!-- Predikat D -->
            <div class="p-3 rounded-lg border border-border bg-[var(--card)] flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-7 h-7 rounded bg-red-100 text-red-700 font-extrabold text-xs flex items-center justify-center">D</span>
                    <div>
                        <span class="text-xs font-bold text-text-primary">Kurang</span>
                        <span class="text-[10px] text-text-secondary block">Nilai &lt; 60</span>
                    </div>
                </div>
                <span class="text-base font-extrabold text-red-700">{{ $predikatCounts['D'] }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Section 2: Pending Submissions & Course Overview -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Tugas Menunggu Review -->
    <div id="tugas-pending-section" class="lg:col-span-2 bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-xl overflow-hidden">
        <div class="p-5 border-b border-border bg-secondary/30 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="p-1.5 rounded-lg bg-amber-500/10 text-amber-600">
                    <i data-lucide="file-check" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="font-bold text-text-primary text-sm">Penugasan Menunggu Penilaian ({{ $countPendingTugas }})</h3>
                    <p class="text-[11px] text-text-secondary">Pengumpulan studi kasus atau sertifikat luar yang siap diperiksa</p>
                </div>
            </div>
        </div>

        <div class="p-5">
            @if($pendingSubmissions->isEmpty())
                <div class="text-center py-10 text-xs text-text-secondary">
                    <i data-lucide="inbox" class="w-8 h-8 text-border mx-auto mb-2"></i>
                    Semua penugasan telah dinilai. Tidak ada antrian baru!
                </div>
            @else
                <div class="space-y-3">
                    @foreach($pendingSubmissions as $sub)
                    <div class="flex items-center justify-between p-3 rounded-lg border border-border hover:bg-secondary/20 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg {{ $sub->tugas->tipe === 'upload_sertifikat' ? 'bg-blue-50 text-blue-600' : 'bg-amber-50 text-amber-600' }} flex items-center justify-center shrink-0">
                                <i data-lucide="{{ $sub->tugas->tipe === 'upload_sertifikat' ? 'award' : 'file-text' }}" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-text-primary">{{ $sub->user->nama ?? '-' }}</h4>
                                <p class="text-[11px] text-text-secondary">
                                    {{ $sub->tugas->judul ?? '-' }} &bull; {{ $sub->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.tugas.submissions', $sub->tugas_id) }}" class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg bg-primary hover:bg-primary-hover text-white transition-colors">
                                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Periksa & Nilai
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Kursus Aktif -->
    <div class="lg:col-span-1 bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-xl p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-text-primary text-sm flex items-center gap-2">
                <i data-lucide="book-open" class="w-4 h-4 text-primary"></i> Kursus LMS Aktif
            </h3>
            <a href="{{ route('admin.pelatihan.create') }}" class="text-xs font-semibold text-primary hover:underline">
                + Baru
            </a>
        </div>

        <div class="space-y-3">
            @foreach($pelatihans as $p)
            <div class="p-3 rounded-lg border border-border bg-[var(--card)] hover:border-primary/40 transition-colors">
                <div class="flex items-center justify-between mb-1">
                    <h4 class="text-xs font-bold text-text-primary line-clamp-1">{{ $p->judul }}</h4>
                    <span class="text-[10px] font-bold text-green-700 bg-green-100 px-1.5 py-0.5 rounded">Aktif</span>
                </div>
                <div class="text-[10px] text-text-secondary flex items-center gap-2 mb-2">
                    <span>{{ $p->materis_count }} Materi/Kuis</span> &bull; <span>{{ $p->tugas_count }} Tugas</span>
                </div>
                <div class="flex items-center gap-2 pt-2 border-t border-border">
                    <a href="{{ route('admin.pelatihan.show', $p->id) }}" class="text-xs font-medium text-primary hover:underline flex items-center gap-1">
                        <i data-lucide="sliders" class="w-3 h-3"></i> Kurikulum
                    </a>
                    <span class="text-border">&bull;</span>
                    <a href="{{ route('admin.pelatihan.gradebook', $p->id) }}" class="text-xs font-medium text-text-secondary hover:text-text-primary flex items-center gap-1">
                        <i data-lucide="award" class="w-3 h-3"></i> Buku Nilai
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
