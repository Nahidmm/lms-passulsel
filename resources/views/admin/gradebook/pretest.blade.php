@extends('layouts.app')

@section('title', 'Rekap Hasil Pretest - Buku Nilai')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('admin.gradebook.index') }}" class="text-text-secondary hover:text-primary transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <span class="inline-block bg-primary/10 text-primary text-xs font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                Pretest Diagnostik
            </span>
        </div>
        <h1 class="text-2xl font-bold text-text-primary">Rekap Hasil & Pemantauan Pretest</h1>
        <p class="text-xs text-text-secondary mt-1">
            {{ $pretest ? $pretest->judul : 'Pretest belum dikonfigurasi' }} &bull; Total {{ count($rows) }} peserta
        </p>
    </div>

    <div class="flex flex-wrap items-center gap-2">
        <button type="button" onclick="window.location.reload()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-[var(--border)] bg-[var(--card)] hover:bg-slate-50 dark:hover:bg-slate-800 text-text-primary text-xs font-semibold shadow-xs transition-colors">
            <i data-lucide="refresh-cw" class="w-4 h-4 text-blue-600"></i> Refresh Status
        </button>
        <a href="{{ route('admin.gradebook.pretest.pdf') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-sm transition-colors">
            <i data-lucide="file-down" class="w-4 h-4"></i> Export PDF
        </a>
    </div>
</div>

{{-- Summary Metric Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5 mb-6">
    <div class="bg-[var(--card)] border border-[var(--border)] rounded-xl p-4 shadow-2xs">
        <div class="flex items-center justify-between text-slate-500 mb-1">
            <span class="text-xs font-semibold">Total Peserta</span>
            <i data-lucide="users" class="w-4 h-4"></i>
        </div>
        <div class="text-2xl font-extrabold text-[var(--text-primary)]">{{ $stats['total'] ?? count($rows) }}</div>
    </div>

    <div class="bg-[var(--card)] border border-emerald-200 dark:border-emerald-900/50 rounded-xl p-4 shadow-2xs">
        <div class="flex items-center justify-between text-emerald-600 dark:text-emerald-400 mb-1">
            <span class="text-xs font-semibold">Selesai</span>
            <i data-lucide="check-circle-2" class="w-4 h-4"></i>
        </div>
        <div class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ $stats['selesai'] ?? 0 }}</div>
    </div>

    <div class="bg-[var(--card)] border border-blue-200 dark:border-blue-900/50 rounded-xl p-4 shadow-2xs">
        <div class="flex items-center justify-between text-blue-600 dark:text-blue-400 mb-1">
            <span class="text-xs font-semibold">Mengerjakan</span>
            <span class="w-2.5 h-2.5 rounded-full bg-blue-600 animate-pulse"></span>
        </div>
        <div class="text-2xl font-extrabold text-blue-600 dark:text-blue-400">{{ $stats['mengerjakan'] ?? 0 }}</div>
    </div>

    <div class="bg-[var(--card)] border border-amber-200 dark:border-amber-900/50 rounded-xl p-4 shadow-2xs">
        <div class="flex items-center justify-between text-amber-600 dark:text-amber-400 mb-1">
            <span class="text-xs font-semibold">Sedang Dijeda</span>
            <i data-lucide="pause-circle" class="w-4 h-4"></i>
        </div>
        <div class="text-2xl font-extrabold text-amber-600 dark:text-amber-400">{{ $stats['dijeda'] ?? 0 }}</div>
    </div>

    <div class="bg-[var(--card)] border border-[var(--border)] rounded-xl p-4 shadow-2xs col-span-2 sm:col-span-1">
        <div class="flex items-center justify-between text-slate-400 mb-1">
            <span class="text-xs font-semibold">Belum Mulai</span>
            <i data-lucide="clock" class="w-4 h-4"></i>
        </div>
        <div class="text-2xl font-extrabold text-slate-500">{{ $stats['belum'] ?? 0 }}</div>
    </div>
</div>

@if(session('success'))
<div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 flex items-center gap-3 text-sm">
    <i data-lucide="check-circle" class="w-5 h-5 text-green-600 shrink-0"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

<div class="bg-[var(--card)] border border-[var(--border)] rounded-2xl overflow-hidden shadow-xs">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="border-b border-[var(--border)] bg-slate-50/50 dark:bg-slate-900/50">
                <tr>
                    <th class="p-4 text-xs font-bold uppercase tracking-wider text-text-secondary w-14 text-center">No</th>
                    <th class="p-4 text-xs font-bold uppercase tracking-wider text-text-secondary">Peserta</th>
                    <th class="p-4 text-xs font-bold uppercase tracking-wider text-text-secondary">Unit Kerja</th>
                    <th class="p-4 text-xs font-bold uppercase tracking-wider text-text-secondary text-center">Nilai Pretest</th>
                    <th class="p-4 text-xs font-bold uppercase tracking-wider text-text-secondary text-center">Status Pengerjaan</th>
                    <th class="p-4 text-xs font-bold uppercase tracking-wider text-text-secondary text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--border)]">
                @forelse($rows as $index => $row)
                <tr class="hover:bg-[var(--card-hover)] transition-colors">
                    <td class="p-4 text-center text-xs text-text-secondary">{{ $index + 1 }}</td>
                    <td class="p-4">
                        <p class="text-xs font-bold text-text-primary">{{ $row['user']->nama }}</p>
                        <p class="text-[11px] text-text-secondary font-mono">NIP: {{ $row['user']->nip }}</p>
                    </td>
                    <td class="p-4 text-xs text-text-secondary">{{ $row['user']->unitKerja?->nama_unit ?? '-' }}</td>
                    <td class="p-4 text-center">
                        @if($row['nilai_pretest'] !== null)
                            <span class="text-sm font-extrabold {{ $row['nilai_pretest'] >= 70 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ number_format($row['nilai_pretest'], 1) }}
                            </span>
                        @elseif($row['status_ujian'] === 'sedang_mengerjakan')
                            <span class="inline-block text-[11px] font-semibold text-blue-600 dark:text-blue-400 italic">
                                Sedang dikerjakan...
                            </span>
                        @elseif($row['status_ujian'] === 'dijeda')
                            <span class="inline-block text-[11px] font-semibold text-amber-600 dark:text-amber-400 italic">
                                Dijeda...
                            </span>
                        @else
                            <span class="text-xs text-text-muted">-</span>
                        @endif
                    </td>
                    <td class="p-4 text-center">
                        @if($row['status_ujian'] === 'selesai')
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-600 shrink-0"></i> Selesai
                            </span>
                        @elseif($row['status_ujian'] === 'sedang_mengerjakan')
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2.5 py-1 rounded-full bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 animate-pulse">
                                <span class="w-2 h-2 rounded-full bg-blue-600 shrink-0"></span> Sedang Mengerjakan
                            </span>
                        @elseif($row['status_ujian'] === 'dijeda')
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2.5 py-1 rounded-full bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                <i data-lucide="pause-circle" class="w-3.5 h-3.5 text-amber-600 shrink-0"></i> Sedang Dijeda
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i> Belum Mulai
                            </span>
                        @endif
                    </td>
                    <td class="p-4 text-right">
                        @if($row['nilai_pretest'] !== null || $row['status_ujian'] === 'selesai')
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('admin.gradebook.pretest.detail', $row['user']->id) }}" class="inline-flex items-center gap-1 text-[11px] font-bold px-3 py-1.5 rounded-lg border border-[var(--border)] bg-[var(--card)] hover:bg-secondary text-text-primary transition-colors">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i> Lihat Detail
                                </a>
                                <a href="{{ route('admin.gradebook.pretest.detail.pdf', $row['user']->id) }}" class="inline-flex items-center gap-1 text-[11px] font-bold px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white transition-colors">
                                    <i data-lucide="file-down" class="w-3.5 h-3.5"></i> PDF
                                </a>
                            </div>
                        @else
                            <span class="text-xs text-text-muted">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-10 text-center text-xs text-text-muted">Belum ada peserta terdaftar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
