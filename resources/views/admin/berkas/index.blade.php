@extends('layouts.app')

@section('title', 'Arsip Berkas Peserta')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('dashboard') }}" class="text-text-secondary hover:text-primary transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <span class="inline-block bg-primary/10 text-primary text-xs font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                Arsip Berkas
            </span>
        </div>
        <h1 class="text-2xl font-bold text-text-primary">Arsip Berkas Peserta</h1>
        <p class="text-xs text-text-secondary mt-1">Seluruh file yang diunggah peserta, dikelompokkan per pelatihan & tugas.</p>
    </div>

    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.berkas.pdf') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-sm transition-colors">
            <i data-lucide="file-down" class="w-4 h-4"></i> Export PDF
        </a>
    </div>
</div>

@forelse($pelatihans as $pelatihan)
<div class="mb-6">
    <div class="mb-2 flex items-center gap-2">
        <i data-lucide="book-open" class="w-4 h-4 text-primary"></i>
        <h2 class="text-sm font-bold text-text-primary">{{ $pelatihan->judul }}</h2>
    </div>

    @foreach($pelatihan->tugas as $tugas)
        @if($tugas->submissions->isNotEmpty())
        <div class="mb-4 rounded-xl border border-[var(--border)] bg-[var(--card)] overflow-hidden shadow-xs">
            <div class="px-4 py-2.5 bg-secondary/40 border-b border-[var(--border)] flex items-center justify-between">
                <span class="text-xs font-bold text-text-primary">{{ $tugas->judul }}</span>
                <span class="text-[10px] font-semibold text-text-secondary">{{ $tugas->submissions->count() }} berkas</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-secondary/20 text-text-secondary uppercase text-[10px] border-b border-[var(--border)]">
                        <tr>
                            <th class="px-4 py-2">Peserta</th>
                            <th class="px-4 py-2">File</th>
                            <th class="px-4 py-2">Dikumpulkan</th>
                            <th class="px-4 py-2 text-center">Status</th>
                            <th class="px-4 py-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border)]">
                        @foreach($tugas->submissions as $sub)
                        <tr class="hover:bg-secondary/10 transition-colors">
                            <td class="px-4 py-2.5">
                                <div class="font-medium text-text-primary text-xs">{{ $sub->user->nama ?? '-' }}</div>
                                <div class="text-[10px] text-text-secondary">NIP: {{ $sub->user->nip ?? '-' }} &bull; {{ $sub->user->unitKerja?->nama_unit ?? 'Umum' }}</div>
                            </td>
                            <td class="px-4 py-2.5 text-xs text-text-secondary">
                                {{ $sub->file_nama_asli ?? 'Berkas Tugas' }}
                            </td>
                            <td class="px-4 py-2.5 text-xs text-text-secondary">{{ $sub->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-4 py-2.5 text-center">
                                @if($sub->status === 'graded')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 text-green-700">Dinilai</span>
                                @elseif($sub->status === 'need_revision')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-orange-100 text-orange-700">Perlu Revisi</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-yellow-100 text-yellow-800">Menunggu Review</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-right">
                                @if($sub->file_path)
                                <a href="{{ asset('storage/' . $sub->file_path) }}" target="_blank" class="inline-flex items-center gap-1 text-primary hover:underline text-xs font-medium">
                                    <i data-lucide="download" class="w-3.5 h-3.5"></i> Unduh
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    @endforeach
</div>
@empty
<div class="rounded-2xl border border-dashed border-[var(--border)] bg-[var(--card)] p-12 text-center">
    <i data-lucide="inbox" class="w-12 h-12 text-text-muted mx-auto mb-3"></i>
    <p class="text-sm text-text-primary font-medium">Belum ada berkas yang diunggah peserta.</p>
</div>
@endforelse
@endsection
