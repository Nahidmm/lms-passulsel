@extends('layouts.app')

@section('title', 'Detail Penilaian â€” ' . $user->nama)

@section('content')

{{-- Back --}}
<div class="mb-5">
    <a href="{{ route('admin.gradebook.index') }}" class="text-text-secondary hover:text-primary flex items-center gap-1.5 font-medium transition-colors w-fit">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Buku Nilai (Gradebook)
    </a>
</div>

{{-- Profil Peserta --}}
<div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-2xl p-6 mb-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
        <img src="{{ $user->avatar_url }}" alt="{{ $user->nama }}" class="w-20 h-20 rounded-2xl object-cover border-2 border-border shadow-sm shrink-0">
        <div class="flex-1 min-w-0">
            <h1 class="text-2xl font-bold text-text-primary truncate">{{ $user->nama }}</h1>
            <p class="text-text-secondary text-sm mt-0.5">{{ $user->nip }} Â· {{ $user->jabatan->nama_jabatan ?? '-' }}</p>
        </div>
        {{-- Summary Cards --}}
        <div class="flex gap-4 flex-wrap">
            <div class="text-center bg-secondary rounded-xl px-5 py-3 border border-border">
                <p class="text-2xl font-bold text-primary">{{ $sesis->count() }}</p>
                <p class="text-xs text-text-secondary mt-0.5">Kuis Dikerjakan</p>
            </div>
            <div class="text-center bg-secondary rounded-xl px-5 py-3 border border-border">
                <p class="text-2xl font-bold {{ $rataRata >= 70 ? 'text-success' : ($rataRata > 0 ? 'text-warning' : 'text-text-secondary') }}">
                    {{ $rataRata > 0 ? number_format($rataRata, 1) : '-' }}
                </p>
                <p class="text-xs text-text-secondary mt-0.5">Rata-rata Nilai Kuis</p>
            </div>
            <div class="text-center bg-secondary rounded-xl px-5 py-3 border border-border">
                <p class="text-2xl font-bold text-success">{{ $totalLulus }}</p>
                <p class="text-xs text-text-secondary mt-0.5">Kuis Lulus</p>
            </div>
            <div class="text-center bg-secondary rounded-xl px-5 py-3 border border-border">
                <p class="text-2xl font-bold text-primary">{{ $progresMateri->count() }}</p>
                <p class="text-xs text-text-secondary mt-0.5">Materi Selesai</p>
            </div>
            <div class="text-center rounded-xl px-5 py-3 border border-accent/20" style="background:#FEF9EC">
                <p class="text-2xl font-bold text-accent">{{ number_format($user->getTotalPoin()) }}</p>
                <p class="text-xs text-text-secondary mt-0.5">Total Poin</p>
            </div>
        </div>
    </div>
</div>

{{-- Breakdown Poin --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-2xl p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:#EFF6FF">
            <i data-lucide="book-open" class="w-6 h-6 text-primary"></i>
        </div>
        <div>
            <p class="text-xs text-text-secondary font-semibold uppercase tracking-wider">Poin dari Membaca Materi</p>
            <p class="text-3xl font-bold text-primary mt-0.5">{{ number_format($totalPoinMateri) }}</p>
            <p class="text-xs text-text-secondary mt-0.5">dari {{ $progresMateri->count() }} materi selesai</p>
        </div>
    </div>
    <div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-2xl p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:#FEF9EC">
            <i data-lucide="star" class="w-6 h-6 text-accent"></i>
        </div>
        <div>
            <p class="text-xs text-text-secondary font-semibold uppercase tracking-wider">Poin dari Kuis (Skor Tertinggi)</p>
            <p class="text-3xl font-bold text-accent mt-0.5">{{ number_format($totalPoinEvaluasi) }}</p>
            <p class="text-xs text-text-secondary mt-0.5">dari {{ $sesis->unique('materi_id')->count() }} kuis</p>
        </div>
    </div>
</div>

{{-- Riwayat Materi Dibaca --}}
<div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-2xl overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-border bg-secondary/50 flex items-center gap-3">
        <i data-lucide="book-open" class="w-5 h-5 text-primary"></i>
        <h2 class="font-bold text-text-primary">Materi yang Telah Diselesaikan</h2>
        <span class="ml-auto text-xs bg-primary/10 text-primary font-bold px-2.5 py-1 rounded-full">{{ $progresMateri->count() }} Materi</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-border">
                    <th class="py-3 px-6 text-xs font-semibold text-text-secondary uppercase tracking-wider">Judul Materi</th>
                    <th class="py-3 px-6 text-xs font-semibold text-text-secondary uppercase tracking-wider">Jenis</th>
                    <th class="py-3 px-6 text-xs font-semibold text-text-secondary uppercase tracking-wider">Tanggal Selesai</th>
                    <th class="py-3 px-6 text-xs font-semibold text-text-secondary uppercase tracking-wider text-center">Poin Didapat</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse($progresMateri as $progres)
                <tr class="hover:bg-secondary/30 transition-colors">
                    <td class="py-3 px-6">
                        <p class="text-sm font-semibold text-text-primary">{{ $progres->materi->judul ?? 'Materi Dihapus' }}</p>
                    </td>
                    <td class="py-3 px-6">
                        @php
                            $jenisBadge = match($progres->materi->jenis ?? '') {
                                'pdf' => ['label' => 'PDF', 'class' => 'bg-violet-900/30 text-[#fcd34d]'],
                                'ppt', 'pptx' => ['label' => 'PPT', 'class' => 'bg-orange-100 text-orange-700'],
                                'video_embed' => ['label' => 'Video', 'class' => 'bg-purple-100 text-purple-700'],
                                'link' => ['label' => 'Link', 'class' => 'bg-[#13161c] text-gray-300'],
                                default => ['label' => '-', 'class' => 'bg-secondary text-text-secondary'],
                            };
                        @endphp
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $jenisBadge['class'] }}">{{ $jenisBadge['label'] }}</span>
                    </td>
                    <td class="py-3 px-6 text-sm text-text-secondary">
                        {{ $progres->tanggal_selesai ? \Carbon\Carbon::parse($progres->tanggal_selesai)->format('d M Y') : '-' }}
                    </td>
                    <td class="py-3 px-6 text-center">
                        <span class="px-3 py-1 bg-accent/10 text-accent text-sm font-bold rounded-full">
                            +{{ $progres->materi->poin ?? 0 }} poin
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-8 text-center text-sm text-text-secondary">Belum ada materi yang diselesaikan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Riwayat Kuis --}}
<div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-2xl overflow-hidden">
    <div class="px-6 py-4 border-b border-border bg-secondary/50 flex items-center gap-3">
        <i data-lucide="clipboard-list" class="w-5 h-5 text-primary"></i>
        <h2 class="font-bold text-text-primary">Riwayat Kuis yang Dikerjakan</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-border">
                    <th class="py-3 px-6 text-xs font-semibold text-text-secondary uppercase tracking-wider">Judul Kuis</th>
                    <th class="py-3 px-6 text-xs font-semibold text-text-secondary uppercase tracking-wider">Waktu Selesai</th>
                    <th class="py-3 px-6 text-xs font-semibold text-text-secondary uppercase tracking-wider text-center">Durasi</th>
                    <th class="py-3 px-6 text-xs font-semibold text-text-secondary uppercase tracking-wider text-center">Benar / Total</th>
                    <th class="py-3 px-6 text-xs font-semibold text-text-secondary uppercase tracking-wider text-center">Nilai</th>
                    <th class="py-3 px-6 text-xs font-semibold text-text-secondary uppercase tracking-wider text-center">Status</th>
                    <th class="py-3 px-6 text-xs font-semibold text-text-secondary uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse($sesis as $sesi)
                <tr class="hover:bg-secondary/30 transition-colors">
                    <td class="py-4 px-6">
                        <p class="text-sm font-semibold text-text-primary">{{ $sesi->materi->judul ?? 'Kuis Dihapus' }}</p>
                        @if($sesi->materi)
                            <p class="text-xs text-text-secondary mt-0.5">Passing Grade: {{ $sesi->passing_grade }}%</p>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-sm text-text-secondary whitespace-nowrap">
                        {{ $sesi->selesai_at ? $sesi->selesai_at->format('d M Y, H:i') : '-' }}
                    </td>
                    <td class="py-4 px-6 text-center text-sm text-text-secondary">
                        {{ $sesi->durasi_menit !== null ? $sesi->durasi_menit . ' mnt' : '-' }}
                    </td>
                    <td class="py-4 px-6 text-center">
                        <span class="text-sm font-semibold text-text-primary">
                            {{ $sesi->benar ?? 0 }} / {{ $sesi->total_soal ?? 0 }}
                        </span>
                    </td>
                    <td class="py-4 px-6 text-center">
                        <span class="text-xl font-bold {{ $sesi->skor >= $sesi->passing_grade ? 'text-success' : 'text-danger' }}">
                            {{ number_format($sesi->skor, 1) }}
                        </span>
                    </td>
                    <td class="py-4 px-6 text-center">
                        @if($sesi->skor >= $sesi->passing_grade)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-success/10 text-success text-xs font-bold rounded-full">
                                <i data-lucide="check-circle" class="w-3 h-3"></i> Lulus
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-danger/10 text-danger text-xs font-bold rounded-full">
                                <i data-lucide="x-circle" class="w-3 h-3"></i> Tidak Lulus
                            </span>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-right">
                        @if($sesi->materi)
                        <a href="{{ route('admin.kuis.jawaban', [$sesi->materi_id, $sesi->id]) }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-[var(--card)] border border-[var(--border)] shadow-xs hover:bg-secondary text-text-primary text-xs font-semibold rounded-lg transition-colors">
                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                            Review Jawaban
                        </a>
                        @else
                        <span class="text-xs text-text-secondary">â€”</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-12 text-center text-text-secondary">
                        <div class="w-14 h-14 bg-secondary rounded-full flex items-center justify-center mx-auto mb-3">
                            <i data-lucide="clipboard-x" class="w-7 h-7 opacity-40"></i>
                        </div>
                        <p class="text-sm font-medium">Peserta belum mengerjakan kuis apapun.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

