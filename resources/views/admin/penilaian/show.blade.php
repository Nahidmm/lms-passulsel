@extends('layouts.app')

@section('title', 'Detail Penilaian - ' . $user->nama)

@section('content')

{{-- Back Navigation --}}
<div class="mb-5">
    <a href="{{ route('admin.gradebook.index') }}" class="text-text-secondary hover:text-primary flex items-center gap-1.5 font-medium transition-colors w-fit">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Buku Nilai (Gradebook)
    </a>
</div>

{{-- Flash Messages --}}
@if(session('success'))
<div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-400 flex items-center gap-3 text-sm">
    <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-700 dark:text-rose-400 flex items-center gap-3 text-sm">
    <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
    <span>{{ session('error') }}</span>
</div>
@endif

{{-- Profil Peserta --}}
<div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-2xl p-6 mb-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
        <img src="{{ $user->avatar_url }}" alt="{{ $user->nama }}" class="w-20 h-20 rounded-2xl object-cover border-2 border-border shadow-sm shrink-0">
        <div class="flex-1 min-w-0">
            <h1 class="text-2xl font-bold text-text-primary truncate">{{ $user->nama }}</h1>
            <p class="text-text-secondary text-sm mt-0.5">{{ $user->nip }} &bull; {{ $user->jabatan->nama_jabatan ?? '-' }}</p>
        </div>
        {{-- Summary Cards --}}
        <div class="flex gap-3 flex-wrap">
            <div class="text-center bg-secondary rounded-xl px-4 py-2.5 border border-border min-w-[90px]">
                <p class="text-xl font-bold text-primary">{{ $sesis->count() }}</p>
                <p class="text-[11px] text-text-secondary mt-0.5">Kuis Dikerjakan</p>
            </div>
            <div class="text-center bg-secondary rounded-xl px-4 py-2.5 border border-border min-w-[90px]">
                <p class="text-xl font-bold {{ $rataRata >= 70 ? 'text-success' : ($rataRata > 0 ? 'text-warning' : 'text-text-secondary') }}">
                    {{ $rataRata > 0 ? number_format($rataRata, 1) : '-' }}
                </p>
                <p class="text-[11px] text-text-secondary mt-0.5">Rata Kuis</p>
            </div>
            <div class="text-center bg-secondary rounded-xl px-4 py-2.5 border border-border min-w-[90px]">
                <p class="text-xl font-bold text-indigo-600">{{ $totalTugasDinilai }}/{{ $totalTugas }}</p>
                <p class="text-[11px] text-text-secondary mt-0.5">Tugas Dinilai</p>
            </div>
            <div class="text-center bg-secondary rounded-xl px-4 py-2.5 border border-border min-w-[90px]">
                <p class="text-xl font-bold {{ $rataTugas >= 70 ? 'text-success' : ($rataTugas > 0 ? 'text-warning' : 'text-text-secondary') }}">
                    {{ $totalTugasDinilai > 0 ? number_format($rataTugas, 1) : '-' }}
                </p>
                <p class="text-[11px] text-text-secondary mt-0.5">Rata Tugas</p>
            </div>
            <div class="text-center bg-secondary rounded-xl px-4 py-2.5 border border-border min-w-[90px]">
                <p class="text-xl font-bold text-primary">{{ $progresMateri->count() }}</p>
                <p class="text-[11px] text-text-secondary mt-0.5">Materi Selesai</p>
            </div>
            <div class="text-center rounded-xl px-4 py-2.5 border border-accent/20 min-w-[90px]" style="background:#FEF9EC">
                <p class="text-xl font-bold text-accent">{{ number_format($user->getTotalPoin()) }}</p>
                <p class="text-[11px] text-text-secondary mt-0.5">Total Poin</p>
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

{{-- 1. Riwayat Materi Dibaca --}}
<div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-2xl overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-border bg-secondary/50 flex items-center gap-3">
        <i data-lucide="book-open" class="w-5 h-5 text-primary"></i>
        <h2 class="font-bold text-text-primary">Materi yang Telah Diselesaikan</h2>
        <span class="ml-auto text-xs bg-primary/10 text-primary font-bold px-2.5 py-1 rounded-full">{{ $progresMateri->count() }} Materi</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-border text-[11px] font-semibold text-text-secondary uppercase tracking-wider bg-secondary/20">
                    <th class="py-3 px-6">Judul Materi</th>
                    <th class="py-3 px-6">Jenis</th>
                    <th class="py-3 px-6">Tanggal Selesai</th>
                    <th class="py-3 px-6 text-center">Poin Didapat</th>
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
                        {{ $progres->tanggal_selesai ? \Carbon\Carbon::parse($progres->tanggal_selesai)->translatedFormat('d M Y') : '-' }}
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

{{-- 2. Riwayat Tugas & Penugasan Kasus yang Dikumpulkan / Dinilai --}}
<div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-2xl overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-border bg-secondary/50 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <i data-lucide="file-check" class="w-5 h-5 text-indigo-600"></i>
            <div>
                <h2 class="font-bold text-text-primary">Tugas & Telaah Kasus yang Dikumpulkan / Dinilai</h2>
                <p class="text-xs text-text-secondary mt-0.5">Daftar penugasan, berkas yang diunggah peserta, nilai, serta umpan balik instruktur.</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs bg-indigo-500/10 text-indigo-600 font-bold px-2.5 py-1 rounded-full">
                {{ $totalTugasDinilai }} / {{ $totalTugas }} Tugas Dinilai
            </span>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-border text-[11px] font-semibold text-text-secondary uppercase tracking-wider bg-secondary/20">
                    <th class="py-3 px-6">Judul Tugas & Pelatihan</th>
                    <th class="py-3 px-4">Tipe Tugas</th>
                    <th class="py-3 px-4">Berkas / Jawaban</th>
                    <th class="py-3 px-4">Waktu Kumpul</th>
                    <th class="py-3 px-4 text-center">Nilai</th>
                    <th class="py-3 px-4 text-center">Status</th>
                    <th class="py-3 px-6">Feedback & Penilai</th>
                    <th class="py-3 px-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border text-xs">
                @forelse($tugasSubmissions as $sub)
                <tr class="hover:bg-secondary/30 transition-colors">
                    <td class="py-4 px-6">
                        <p class="font-bold text-text-primary text-sm">{{ $sub->tugas->judul ?? 'Tugas Dihapus' }}</p>
                        <p class="text-[11px] text-text-secondary mt-0.5">{{ $sub->tugas->pelatihan->judul ?? '-' }}</p>
                    </td>
                    <td class="py-4 px-4 whitespace-nowrap">
                        @if(($sub->tugas->tipe ?? '') === 'upload_sertifikat')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-500/10 text-blue-600 border border-blue-500/20">
                                <i data-lucide="award" class="w-3 h-3"></i> Sertifikat Eksternal
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-600 border border-amber-500/20">
                                <i data-lucide="file-text" class="w-3 h-3"></i> Telaah Kasus
                            </span>
                        @endif
                    </td>
                    <td class="py-4 px-4">
                        @if($sub->file_path)
                            <a href="{{ Storage::url($sub->file_path) }}" target="_blank" class="inline-flex items-center gap-1.5 text-primary hover:underline font-semibold max-w-[180px] truncate" title="{{ $sub->file_nama_asli }}">
                                <i data-lucide="paperclip" class="w-3.5 h-3.5 shrink-0"></i>
                                <span class="truncate">{{ $sub->file_nama_asli ?: 'Lihat Berkas' }}</span>
                            </a>
                        @else
                            <span class="text-text-secondary italic">Tidak ada file</span>
                        @endif
                        @if($sub->catatan_peserta)
                            <p class="text-[11px] text-text-secondary mt-1 bg-secondary/50 p-1.5 rounded-md italic max-w-xs truncate" title="{{ $sub->catatan_peserta }}">
                                "{{ $sub->catatan_peserta }}"
                            </p>
                        @endif
                        @if($sub->nomor_sertifikat)
                            <p class="text-[10px] text-text-secondary mt-0.5 font-mono">No: {{ $sub->nomor_sertifikat }}</p>
                        @endif
                    </td>
                    <td class="py-4 px-4 whitespace-nowrap text-text-secondary">
                        {{ $sub->created_at ? $sub->created_at->translatedFormat('d M Y, H:i') : '-' }}
                        @if($sub->is_late)
                            <span class="block text-[10px] font-bold text-rose-500">Terlambat</span>
                        @endif
                    </td>
                    <td class="py-4 px-4 text-center">
                        @if($sub->status === 'graded')
                            <span class="text-base font-bold {{ $sub->nilai >= 70 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($sub->nilai, 1) }}
                            </span>
                            <span class="text-[10px] text-text-secondary block">/ {{ $sub->tugas->skor_maksimal ?? 100 }}</span>
                        @else
                            <span class="text-text-secondary text-sm font-semibold">-</span>
                        @endif
                    </td>
                    <td class="py-4 px-4 text-center whitespace-nowrap">
                        @if($sub->status === 'graded')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 text-[11px] font-bold rounded-full">
                                <i data-lucide="check-circle" class="w-3 h-3"></i> Dinilai
                            </span>
                        @elseif($sub->status === 'need_revision')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-rose-500/10 text-rose-600 border border-rose-500/20 text-[11px] font-bold rounded-full">
                                <i data-lucide="alert-circle" class="w-3 h-3"></i> Perlu Revisi
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-500/10 text-amber-600 border border-amber-500/20 text-[11px] font-bold rounded-full">
                                <i data-lucide="clock" class="w-3 h-3"></i> Menunggu Penilaian
                            </span>
                        @endif
                    </td>
                    <td class="py-4 px-6 min-w-[180px]">
                        @if($sub->feedback_instruktur)
                            <p class="text-text-primary text-xs leading-relaxed italic">
                                "{{ $sub->feedback_instruktur }}"
                            </p>
                        @else
                            <span class="text-text-secondary text-[11px] italic">Belum ada catatan</span>
                        @endif
                        @if($sub->penilai)
                            <p class="text-[10px] text-text-secondary mt-1 flex items-center gap-1">
                                <i data-lucide="user-check" class="w-3 h-3"></i> {{ $sub->penilai->nama }} &bull; {{ $sub->dinilai_at ? \Carbon\Carbon::parse($sub->dinilai_at)->translatedFormat('d M Y') : '' }}
                            </p>
                        @endif
                    </td>
                    <td class="py-4 px-4 text-right whitespace-nowrap">
                        @if($sub->tugas_id)
                            <a href="{{ route('admin.tugas.submissions', $sub->tugas_id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[var(--card)] border border-[var(--border)] shadow-xs hover:bg-secondary text-text-primary text-xs font-semibold rounded-lg transition-colors">
                                <i data-lucide="edit-3" class="w-3.5 h-3.5 text-primary"></i> Review / Nilai
                            </a>
                        @else
                            <span class="text-xs text-text-secondary">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-10 text-center text-text-secondary">
                        <div class="w-12 h-12 bg-secondary rounded-full flex items-center justify-center mx-auto mb-2">
                            <i data-lucide="inbox" class="w-6 h-6 opacity-40"></i>
                        </div>
                        <p class="text-sm font-medium">Peserta belum mengumpulkan tugas atau telaah kasus apapun.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- 3. Riwayat Kuis yang Dikerjakan --}}
<div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-2xl overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-border bg-secondary/50 flex items-center gap-3">
        <i data-lucide="clipboard-list" class="w-5 h-5 text-primary"></i>
        <h2 class="font-bold text-text-primary">Riwayat Kuis yang Dikerjakan</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-border text-[11px] font-semibold text-text-secondary uppercase tracking-wider bg-secondary/20">
                    <th class="py-3 px-6">Judul Kuis</th>
                    <th class="py-3 px-6">Waktu Selesai</th>
                    <th class="py-3 px-6 text-center">Durasi</th>
                    <th class="py-3 px-6 text-center">Benar / Total</th>
                    <th class="py-3 px-6 text-center">Nilai</th>
                    <th class="py-3 px-6 text-center">Status</th>
                    <th class="py-3 px-6 text-right">Aksi</th>
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
                        {{ $sesi->selesai_at ? $sesi->selesai_at->translatedFormat('d M Y, H:i') : '-' }}
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
                        <span class="text-xs text-text-secondary">-</span>
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

{{-- 4. Status Kelulusan Pelatihan & Penerbitan Sertifikat Peserta --}}
<div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-2xl overflow-hidden">
    <div class="px-6 py-4 border-b border-border bg-secondary/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <i data-lucide="award" class="w-5 h-5 text-amber-500 shrink-0"></i>
            <div>
                <h2 class="font-bold text-text-primary text-sm sm:text-base">Status Kelulusan & Penerbitan Sertifikat Peserta</h2>
                <p class="text-xs text-text-secondary mt-0.5">Admin dapat menerbitkan, mengunduh, atau mencabut sertifikat kelulusan peserta secara manual.</p>
            </div>
        </div>
        <div class="shrink-0">
            @if($certSetting && !$certSetting->is_active)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-500/10 text-rose-600 border border-rose-500/20">
                    <i data-lucide="alert-octagon" class="w-3.5 h-3.5"></i> Penerbitan Nonaktif
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                    <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Penerbitan Aktif ({{ ($certSetting?->generation_mode ?? 'admin_only') === 'admin_only' ? 'Manual oleh Admin' : 'Otomatis' }})
                </span>
            @endif
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="border-b border-border text-[11px] font-semibold text-text-secondary uppercase tracking-wider bg-secondary/20">
                    <th class="py-3 px-6">Pelatihan / Kursus</th>
                    <th class="py-3 px-4 text-center">Status Belajar</th>
                    <th class="py-3 px-4">Tanggal Selesai</th>
                    <th class="py-3 px-4">Nomor Sertifikat (Credential)</th>
                    <th class="py-3 px-4 text-center">Status Sertifikat</th>
                    <th class="py-3 px-6 text-right">Aksi Admin</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse($progresPelatihans as $pp)
                    @php
                        $userCert = $sertifikats->firstWhere('pelatihan_id', $pp->pelatihan_id);
                    @endphp
                    <tr class="hover:bg-secondary/30 transition-colors">
                        <td class="py-4 px-6 font-semibold text-text-primary text-sm">
                            {{ $pp->pelatihan->judul ?? 'Pelatihan Dihapus' }}
                        </td>
                        <td class="py-4 px-4 text-center whitespace-nowrap">
                            @if($pp->status === 'selesai')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                                    <i data-lucide="check" class="w-3 h-3"></i> Selesai
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-500/10 text-amber-600 border border-amber-500/20">
                                    <i data-lucide="clock" class="w-3 h-3"></i> Sedang Belajar
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-text-secondary whitespace-nowrap">
                            {{ $pp->tanggal_selesai ? \Carbon\Carbon::parse($pp->tanggal_selesai)->translatedFormat('d M Y') : '-' }}
                        </td>
                        <td class="py-4 px-4 font-mono text-xs whitespace-nowrap">
                            @if($userCert)
                                <span class="font-bold text-primary">{{ $userCert->credential_id }}</span>
                                <span class="block text-[10px] text-text-secondary font-sans mt-0.5">Diterbitkan {{ $userCert->issued_at ? \Carbon\Carbon::parse($userCert->issued_at)->translatedFormat('d M Y') : '-' }}</span>
                            @else
                                <span class="text-text-secondary italic">Belum diterbitkan</span>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-center whitespace-nowrap">
                            @if($userCert)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30">
                                    <i data-lucide="award" class="w-3 h-3"></i> Terbit
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/20">
                                    Belum Terbit
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-right whitespace-nowrap">
                            <div class="inline-flex items-center justify-end gap-2">
                                @if($userCert)
                                    <a href="{{ route('peserta.sertifikat.download', $userCert->credential_id) }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs transition-colors">
                                        <i data-lucide="download" class="w-3.5 h-3.5"></i> Unduh PDF
                                    </a>
                                    <form action="{{ route('admin.sertifikat.destroy', $userCert->id) }}" method="POST"
                                          onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin mencabut/membatalkan sertifikat ini?\n\nPeserta tidak akan dapat mengunduh atau memverifikasi sertifikat ini lagi.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-600 hover:text-white text-xs font-semibold border border-rose-500/20 transition-all" title="Cabut Sertifikat">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.sertifikat.generate') }}" method="POST"
                                          onsubmit="return confirm('Terbitkan sertifikat kelulusan untuk peserta ini pada pelatihan ini?');">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                                        <input type="hidden" name="pelatihan_id" value="{{ $pp->pelatihan_id }}">
                                        <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-primary hover:bg-primary/90 text-white text-xs font-semibold shadow-xs transition-colors"
                                                {{ ($certSetting && !$certSetting->is_active) ? 'disabled title="Penerbitan dinonaktifkan"' : '' }}>
                                            <i data-lucide="award" class="w-3.5 h-3.5"></i> Terbitkan Sertifikat
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-text-secondary text-sm">
                            Peserta belum terdaftar di pelatihan manapun.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
