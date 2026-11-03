@extends('layouts.app')

@section('title', 'Tugas Menunggu Penilaian')

@section('content')
{{-- Header --}}
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('dashboard') }}" class="text-xs text-text-secondary hover:text-primary flex items-center gap-1">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Dashboard
            </a>
        </div>
        <h1 class="text-2xl font-bold text-text-primary flex items-center gap-2">
            <i data-lucide="clipboard-list" class="w-6 h-6 text-amber-500"></i>
            Tugas Menunggu Penilaian
        </h1>
        <p class="text-xs text-text-secondary mt-1">Semua pengumpulan tugas peserta yang belum dinilai, dikelompokkan per penugasan.</p>
    </div>
    <div class="flex items-center gap-3">
        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-amber-500/10 border border-amber-400/30 text-amber-700 dark:text-amber-300 text-sm font-bold">
            <i data-lucide="clock" class="w-4 h-4"></i>
            {{ $totalPending }} Pending
        </span>
    </div>
</div>

@if(session('success'))
    <div class="mb-4 p-3 rounded-xl bg-green-500/10 border border-green-500/30 text-green-700 dark:text-green-300 text-xs font-semibold flex items-center gap-2">
        <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
        {{ session('success') }}
    </div>
@endif

@if($tugasPending->isEmpty())
    <div class="bg-[var(--card)] border border-[var(--border)] rounded-2xl p-16 text-center">
        <i data-lucide="inbox" class="w-12 h-12 text-border mx-auto mb-4"></i>
        <h3 class="text-lg font-bold text-text-primary mb-2">Semua Bersih!</h3>
        <p class="text-sm text-text-secondary">Tidak ada penugasan yang menunggu penilaian saat ini.</p>
        <a href="{{ route('dashboard') }}" class="mt-6 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-white text-xs font-semibold hover:bg-primary/90 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Dashboard
        </a>
    </div>
@else
    <div class="space-y-6">
        @foreach($tugasPending as $tugas)
        @php $submissions = $recentSubmissions[$tugas->id] ?? collect(); @endphp
        <div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-2xl overflow-hidden">
            {{-- Tugas Header --}}
            <div class="p-5 border-b border-border bg-secondary/30 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl {{ $tugas->tipe === 'upload_sertifikat' ? 'bg-blue-500/10 text-blue-600' : 'bg-amber-500/10 text-amber-600' }} flex items-center justify-center shrink-0 mt-0.5">
                        <i data-lucide="{{ $tugas->tipe === 'upload_sertifikat' ? 'award' : 'file-text' }}" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-text-primary text-sm">{{ $tugas->judul }}</h3>
                        <div class="flex flex-wrap items-center gap-2 mt-1">
                            <span class="text-[11px] text-text-secondary flex items-center gap-1">
                                <i data-lucide="book-open" class="w-3 h-3"></i>
                                {{ $tugas->pelatihan->judul ?? 'Tanpa Pelatihan' }}
                            </span>
                            <span class="text-[10px] px-2 py-0.5 rounded-full {{ $tugas->tipe === 'upload_sertifikat' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }} font-semibold">
                                {{ $tugas->tipe === 'upload_sertifikat' ? 'Sertifikat' : 'Tugas Umum' }}
                            </span>
                            @if($tugas->deadline)
                                <span class="text-[10px] text-text-secondary flex items-center gap-1">
                                    <i data-lucide="calendar" class="w-3 h-3"></i>
                                    Deadline: {{ $tugas->deadline->translatedFormat('d M Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-500/15 text-amber-700 dark:text-amber-300 text-xs font-bold border border-amber-400/30">
                        <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                        {{ $tugas->pending_count }} Pending
                    </span>
                    <a href="{{ route('admin.tugas.submissions', $tugas->id) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg bg-primary hover:bg-primary/90 text-white text-xs font-bold transition-colors">
                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                        Nilai Semua
                    </a>
                </div>
            </div>

            {{-- Submission List Preview --}}
            <div class="divide-y divide-border">
                @forelse($submissions->take(5) as $sub)
                <div class="flex items-center justify-between px-5 py-3 hover:bg-secondary/20 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center shrink-0 text-xs font-bold">
                            {{ mb_substr($sub->user->nama ?? '?', 0, 1) }}
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-text-primary">{{ $sub->user->nama ?? '-' }}</p>
                            <p class="text-[11px] text-text-secondary flex items-center gap-1">
                                {{ $sub->user->unitKerja->nama_unit ?? 'Unit tidak diketahui' }}
                                &bull; Dikumpulkan {{ $sub->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($sub->is_late)
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-semibold">Terlambat</span>
                        @endif
                        <a href="{{ route('admin.tugas.submissions', $tugas->id) }}"
                           class="text-xs text-primary hover:underline font-semibold">
                            Periksa &rarr;
                        </a>
                    </div>
                </div>
                @empty
                <div class="px-5 py-4 text-xs text-text-secondary text-center">Tidak ada data submission.</div>
                @endforelse

                @if($submissions->count() > 5)
                <div class="px-5 py-3 bg-secondary/10 text-center">
                    <a href="{{ route('admin.tugas.submissions', $tugas->id) }}" class="text-xs font-semibold text-primary hover:underline">
                        + {{ $submissions->count() - 5 }} pengumpulan lainnya &rarr;
                    </a>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection

