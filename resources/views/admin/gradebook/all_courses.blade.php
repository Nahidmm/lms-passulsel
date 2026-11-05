@extends('layouts.app')

@section('title', 'Buku Nilai: Seluruh Pelatihan / Kursus')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <span class="inline-block bg-primary/10 text-primary text-xs font-bold px-2.5 py-0.5 rounded-full uppercase">
                Master Grader Report
            </span>
        </div>
        <h1 class="text-2xl font-bold text-text-primary">Buku Nilai (Seluruh Pelatihan)</h1>
        <p class="text-xs text-text-secondary mt-1">Rekapitulasi komprehensif seluruh peserta pada semua program pelatihan LMS STRAPSUSPAS.</p>
    </div>
    
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.gradebook.cetak', ['pelatihan_id' => 'all', 'unit_kerja_id' => request('unit_kerja_id')]) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-sm transition-colors">
            <i data-lucide="printer" class="w-4 h-4"></i> Cetak Lengkap
        </a>
        <a href="{{ route('admin.gradebook.pretest') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-border bg-[var(--card)] hover:bg-secondary text-text-primary text-xs font-semibold shadow-sm transition-colors">
            <i data-lucide="clipboard-check" class="w-4 h-4 text-primary"></i> Rekap Pretest
        </a>
    </div>
</div>

@if(session('success'))
<div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 flex items-center gap-3 text-sm">
    <i data-lucide="check-circle" class="w-5 h-5 text-green-600 shrink-0"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

<!-- Global Summary Cards -->
@php
    $totalPeserta = count($allCoursesData);
    $totalCertsGlobal = collect($allCoursesData)->sum('totalCerts');
    $validAvgScores = collect($allCoursesData)->pluck('avgScore')->filter(fn($v) => $v > 0);
    $rataGlobal = $validAvgScores->isNotEmpty() ? $validAvgScores->avg() : 0;
