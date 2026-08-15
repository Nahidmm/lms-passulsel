@extends('layouts.app')

@section('title', 'Nilai Peserta – ' . $materi->judul)

@section('content')

<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.materi.edit', $materi->id) }}" class="text-text-secondary hover:text-primary flex items-center gap-1.5 font-medium transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Konfigurasi
        </a>
    </div>
    <a href="{{ route('admin.kuis.export', $materi->id) }}" class="inline-flex items-center gap-2 bg-success hover:bg-success/80 text-white font-bold px-4 py-2.5 rounded-xl transition-all shadow-sm">
        <i data-lucide="download" class="w-4 h-4"></i> Export Nilai (CSV)
    </a>
</div>

<div class="mb-6">
    <div class="flex items-start gap-4">
        <div class="w-12 h-12 bg-accent/10 rounded-xl flex items-center justify-center shrink-0">
            <i data-lucide="bar-chart-2" class="w-6 h-6 text-accent"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-text-primary">Nilai Peserta Kuis</h1>
            <p class="text-text-secondary mt-1">Kuis: <span class="font-bold text-text-primary">{{ $materi->judul }}</span> &bull; Nilai lulus: <span class="font-bold text-accent">{{ $passingGrade }}</span></p>
        </div>
    </div>
</div>

{{-- Stats --}}
@php
    $lulus    = $sesis->where('skor', '>=', $passingGrade)->count();
    $gagal    = $sesis->count() - $lulus;
    $rataRata = $sesis->count() > 0 ? round($sesis->avg('skor'), 1) : 0;
@endphp
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white border border-border rounded-xl p-5 text-center shadow-sm">
        <p class="text-3xl font-bold text-text-primary">{{ $sesis->count() }}</p>
        <p class="text-sm text-text-secondary mt-1">Total Peserta</p>
    </div>
    <div class="bg-white border border-border rounded-xl p-5 text-center shadow-sm">
        <p class="text-3xl font-bold text-success">{{ $lulus }}</p>
        <p class="text-sm text-text-secondary mt-1">Lulus</p>
    </div>
    <div class="bg-white border border-border rounded-xl p-5 text-center shadow-sm">
        <p class="text-3xl font-bold text-danger">{{ $gagal }}</p>
        <p class="text-sm text-text-secondary mt-1">Tidak Lulus</p>
    </div>
    <div class="bg-white border border-border rounded-xl p-5 text-center shadow-sm">
        <p class="text-3xl font-bold text-accent">{{ $rataRata }}</p>
        <p class="text-sm text-text-secondary mt-1">Rata-rata Nilai</p>
    </div>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl border border-border shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-border bg-secondary/30 flex items-center gap-2">
        <i data-lucide="list-checks" class="w-5 h-5 text-primary"></i>
        <h3 class="font-bold text-text-primary">Daftar Peserta</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-text-secondary uppercase bg-secondary border-b border-border">
                <tr>
                    <th class="px-4 py-3 w-8">No</th>
                    <th class="px-4 py-3">Nama Peserta</th>
                    <th class="px-4 py-3 hidden md:table-cell">Jabatan</th>
                    <th class="px-4 py-3 text-center">Benar</th>
                    <th class="px-4 py-3 text-center">Nilai</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center hidden md:table-cell">Tanggal</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sesis as $sesi)
                    @php $lulus = $sesi->skor >= $passingGrade; @endphp
                    <tr class="border-b border-border hover:bg-secondary/30 transition-colors">
                        <td class="px-4 py-3 text-text-secondary">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3">
                            <p class="font-bold text-text-primary">{{ $sesi->user->nama ?? '-' }}</p>
                            <p class="text-xs text-text-secondary">{{ $sesi->user->nip ?? '-' }}</p>
                        </td>
                        <td class="px-4 py-3 text-text-secondary hidden md:table-cell">{{ $sesi->user->jabatan->nama_jabatan ?? '-' }}</td>
                        <td class="px-4 py-3 text-center text-text-primary font-medium">{{ $sesi->benar }} / {{ $sesi->total_soal }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-xl font-bold {{ $lulus ? 'text-success' : 'text-danger' }}">{{ number_format($sesi->skor, 1) }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($lulus)
                                <span class="inline-block bg-success/10 text-success text-xs font-bold px-2 py-1 rounded-full">Lulus</span>
                            @else
                                <span class="inline-block bg-danger/10 text-danger text-xs font-bold px-2 py-1 rounded-full">Tidak Lulus</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-text-secondary text-xs hidden md:table-cell">
                            {{ $sesi->selesai_at ? $sesi->selesai_at->format('d M Y, H:i') : '-' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.kuis.jawaban', [$materi->id, $sesi->id]) }}"
                               class="inline-flex items-center gap-1 bg-primary/10 hover:bg-primary text-primary hover:text-white text-xs font-bold px-3 py-1.5 rounded-lg transition-all">
                                <i data-lucide="eye" class="w-3.5 h-3.5"></i> Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-text-secondary">
                            <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-3 text-border"></i>
                            <p class="font-medium">Belum ada peserta yang mengerjakan kuis ini.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
