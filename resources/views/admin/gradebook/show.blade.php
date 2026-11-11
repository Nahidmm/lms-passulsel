@extends('layouts.app')

@section('title', 'Detail Raport: ' . $user->nama)

@section('content')
@php
    $componentLabels = [
        'quiz' => 'Seluruh Kuis',
        'tugas' => 'Seluruh Tugas',
        'mental' => 'Mental',
        'fisik' => 'Fisik',
        'akhlak' => 'Akhlak',
        'kompetensi' => 'Kompetensi',
    ];
    $itemTypeLabels = [
        'pretest' => 'Pretest',
        'quiz' => 'Kuis',
        'tugas' => 'Tugas',
        'posttest' => 'Posttest',
    ];
    $enrolledCourses = collect($courseScores)->where('enrolled', true);
    $status = $globalSummary['status_kelulusan'];
@endphp

<div class="mb-5 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.gradebook.index', ['pelatihan_id' => 'all']) }}" class="flex h-9 w-9 items-center justify-center rounded-lg border border-border bg-[var(--card)] text-text-secondary hover:bg-secondary hover:text-primary">
            <i data-lucide="arrow-left" class="h-4 w-4"></i>
        </a>
        <div>
            <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary">Detail Raport</span>
            <h1 class="mt-0.5 text-xl font-bold text-text-primary">{{ $user->nama }}</h1>
            <p class="text-[10px] text-text-secondary">NIP {{ $user->nip ?? '-' }}</p>
        </div>
    </div>
    <a href="{{ route('admin.penilaian.show', $user->id) }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-border bg-[var(--card)] px-3 py-2 text-xs font-semibold text-primary hover:bg-secondary">
        <i data-lucide="scroll-text" class="h-4 w-4"></i> Buka Transkrip
    </a>
</div>

<div class="mb-5 rounded-xl border border-border bg-[var(--card)] p-5">
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-[1.5fr_1.5fr_1fr_1fr] lg:items-center">
        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-primary/10 text-sm font-bold text-primary">{{ strtoupper(substr($user->nama, 0, 2)) }}</div>
            <div>
                <span class="block text-[9px] font-bold uppercase tracking-wide text-text-secondary">Peserta</span>
                <span class="text-sm font-bold text-text-primary">{{ $user->nama }}</span>
            </div>
        </div>
        <div>
            <span class="block text-[9px] font-bold uppercase tracking-wide text-text-secondary">Unit Kerja</span>
            <span class="mt-1 block text-sm font-medium text-text-primary">{{ $user->unitKerja->nama ?? $user->unitKerja->nama_unit ?? '-' }}</span>
        </div>
        <div>
            <span class="block text-[9px] font-bold uppercase tracking-wide text-text-secondary">Course Diikuti</span>
            <span class="mt-1 block text-sm font-bold text-text-primary">{{ $enrolledCourses->count() }}</span>
        </div>
        <div class="flex items-center justify-between gap-3 lg:justify-end">
            <div class="text-right">
                <span class="block text-[9px] font-bold uppercase tracking-wide text-text-secondary">Nilai Akhir</span>
                <span class="text-2xl font-extrabold text-primary">{{ number_format($globalSummary['nilai_akhir'], 1) }}</span>
                <span class="ml-1 text-xs font-bold text-text-secondary">{{ $globalSummary['predikat'] }}</span>
            </div>
            @if($status === 'lulus')
                <span class="rounded-full bg-green-100 px-3 py-1 text-[10px] font-bold text-green-700">Lulus</span>
            @elseif($status === 'tidak_lulus')
                <span class="rounded-full bg-red-100 px-3 py-1 text-[10px] font-bold text-red-700">Tidak Lulus</span>
            @else
                <span class="rounded-full bg-amber-100 px-3 py-1 text-[10px] font-bold text-amber-700">Belum Lengkap</span>
            @endif
        </div>
    </div>
</div>

<div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_340px]">
    <section class="space-y-4">
        <div>
            <h2 class="text-sm font-bold text-text-primary">Nilai Seluruh Course</h2>
            <p class="mt-0.5 text-[10px] text-text-secondary">Nilai mentah setiap komponen sebelum pembobotan Nilai Akhir.</p>
        </div>

        @forelse($enrolledCourses as $course)
            <div class="overflow-hidden rounded-xl border border-border bg-[var(--card)]">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-border bg-secondary/30 px-5 py-4">
                    <div>
                        <h3 class="text-sm font-bold text-text-primary">{{ $course['pelatihan']->judul }}</h3>
                        <p class="mt-0.5 text-[10px] text-text-secondary">{{ count($course['items']) }} komponen penilaian</p>
                    </div>
                    <div class="text-right">
                        <span class="block text-[9px] font-bold uppercase tracking-wide text-text-secondary">Nilai Course</span>
                        <span class="text-xl font-extrabold text-primary">{{ $course['nilai_akhir'] !== null ? number_format($course['nilai_akhir'], 1) : '-' }}</span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[520px] text-left text-xs">
                        <thead class="border-b border-border text-[9px] uppercase tracking-wide text-text-secondary">
                            <tr>
                                <th class="w-12 px-5 py-3 text-center">No</th>
                                <th class="px-4 py-3">Komponen Penilaian</th>
                                <th class="w-28 px-4 py-3 text-center">Jenis</th>
                                <th class="w-24 px-5 py-3 text-right">Nilai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse($course['items'] as $item)
                                <tr>
                                    <td class="px-5 py-3 text-center text-text-secondary">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3 font-medium text-text-primary">{{ $item['label'] }}</td>
                                    <td class="px-4 py-3 text-center"><span class="rounded bg-secondary px-2 py-1 text-[9px] font-semibold text-text-secondary">{{ $itemTypeLabels[$item['type']] ?? ucfirst($item['type']) }}</span></td>
                                    <td class="px-5 py-3 text-right font-bold {{ $item['score'] !== null ? 'text-text-primary' : 'text-amber-600' }}">{{ $item['score'] !== null ? number_format($item['score'], 1) : '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-5 py-8 text-center text-text-secondary">Belum ada komponen penilaian.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-border bg-[var(--card)] px-5 py-16 text-center text-xs text-text-secondary">Peserta belum mengikuti course.</div>
        @endforelse
    </section>

    <aside class="h-fit rounded-xl border border-border bg-[var(--card)] p-5 xl:sticky xl:top-4">
        <div class="mb-4 flex items-center justify-between border-b border-border pb-3">
            <div>
                <h2 class="text-sm font-bold text-text-primary">Rangkuman Nilai Akhir</h2>
                <p class="mt-0.5 text-[9px] text-text-secondary">Bobot diterapkan satu kali.</p>
            </div>
            <span class="text-[10px] font-semibold text-text-secondary">PG {{ number_format($gradebookSettings->passing_grade, 1) }}</span>
        </div>

        <div class="mb-2 grid grid-cols-[1fr_44px_62px_48px] gap-2 px-2 text-[8px] font-bold uppercase tracking-wide text-text-secondary">
            <span>Komponen</span><span class="text-right">Nilai</span><span class="text-right">Bobot</span><span class="text-right">Hasil</span>
        </div>
        <div class="space-y-1.5">
            @foreach($componentLabels as $key => $label)
                @php
                    $score = $globalSummary['scores'][$key] ?? null;
                    $effectiveWeight = $globalSummary['effective_weights'][$key] ?? 0;
                    $contribution = $globalSummary['contributions'][$key] ?? 0;
                @endphp
                <div class="grid grid-cols-[1fr_44px_62px_48px] items-center gap-2 rounded px-2 py-2 text-[10px] odd:bg-secondary/40">
                    <span class="font-medium text-text-primary">{{ $label }}</span>
                    <span class="text-right font-bold text-text-primary">{{ $score !== null ? number_format($score, 1) : '-' }}</span>
                    <span class="text-right text-text-secondary">{{ number_format($effectiveWeight, 1) }}%</span>
                    <span class="text-right font-bold text-primary">{{ number_format($contribution, 1) }}</span>
                </div>
            @endforeach
        </div>

        <div class="mt-5 rounded-xl border border-primary/20 bg-primary/5 p-5 text-center">
            <span class="block text-[9px] font-bold uppercase tracking-[0.18em] text-primary">Nilai Akhir</span>
            <span class="mt-1 block text-4xl font-extrabold text-primary">{{ number_format($globalSummary['nilai_akhir'], 1) }}</span>
            <span class="text-sm font-bold text-text-primary">Predikat {{ $globalSummary['predikat'] }}</span>
            <p class="mt-2 text-[9px] text-text-secondary">{{ $globalSummary['item_selesai'] }} dari {{ $globalSummary['total_item'] }} item selesai</p>
        </div>
    </aside>
</div>
@endsection