@endphp
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    <div class="p-4 bg-[var(--card)] border border-[var(--border)] rounded-2xl">
        <span class="text-[11px] font-semibold text-text-secondary uppercase block">Total Peserta Aktif</span>
        <div class="flex items-baseline gap-1 mt-1">
            <span class="text-2xl font-bold text-text-primary">{{ $totalPeserta }}</span>
            <span class="text-xs text-text-secondary">Pegawai</span>
        </div>
    </div>
    <div class="p-4 bg-[var(--card)] border border-[var(--border)] rounded-2xl">
        <span class="text-[11px] font-semibold text-text-secondary uppercase block">Total Kursus LMS</span>
        <div class="flex items-baseline gap-1 mt-1">
            <span class="text-2xl font-bold text-primary">{{ $allPelatihans->count() }}</span>
            <span class="text-xs text-text-secondary">Program</span>
        </div>
    </div>
    <div class="p-4 bg-[var(--card)] border border-[var(--border)] rounded-2xl">
        <span class="text-[11px] font-semibold text-text-secondary uppercase block">Rata-rata Nilai Global</span>
        <div class="flex items-baseline gap-1 mt-1">
            <span class="text-2xl font-bold {{ $rataGlobal >= 70 ? 'text-emerald-600' : 'text-amber-600' }}">
                {{ $rataGlobal > 0 ? number_format($rataGlobal, 1) : '-' }}
            </span>
            <span class="text-xs text-text-secondary">/ 100</span>
        </div>
    </div>
    <div class="p-4 bg-[var(--card)] border border-[var(--border)] rounded-2xl">
        <span class="text-[11px] font-semibold text-text-secondary uppercase block">Sertifikat Diterbitkan</span>
        <div class="flex items-baseline gap-1 mt-1">
            <span class="text-2xl font-bold text-indigo-600">{{ $totalCertsGlobal }}</span>
            <span class="text-xs text-text-secondary">Sertifikat</span>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-xl p-4 mb-6">
    <form method="GET" action="{{ route('admin.gradebook.index') }}" class="flex flex-wrap items-center gap-3">
        <input type="hidden" name="pelatihan_id" value="all">

        <!-- Course Switcher -->
        <div class="min-w-[260px]">
            <label class="text-[10px] font-bold text-text-secondary uppercase tracking-wider block mb-1">Pilih Kursus LMS</label>
            <select onchange="window.location.href = this.value === 'all' ? '{{ route('admin.gradebook.index') }}?pelatihan_id=all' : '{{ url('admin/pelatihan') }}/' + this.value + '/gradebook'" class="w-full px-3 py-2 text-xs rounded-lg border border-border bg-[var(--card)] text-text-primary focus:border-primary outline-none font-semibold">
                <option value="all" selected>🌟 Semua Pelatihan (Seluruh Kursus)</option>
                @foreach($allPelatihans as $p)
                    <option value="{{ $p->id }}">{{ $p->judul }}</option>
                @endforeach
            </select>
        </div>

        <!-- Unit Kerja Filter -->
        <div class="flex-1 min-w-[200px]">
            <label class="text-[10px] font-bold text-text-secondary uppercase tracking-wider block mb-1">Filter Unit Kerja</label>
            <select name="unit_kerja_id" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs rounded-lg border border-border bg-[var(--card)] text-text-primary focus:border-primary outline-none">
                <option value="">Semua Unit Kerja</option>
                @foreach($unitKerjas as $uk)
                    <option value="{{ $uk->id }}" {{ request('unit_kerja_id') == $uk->id ? 'selected' : '' }}>{{ $uk->nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="pt-4 flex items-center gap-2">
            <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-lg bg-secondary text-text-primary hover:bg-secondary/70 transition-colors">
                Terapkan
            </button>
            @if(request('unit_kerja_id'))
                <a href="{{ route('admin.gradebook.index', ['pelatihan_id' => 'all']) }}" class="text-xs text-danger hover:underline">Reset</a>
            @endif
        </div>
    </form>
</div>

<!-- All Courses Gradebook Matrix Table -->
<div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-xl overflow-hidden">
    <div class="px-6 py-4 border-b border-border bg-secondary/30 flex items-center justify-between">
        <h3 class="font-bold text-text-primary flex items-center gap-2 text-sm">
            <i data-lucide="layers" class="w-4 h-4 text-primary"></i> Rekapitulasi Nilai Seluruh Pelatihan ({{ count($allCoursesData) }} Peserta)
        </h3>
    </div>

    @if(empty($allCoursesData))
        <div class="text-center py-16">
            <i data-lucide="user-x" class="w-12 h-12 text-border mx-auto mb-3"></i>
            <h4 class="text-text-primary font-medium">Belum ada data peserta</h4>
            <p class="text-text-secondary text-xs mt-1">Tidak ada data peserta yang cocok dengan filter yang dipilih.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-secondary/50 text-text-secondary uppercase border-b border-border text-[11px]">
                    <tr>
                        <th class="px-4 py-3 sticky left-0 bg-secondary/90 backdrop-blur z-10 border-r border-border">Nama Peserta</th>
                        <th class="px-4 py-3 border-r border-border">Unit Kerja</th>
                        <th class="px-4 py-3 text-center border-r border-border">Kelulusan Pelatihan</th>
                        <th class="px-4 py-3 border-r border-border">Nilai Per Pelatihan</th>
                        <th class="px-4 py-3 text-center border-r border-border bg-primary/5">Rata2 Nilai Akhir</th>
                        <th class="px-4 py-3 text-center border-r border-border">Predikat</th>
                        <th class="px-4 py-3 text-center border-r border-border">Sertifikat</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach($allCoursesData as $item)
                    @php
                        $u = $item['user'];
                        $avg = $item['avgScore'];
                        $predikat = match(true) {
                            $avg >= 85 => ['label' => 'Sangat Baik', 'class' => 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20'],
                            $avg >= 70 => ['label' => 'Baik', 'class' => 'bg-blue-500/10 text-blue-600 border border-blue-500/20'],
                            $avg >= 55 => ['label' => 'Cukup', 'class' => 'bg-amber-500/10 text-amber-600 border border-amber-500/20'],
                            $avg > 0   => ['label' => 'Kurang', 'class' => 'bg-rose-500/10 text-rose-600 border border-rose-500/20'],
                            default    => ['label' => 'Belum Ada', 'class' => 'bg-slate-500/10 text-slate-500 border border-slate-500/20'],
                        };
                    @endphp
                    <tr class="hover:bg-secondary/30 transition-colors">
                        <td class="px-4 py-3 sticky left-0 bg-[var(--card)] z-10 border-r border-border">
                            <div class="flex items-center gap-2.5">
                                <img src="{{ $u->avatar_url }}" alt="" class="w-7 h-7 rounded-full object-cover shrink-0 border border-border">
                                <div>
                                    <p class="font-bold text-text-primary">{{ $u->nama }}</p>
                                    <p class="text-[10px] text-text-secondary font-mono">{{ $u->nip }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 border-r border-border text-text-secondary whitespace-nowrap">
                            {{ $u->unitKerja->nama ?? '-' }}
                        </td>
                        <td class="px-4 py-3 border-r border-border text-center whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 font-bold {{ $item['totalCompleted'] > 0 ? 'text-emerald-600' : 'text-text-secondary' }}">
                                {{ $item['totalCompleted'] }} / {{ $item['totalEnrolled'] }} Lulus
                            </span>
                        </td>
                        <td class="px-4 py-3 border-r border-border min-w-[280px]">
                            <div class="flex flex-wrap gap-1.5">
                                @forelse($allPelatihans as $p)
                                    @php $cs = $item['courseScores'][$p->id] ?? null; @endphp
                                    @if($cs && $cs['enrolled'])
                                        <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] border {{ $cs['nilai_akhir'] !== null ? ($cs['nilai_akhir'] >= 70 ? 'bg-emerald-50 text-emerald-700 border-emerald-300 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-700/50' : 'bg-rose-50 text-rose-700 border-rose-300 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-700/50') : 'bg-slate-100 text-slate-600 border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700' }}" title="{{ $p->judul }}">
                                            <span class="font-semibold max-w-[120px] truncate">{{ $p->judul }}:</span>
                                            <span class="font-bold">{{ $cs['nilai_akhir'] !== null ? number_format($cs['nilai_akhir'], 1) : 'Progres' }}</span>
                                        </div>
                                    @endif
                                @empty
                                    <span class="text-text-secondary text-[11px] italic">Tidak ada pelatihan</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-4 py-3 border-r border-border text-center bg-primary/5 whitespace-nowrap">
                            <span class="text-sm font-bold {{ $avg >= 70 ? 'text-emerald-600' : ($avg > 0 ? 'text-amber-600' : 'text-text-secondary') }}">
                                {{ $avg > 0 ? number_format($avg, 1) : '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 border-r border-border text-center whitespace-nowrap">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold {{ $predikat['class'] }}">
                                {{ $predikat['label'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 border-r border-border text-center whitespace-nowrap">
                            @if($item['totalCerts'] > 0)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-500/10 text-indigo-600 border border-indigo-500/20">
                                    <i data-lucide="award" class="w-3 h-3"></i> {{ $item['totalCerts'] }} Terbit
                                </span>
                            @else
                                <span class="text-text-secondary text-[10px]">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            <a href="{{ route('admin.penilaian.show', $u->id) }}" class="inline-flex items-center gap-1 text-[11px] font-semibold px-2.5 py-1 rounded bg-secondary hover:bg-primary/10 text-primary border border-border transition-colors" title="Lihat Transkrip & Detail Nilai">
                                <i data-lucide="eye" class="w-3 h-3"></i> Transkrip
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection

