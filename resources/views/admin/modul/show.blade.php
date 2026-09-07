@extends('layouts.app')

@section('title', 'Detail Modul: ' . $modul->judul)

@section('content')

<div class="mb-5">
    <a href="{{ route('admin.modul.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-[var(--text-secondary)] hover:text-primary transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Kembali ke Daftar Modul</span>
    </a>
</div>

<!-- Modul Header Card -->
<div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-2xl p-6 md:p-8 mb-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="space-y-1.5 max-w-2xl">
            <div class="inline-flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary/10 text-primary border border-primary/20">
                    Modul {{ $modul->urutan }}
                </span>
                @if($modul->is_active)
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                        Aktif
                    </span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-zinc-500/10 text-zinc-600 dark:text-zinc-400 border border-zinc-500/20">
                        Draft
                    </span>
                @endif
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-[var(--text-primary)]">{{ $modul->judul }}</h1>
            <p class="text-sm text-[var(--text-secondary)] leading-relaxed">{{ $modul->deskripsi ?: 'Belum ada deskripsi untuk modul ini.' }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.modul.edit', $modul->id) }}" class="btn btn-secondary text-[var(--text-primary)] font-medium py-2 px-4 rounded-xl flex items-center gap-2 transition-all">
                <i data-lucide="pencil" class="w-4 h-4"></i> Edit Modul
            </a>
            <a href="{{ route('admin.modul.materi.create', $modul->id) }}" class="btn btn-primary text-white font-medium py-2 px-4 rounded-xl flex items-center gap-2 shadow-xs transition-all">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Materi / Kuis
            </a>
        </div>
    </div>
</div>

<!-- Materi & Kuis List -->
<div class="mb-4 flex items-center justify-between">
    <div>
        <h2 class="text-lg font-bold tracking-tight text-[var(--text-primary)]">Materi & Evaluasi Modul</h2>
        <p class="text-xs text-[var(--text-secondary)] mt-0.5">Daftar urutan konten yang dipelajari peserta pada modul ini.</p>
    </div>
</div>

<div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-2xl overflow-hidden">
    @if($materis->isEmpty())
        <div class="py-16 text-center text-[var(--text-secondary)]">
            <i data-lucide="layers" class="w-10 h-10 mx-auto mb-3 text-[var(--text-muted)]"></i>
            <p class="font-medium">Belum ada materi atau evaluasi di modul ini.</p>
            <p class="text-xs text-[var(--text-muted)] mt-1">Gunakan tombol "Tambah Materi / Kuis" di atas untuk menambahkan bahan ajar.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-[var(--text-secondary)] uppercase bg-[var(--muted)]/50 border-b border-[var(--border)]">
                    <tr>
                        <th class="px-6 py-3.5 w-20 text-center">Urutan</th>
                        <th class="px-6 py-3.5">Judul Materi</th>
                        <th class="px-6 py-3.5 w-36">Jenis Konten</th>
                        <th class="px-6 py-3.5 w-28 text-center">Durasi</th>
                        <th class="px-6 py-3.5 w-28 text-center">Status</th>
                        <th class="px-6 py-3.5 w-44 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    @foreach($materis as $materi)
                        <tr class="hover:bg-[var(--muted)]/30 transition-colors">
                            <td class="px-6 py-4 text-center">
                                <span class="w-7 h-7 rounded-lg bg-[var(--muted)] flex items-center justify-center font-bold text-xs text-[var(--text-primary)] mx-auto border border-[var(--border)]">
                                    {{ $materi->urutan }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-[var(--text-primary)] text-sm">{{ $materi->judul }}</p>
                                @if($materi->jenis === 'quiz')
                                    <p class="text-xs text-primary font-medium mt-0.5 flex items-center gap-1">
                                        <i data-lucide="help-circle" class="w-3.5 h-3.5"></i>
                                        {{ $materi->soals->count() }} Butir Soal Terdaftar
                                    </p>
                                @else
                                    <p class="text-xs text-[var(--text-secondary)] line-clamp-1 mt-0.5">{{ $materi->deskripsi }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[var(--muted)] text-[var(--text-primary)] text-xs font-medium border border-[var(--border)]">
                                    @if($materi->jenis === 'video_embed') <i data-lucide="video" class="w-3.5 h-3.5 text-blue-500"></i> Video
                                    @elseif($materi->jenis === 'link') <i data-lucide="link" class="w-3.5 h-3.5 text-emerald-500"></i> Link
                                    @elseif($materi->jenis === 'quiz') <i data-lucide="help-circle" class="w-3.5 h-3.5 text-amber-500"></i> Kuis
                                    @else <i data-lucide="file-text" class="w-3.5 h-3.5 text-primary"></i> {{ ucfirst($materi->jenis) }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-xs text-[var(--text-secondary)]">
                                {{ $materi->durasi_baca }} mnt
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($materi->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-zinc-500/10 text-zinc-600 dark:text-zinc-400 border border-zinc-500/20">Draft</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-1">
                                @if($materi->jenis === 'quiz')
                                    <a href="{{ route('admin.materi.soal.create', $materi->id) }}" class="inline-flex p-2 border border-[var(--border)] text-primary hover:border-primary rounded-xl transition-all hover:bg-primary/10" title="Kelola Butir Soal">
                                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                                    </a>
                                @endif
                                <a href="{{ route('admin.materi.edit', $materi->id) }}" class="inline-flex p-2 border border-[var(--border)] text-[var(--text-secondary)] hover:text-primary hover:border-primary rounded-xl transition-all hover:bg-[var(--card)]" title="Edit Materi">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('admin.materi.destroy', $materi->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus materi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex p-2 border border-[var(--border)] text-[var(--text-secondary)] hover:text-danger hover:border-danger rounded-xl transition-all hover:bg-danger/10" title="Hapus">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        
                        @if($materi->jenis === 'quiz' && $materi->soals->isNotEmpty())
                            <tr class="bg-[var(--muted)]/20">
                                <td colspan="6" class="p-4 pl-12 md:pl-20">
                                    <div class="bg-[var(--card)] border border-[var(--border)] rounded-xl overflow-hidden shadow-2xs">
                                        <div class="px-4 py-2.5 bg-[var(--muted)]/40 border-b border-[var(--border)] flex items-center justify-between">
                                            <span class="text-xs font-bold text-[var(--text-primary)] uppercase tracking-wider flex items-center gap-1.5">
                                                <i data-lucide="list-checks" class="w-3.5 h-3.5 text-primary"></i>
                                                Daftar Butir Soal Kuis
                                            </span>
                                            <a href="{{ route('admin.materi.soal.create', $materi->id) }}" class="text-xs text-primary hover:underline font-medium inline-flex items-center gap-1">
                                                <i data-lucide="plus" class="w-3 h-3"></i> Tambah Soal
                                            </a>
                                        </div>
                                        <table class="w-full text-xs text-left">
                                            <tbody class="divide-y divide-[var(--border)]">
                                                @foreach($materi->soals as $idx => $soal)
                                                <tr class="hover:bg-[var(--muted)]/20">
                                                    <td class="px-4 py-2.5 w-10 text-center text-[var(--text-secondary)] font-mono">{{ $idx + 1 }}</td>
                                                    <td class="px-4 py-2.5 text-[var(--text-primary)] font-medium">{{ Str::limit($soal->pertanyaan, 90) }}</td>
                                                    <td class="px-4 py-2.5 w-24 text-center text-[var(--text-secondary)]">Bobot: {{ $soal->bobot }}</td>
                                                    <td class="px-4 py-2.5 w-28 text-right space-x-2">
                                                        <a href="{{ route('admin.soal.edit', $soal->id) }}" class="text-primary hover:underline">Edit</a>
                                                        <form action="{{ route('admin.soal.destroy', $soal->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus soal ini?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="text-danger hover:underline">Hapus</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection
