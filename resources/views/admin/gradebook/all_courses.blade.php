@extends('layouts.app')

@section('title', 'Buku Nilai Seluruh Course')

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
    $participantsWithCourses = collect($allCoursesData)->filter(fn ($row) => $row['totalEnrolled'] > 0);
    $globalAverage = $participantsWithCourses->isNotEmpty()
        ? $participantsWithCourses->avg(fn ($row) => $row['globalSummary']['nilai_akhir'])
        : 0;
    $globalPassed = collect($allCoursesData)
        ->filter(fn ($row) => $row['globalSummary']['status_kelulusan'] === 'lulus')
        ->count();
@endphp

<div class="mb-6 flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
    <div>
        <span class="inline-block rounded-full bg-primary/10 px-2.5 py-0.5 text-xs font-bold uppercase text-primary">Global Gradebook</span>
        <h1 class="mt-2 text-2xl font-bold text-text-primary">Buku Nilai Seluruh Course</h1>
        <p class="mt-1 text-xs text-text-secondary">Seluruh nilai mentah ditampilkan per course. Persentase diterapkan satu kali pada Nilai Akhir global.</p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <button type="button" onclick="const modal = document.getElementById('globalBobotModal'); modal.classList.remove('hidden'); modal.classList.add('flex')" class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-[var(--card)] px-3.5 py-2 text-xs font-semibold text-text-primary shadow-sm hover:bg-secondary">
            <i data-lucide="sliders" class="h-4 w-4 text-primary"></i> Atur Bobot Nilai Akhir
        </button>
        <a href="{{ route('admin.gradebook.export', ['unit_kerja_id' => request('unit_kerja_id')]) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-green-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-green-700">
            <i data-lucide="download" class="h-4 w-4"></i> Export Seluruh Nilai
        </a>
        <a href="{{ route('admin.gradebook.cetak', ['pelatihan_id' => 'all', 'unit_kerja_id' => request('unit_kerja_id')]) }}" target="_blank" class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700">
            <i data-lucide="printer" class="h-4 w-4"></i> Cetak
        </a>
    </div>
</div>

@if(session('success'))
    <div class="mb-6 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800">
        <i data-lucide="check-circle" class="h-5 w-5 shrink-0 text-green-600"></i><span>{{ session('success') }}</span>
    </div>
@endif
@if(session('error'))
    <div class="mb-6 flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
        <i data-lucide="alert-circle" class="h-5 w-5 shrink-0 text-red-600"></i><span>{{ session('error') }}</span>
    </div>
@endif

<div class="mb-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
    <div class="rounded-2xl border border-[var(--border)] bg-[var(--card)] p-4">
        <span class="block text-[11px] font-semibold uppercase text-text-secondary">Peserta Aktif</span>
        <span class="mt-1 block text-2xl font-bold text-text-primary">{{ count($allCoursesData) }}</span>
    </div>
    <div class="rounded-2xl border border-[var(--border)] bg-[var(--card)] p-4">
        <span class="block text-[11px] font-semibold uppercase text-text-secondary">Total Course</span>
        <span class="mt-1 block text-2xl font-bold text-primary">{{ $allPelatihans->count() }}</span>
    </div>
    <div class="rounded-2xl border border-[var(--border)] bg-[var(--card)] p-4">
        <span class="block text-[11px] font-semibold uppercase text-text-secondary">Rata-rata Nilai Akhir</span>
        <span class="mt-1 block text-2xl font-bold {{ $globalAverage >= $gradebookSettings->passing_grade ? 'text-emerald-600' : 'text-amber-600' }}">
            {{ $participantsWithCourses->isNotEmpty() ? number_format($globalAverage, 1) : '-' }}
        </span>
    </div>
    <div class="rounded-2xl border border-[var(--border)] bg-[var(--card)] p-4">
        <span class="block text-[11px] font-semibold uppercase text-text-secondary">Lulus Global</span>
        <span class="mt-1 block text-2xl font-bold text-indigo-600">{{ $globalPassed }}</span>
    </div>
</div>

<div class="mb-6 rounded-xl border border-primary/20 bg-primary/5 p-4">
    <div class="mb-3 flex items-center justify-between gap-3">
        <div>
            <h2 class="text-sm font-bold text-text-primary">Pembagian Nilai Akhir Global</h2>
            <p class="text-[11px] text-text-secondary">Pretest dan posttest tetap ditampilkan, tetapi tidak dibobotkan.</p>
        </div>
        <span class="rounded-full bg-[var(--card)] px-3 py-1 text-xs font-bold text-primary">Passing Grade {{ number_format($gradebookSettings->passing_grade, 1) }}</span>
    </div>
    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 xl:grid-cols-6">
        @foreach($componentLabels as $key => $label)
            <div class="rounded-lg border border-border bg-[var(--card)] px-3 py-2">
                <span class="block text-[10px] font-semibold uppercase text-text-secondary">{{ $label }}</span>
                <span class="text-lg font-bold text-text-primary">{{ number_format($gradebookSettings->{'bobot_' . $key}, 0) }}%</span>
            </div>
        @endforeach
    </div>
</div>

<div class="mb-6 rounded-xl border border-[var(--border)] bg-[var(--card)] p-4 shadow-xs">
    <form method="GET" action="{{ route('admin.gradebook.index') }}" class="flex flex-wrap items-end gap-3">
        <input type="hidden" name="pelatihan_id" value="all">
        <div class="min-w-[260px]">
            <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-text-secondary">Pilih Course</label>
            <select onchange="window.location.href = this.value === 'all' ? '{{ route('admin.gradebook.index') }}?pelatihan_id=all' : '{{ url('admin/pelatihan') }}/' + this.value + '/gradebook'" class="w-full rounded-lg border border-border bg-[var(--card)] px-3 py-2 text-xs font-semibold text-text-primary outline-none focus:border-primary">
                <option value="all" selected>Semua Course</option>
                @foreach($allPelatihans as $course)<option value="{{ $course->id }}">{{ $course->judul }}</option>@endforeach
            </select>
        </div>
        <div class="min-w-[220px] flex-1">
            <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-text-secondary">Unit Kerja</label>
            <select name="unit_kerja_id" class="w-full rounded-lg border border-border bg-[var(--card)] px-3 py-2 text-xs text-text-primary outline-none focus:border-primary">
                <option value="">Semua Unit Kerja</option>
                @foreach($unitKerjas as $unit)<option value="{{ $unit->id }}" {{ request('unit_kerja_id') == $unit->id ? 'selected' : '' }}>{{ $unit->nama ?? $unit->nama_unit }}</option>@endforeach
            </select>
        </div>
        <button type="submit" class="rounded-lg bg-secondary px-4 py-2 text-xs font-semibold text-text-primary hover:bg-secondary/70">Terapkan</button>
        @if(request('unit_kerja_id'))<a href="{{ route('admin.gradebook.index', ['pelatihan_id' => 'all']) }}" class="px-2 py-2 text-xs text-danger hover:underline">Reset</a>@endif
    </form>
</div>

