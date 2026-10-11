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
        <h1 class="text-2xl font-bold text-text-primary">Rekap Hasil Pretest Peserta</h1>
        <p class="text-xs text-text-secondary mt-1">
            {{ $pretest ? $pretest->judul : 'Pretest belum dikonfigurasi' }} &bull; Total {{ count($rows) }} peserta
        </p>
    </div>

    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.gradebook.pretest.pdf') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-sm transition-colors">
            <i data-lucide="file-down" class="w-4 h-4"></i> Export PDF
        </a>
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
                    <th class="p-4 text-xs font-bold uppercase tracking-wider text-text-secondary text-center">Status</th>
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
                        @else
                            <span class="text-xs text-text-muted">-</span>
                        @endif
                    </td>
                    <td class="p-4 text-center">
                        @if($row['nilai_pretest'] !== null)
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <i data-lucide="check-circle" class="w-3 h-3"></i> Sudah Pretest
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                <i data-lucide="clock" class="w-3 h-3"></i> Belum Pretest
                            </span>
                        @endif
                    </td>
                    <td class="p-4 text-right">
                        @if($row['nilai_pretest'] !== null)
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