<div class="overflow-hidden rounded-xl border border-[var(--border)] bg-[var(--card)] shadow-xs">
    <div class="border-b border-border bg-secondary/30 px-6 py-4">
        <h3 class="flex items-center gap-2 text-sm font-bold text-text-primary"><i data-lucide="layers" class="h-4 w-4 text-primary"></i> Seluruh Nilai dari Seluruh Course ({{ count($allCoursesData) }} Peserta)</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[1250px] border-collapse text-left text-xs">
            <thead class="border-b border-border bg-secondary/50 text-[11px] uppercase text-text-secondary">
                <tr>
                    <th class="sticky left-0 z-10 border-r border-border bg-secondary/90 px-4 py-3">Peserta</th>
                    <th class="border-r border-border px-4 py-3">Unit Kerja</th>
                    <th class="min-w-[430px] border-r border-border px-4 py-3">Nilai Mentah Seluruh Course</th>
                    <th class="min-w-[270px] border-r border-border px-4 py-3">Kontribusi Nilai Akhir</th>
                    <th class="border-r border-border bg-primary/5 px-4 py-3 text-center">Nilai Akhir</th>
                    <th class="border-r border-border px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse($allCoursesData as $row)
                    @php $user = $row['user']; $summary = $row['globalSummary']; $status = $summary['status_kelulusan']; @endphp
                    <tr class="align-top hover:bg-secondary/20">
                        <td class="sticky left-0 z-10 border-r border-border bg-[var(--card)] px-4 py-3">
                            <p class="font-bold text-text-primary">{{ $user->nama }}</p>
                            <p class="font-mono text-[10px] text-text-secondary">{{ $user->nip }}</p>
                            <p class="mt-1 text-[10px] text-text-secondary">{{ $row['totalCompleted'] }}/{{ $row['totalEnrolled'] }} course lulus</p>
                        </td>
                        <td class="border-r border-border px-4 py-3 text-text-secondary">{{ $user->unitKerja->nama ?? $user->unitKerja->nama_unit ?? '-' }}</td>
                        <td class="border-r border-border px-4 py-3">
                            <div class="space-y-2">
                                @forelse(collect($row['courseScores'])->where('enrolled', true) as $course)
                                    <details class="group rounded-lg border border-border bg-secondary/20 p-2" open>
                                        <summary class="flex cursor-pointer list-none items-center justify-between gap-3 font-semibold text-text-primary">
                                            <span class="truncate">{{ $course['pelatihan']->judul }}</span>
                                            <span class="shrink-0 rounded bg-[var(--card)] px-2 py-0.5 text-[10px] text-primary">Nilai course: {{ $course['nilai_akhir'] !== null ? number_format($course['nilai_akhir'], 1) : '-' }}</span>
                                        </summary>
                                        <div class="mt-2 flex flex-wrap gap-1.5">
                                            @forelse($course['items'] as $item)
                                                <span class="inline-flex items-center gap-1 rounded border border-border bg-[var(--card)] px-2 py-1 text-[10px]" title="{{ ucfirst($item['type']) }}">
                                                    <span class="max-w-[150px] truncate text-text-secondary">{{ $item['label'] }}</span>
                                                    <strong class="{{ $item['score'] !== null ? 'text-text-primary' : 'text-amber-600' }}">{{ $item['score'] !== null ? number_format($item['score'], 1) : '-' }}</strong>
                                                </span>
                                            @empty<span class="text-[10px] italic text-text-secondary">Course ini belum memiliki item penilaian.</span>@endforelse
                                        </div>
                                    </details>
                                @empty<span class="text-[11px] italic text-text-secondary">Belum mengikuti course.</span>@endforelse
                            </div>
                        </td>
                        <td class="border-r border-border px-4 py-3">
                            <div class="grid grid-cols-2 gap-1.5">
                                @foreach($componentLabels as $key => $label)
                                    @php $score = $summary['scores'][$key] ?? null; $effectiveWeight = $summary['effective_weights'][$key] ?? 0; $contribution = $summary['contributions'][$key] ?? 0; @endphp
                                    <div class="rounded border border-border px-2 py-1.5">
                                        <span class="block text-[9px] uppercase text-text-secondary">{{ $label }}</span>
                                        <span class="font-bold text-text-primary">{{ $score !== null ? number_format($score, 1) : '-' }}</span>
                                        <span class="text-[9px] text-text-secondary">× {{ number_format($effectiveWeight, 1) }}% = {{ number_format($contribution, 1) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="border-r border-border bg-primary/5 px-4 py-3 text-center">
                            <span class="block text-xl font-extrabold text-primary">{{ number_format($summary['nilai_akhir'], 1) }}</span>
                            <span class="text-[10px] font-bold text-text-secondary">{{ $summary['predikat'] }}</span>
                            <span class="mt-1 block text-[9px] text-text-secondary">{{ $summary['item_selesai'] }}/{{ $summary['total_item'] }} item selesai</span>
                        </td>
                        <td class="border-r border-border px-4 py-3 text-center">
                            @if($status === 'lulus')
                                <span class="inline-flex rounded-full bg-green-100 px-2 py-1 text-[10px] font-bold text-green-700">Lulus</span>
                            @elseif($status === 'tidak_lulus')
                                <span class="inline-flex rounded-full bg-red-100 px-2 py-1 text-[10px] font-bold text-red-700">Tidak Lulus</span>
                            @else
                                <span class="inline-flex rounded-full bg-amber-100 px-2 py-1 text-[10px] font-bold text-amber-700">Belum Lengkap</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.penilaian.show', $user->id) }}" class="inline-flex items-center gap-1 rounded border border-border bg-secondary px-2.5 py-1 text-[11px] font-semibold text-primary hover:bg-primary/10"><i data-lucide="eye" class="h-3 w-3"></i> Transkrip</a>
                        </td>
                    </tr>
                @empty<tr><td colspan="7" class="px-4 py-16 text-center text-text-secondary">Tidak ada peserta yang cocok dengan filter.</td></tr>@endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="globalBobotModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-xl border border-[var(--border)] bg-[var(--card)] p-6 shadow-2xl">
        <div class="mb-4 flex items-center justify-between border-b border-border pb-3">
            <div><h3 class="text-base font-bold text-text-primary">Bobot Nilai Akhir Global</h3><p class="text-[11px] text-text-secondary">Bobot diterapkan setelah seluruh nilai course dikumpulkan.</p></div>
            <button type="button" onclick="const modal = document.getElementById('globalBobotModal'); modal.classList.remove('flex'); modal.classList.add('hidden')" class="text-text-secondary hover:text-text-primary"><i data-lucide="x" class="h-5 w-5"></i></button>
        </div>
        <form method="POST" action="{{ route('admin.gradebook.bobot-global') }}" id="globalBobotForm">
            @csrf
            <div class="space-y-3">
                @foreach($componentLabels as $key => $label)
                    <div class="flex items-center justify-between gap-4">
                        <label for="global_{{ $key }}" class="text-xs font-medium text-text-primary">{{ $label }} (%)</label>
                        <input id="global_{{ $key }}" name="bobot_{{ $key }}" type="number" min="0" max="100" step="1" value="{{ $gradebookSettings->{'bobot_' . $key} }}" class="global-weight w-24 rounded-lg border border-border bg-[var(--card)] px-3 py-1.5 text-right text-sm font-bold text-text-primary" required>
                    </div>
                @endforeach
                <div class="flex items-center justify-between border-t border-border pt-3"><span class="text-xs font-bold text-text-primary">Total Bobot</span><span id="globalWeightTotal" class="text-sm font-extrabold text-primary">100%</span></div>
                <div class="flex items-center justify-between gap-4 border-t border-border pt-3">
                    <label for="global_passing_grade" class="text-xs font-medium text-text-primary">Passing Grade</label>
                    <input id="global_passing_grade" name="passing_grade" type="number" min="0" max="100" step="0.1" value="{{ $gradebookSettings->passing_grade }}" class="w-24 rounded-lg border border-border bg-[var(--card)] px-3 py-1.5 text-right text-sm font-bold text-text-primary" required>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2 border-t border-border pt-4">
                <button type="button" onclick="const modal = document.getElementById('globalBobotModal'); modal.classList.remove('flex'); modal.classList.add('hidden')" class="rounded-lg px-4 py-2 text-xs text-text-secondary hover:bg-secondary">Batal</button>
                <button type="submit" id="saveGlobalWeights" class="rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-primary-hover">Simpan & Hitung Ulang</button>
            </div>
        </form>
    </div>
</div>

<script>
    function updateGlobalWeightTotal() {
        const total = Array.from(document.querySelectorAll('.global-weight')).reduce((sum, input) => sum + (parseFloat(input.value) || 0), 0);
        const label = document.getElementById('globalWeightTotal');
        const button = document.getElementById('saveGlobalWeights');
        label.textContent = total + '%';
        const valid = Math.abs(total - 100) < 0.01;
        label.className = valid ? 'text-sm font-extrabold text-green-600' : 'text-sm font-extrabold text-red-600';
        button.disabled = !valid;
        button.classList.toggle('opacity-50', !valid);
    }
    document.querySelectorAll('.global-weight').forEach(input => input.addEventListener('input', updateGlobalWeightTotal));
    updateGlobalWeightTotal();
</script>
@endsection
