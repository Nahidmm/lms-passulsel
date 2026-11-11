@extends('layouts.app')

@section('title', 'Raport Nilai Peserta')

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

<div class="mb-5 flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
    <div>
        <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary">Global Gradebook</span>
        <h1 class="mt-1 text-2xl font-bold text-text-primary">Raport Nilai Peserta</h1>
        <p class="mt-1 text-xs text-text-secondary">Ringkasan nilai seluruh course dan Nilai Akhir peserta.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <button type="button" onclick="openGlobalWeightModal()" class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-[var(--card)] px-3 py-2 text-xs font-semibold text-text-primary hover:bg-secondary">
            <i data-lucide="sliders" class="h-4 w-4 text-primary"></i> Atur Bobot
        </button>
        <a href="{{ route('admin.gradebook.export', ['unit_kerja_id' => request('unit_kerja_id')]) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-[var(--card)] px-3 py-2 text-xs font-semibold text-text-primary hover:bg-secondary">
            <i data-lucide="download" class="h-4 w-4 text-green-600"></i> Export
        </a>
        <a href="{{ route('admin.gradebook.cetak', ['pelatihan_id' => 'all', 'unit_kerja_id' => request('unit_kerja_id')]) }}" target="_blank" class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3 py-2 text-xs font-semibold text-white hover:bg-primary-hover">
            <i data-lucide="printer" class="h-4 w-4"></i> Cetak
        </a>
    </div>
</div>

@if(session('success'))
    <div class="mb-5 flex items-center gap-2 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
        <i data-lucide="check-circle" class="h-4 w-4"></i>{{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mb-5 flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
        <i data-lucide="alert-circle" class="h-4 w-4"></i>{{ session('error') }}
    </div>
@endif

<div class="mb-5 grid grid-cols-2 gap-3 lg:grid-cols-4">
    <div class="rounded-xl border border-border bg-[var(--card)] p-4">
        <span class="text-[10px] font-bold uppercase tracking-wide text-text-secondary">Peserta</span>
        <p class="mt-1 text-2xl font-bold text-text-primary">{{ count($allCoursesData) }}</p>
    </div>
    <div class="rounded-xl border border-border bg-[var(--card)] p-4">
        <span class="text-[10px] font-bold uppercase tracking-wide text-text-secondary">Course</span>
        <p class="mt-1 text-2xl font-bold text-text-primary">{{ $allPelatihans->count() }}</p>
    </div>
    <div class="rounded-xl border border-border bg-[var(--card)] p-4">
        <span class="text-[10px] font-bold uppercase tracking-wide text-text-secondary">Rata-rata Akhir</span>
        <p class="mt-1 text-2xl font-bold text-primary">{{ $participantsWithCourses->isNotEmpty() ? number_format($globalAverage, 1) : '-' }}</p>
    </div>
    <div class="rounded-xl border border-border bg-[var(--card)] p-4">
        <span class="text-[10px] font-bold uppercase tracking-wide text-text-secondary">Peserta Lulus</span>
        <p class="mt-1 text-2xl font-bold text-green-600">{{ $globalPassed }}</p>
    </div>
</div>

<div class="mb-5 rounded-xl border border-border bg-[var(--card)] p-4">
    <form method="GET" action="{{ route('admin.gradebook.index') }}" class="grid gap-3 sm:grid-cols-2 xl:grid-cols-[280px_minmax(220px,1fr)_auto_auto] xl:items-end">
        <input type="hidden" name="pelatihan_id" value="all">
        <div>
            <label class="mb-1 block text-[9px] font-bold uppercase tracking-wider text-text-secondary">Tampilan</label>
            <select onchange="window.location.href = this.value === 'all' ? '{{ route('admin.gradebook.index') }}?pelatihan_id=all' : '{{ url('admin/pelatihan') }}/' + this.value + '/gradebook'" class="w-full rounded-lg border border-border bg-[var(--card)] px-3 py-2 text-xs font-semibold text-text-primary">
                <option value="all" selected>Seluruh Course</option>
                @foreach($allPelatihans as $course)
                    <option value="{{ $course->id }}">{{ $course->judul }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-[9px] font-bold uppercase tracking-wider text-text-secondary">Unit Kerja</label>
            <select name="unit_kerja_id" class="w-full rounded-lg border border-border bg-[var(--card)] px-3 py-2 text-xs text-text-primary">
                <option value="">Semua Unit Kerja</option>
                @foreach($unitKerjas as $unit)
                    <option value="{{ $unit->id }}" {{ request('unit_kerja_id') == $unit->id ? 'selected' : '' }}>{{ $unit->nama ?? $unit->nama_unit }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="rounded-lg bg-secondary px-4 py-2 text-xs font-semibold text-text-primary hover:bg-secondary/70">Terapkan</button>
        @if(request('unit_kerja_id'))
            <a href="{{ route('admin.gradebook.index', ['pelatihan_id' => 'all']) }}" class="px-2 py-2 text-center text-xs text-danger hover:underline">Reset</a>
        @endif
    </form>
</div>

<div class="overflow-hidden rounded-xl border border-border bg-[var(--card)] shadow-xs">
    <div class="flex items-center justify-between border-b border-border px-5 py-4">
        <div>
            <h2 class="flex items-center gap-2 text-sm font-bold text-text-primary"><i data-lucide="book-open" class="h-4 w-4 text-primary"></i> Ringkasan Raport</h2>
            <p class="mt-0.5 text-[10px] text-text-secondary">Klik Detail Raport untuk melihat seluruh komponen nilai.</p>
        </div>
        <span class="text-[10px] font-semibold text-text-secondary">{{ count($allCoursesData) }} peserta</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[980px] text-left text-xs">
            <thead class="border-b border-border bg-secondary/40 text-[9px] uppercase tracking-wide text-text-secondary">
                <tr>
                    <th class="w-10 px-4 py-3 text-center">No</th>
                    <th class="px-4 py-3">Peserta</th>
                    <th class="px-4 py-3">Unit Kerja</th>
                    <th class="w-28 px-4 py-3 text-center">Progres</th>
                    <th class="w-64 px-4 py-3">Rangkuman Nilai</th>
                    <th class="w-24 px-4 py-3 text-center">Nilai Akhir</th>
                    <th class="w-28 px-4 py-3 text-center">Status</th>
                    <th class="w-32 px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse($allCoursesData as $row)
                    @php
                        $user = $row['user'];
                        $summary = $row['globalSummary'];
                        $status = $summary['status_kelulusan'];
                        $teamValues = collect(['mental', 'fisik', 'akhlak', 'kompetensi'])
                            ->map(fn ($key) => $summary['scores'][$key] ?? null)
                            ->filter(fn ($score) => $score !== null);
                        $teamAverage = $teamValues->isNotEmpty() ? $teamValues->avg() : null;
                    @endphp
                    <tr class="hover:bg-secondary/20">
                        <td class="px-4 py-3 text-center text-text-secondary">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary/10 text-[10px] font-bold text-primary">{{ strtoupper(substr($user->nama, 0, 2)) }}</div>
                                <div>
                                    <p class="font-bold text-text-primary">{{ $user->nama }}</p>
                                    <p class="font-mono text-[9px] text-text-secondary">{{ $user->nip ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-text-secondary">{{ $user->unitKerja->nama ?? $user->unitKerja->nama_unit ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="font-semibold text-text-primary">{{ $row['totalCompleted'] }}/{{ $row['totalEnrolled'] }}</span>
                            <span class="block text-[9px] text-text-secondary">course lulus</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="min-w-[62px] rounded-md bg-secondary/60 px-2 py-1.5 text-center">
                                    <span class="block text-[8px] uppercase text-text-secondary">Kuis</span>
                                    <strong class="text-text-primary">{{ ($summary['scores']['quiz'] ?? null) !== null ? number_format($summary['scores']['quiz'], 1) : '-' }}</strong>
                                </div>
                                <div class="min-w-[62px] rounded-md bg-secondary/60 px-2 py-1.5 text-center">
                                    <span class="block text-[8px] uppercase text-text-secondary">Tugas</span>
                                    <strong class="text-text-primary">{{ ($summary['scores']['tugas'] ?? null) !== null ? number_format($summary['scores']['tugas'], 1) : '-' }}</strong>
                                </div>
                                <div class="min-w-[72px] rounded-md bg-secondary/60 px-2 py-1.5 text-center">
                                    <span class="block text-[8px] uppercase text-text-secondary">Pembinaan</span>
                                    <strong class="text-text-primary">{{ $teamAverage !== null ? number_format($teamAverage, 1) : '-' }}</strong>
                                </div>
                            </div>
                        </td>
                        <td class="bg-primary/5 px-4 py-3 text-center">
                            <span class="block text-lg font-extrabold text-primary">{{ number_format($summary['nilai_akhir'], 1) }}</span>
                            <span class="text-[9px] font-bold text-text-secondary">{{ $summary['predikat'] }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($status === 'lulus')
                                <span class="rounded-full bg-green-100 px-2.5 py-1 text-[9px] font-bold text-green-700">Lulus</span>
                            @elseif($status === 'tidak_lulus')
                                <span class="rounded-full bg-red-100 px-2.5 py-1 text-[9px] font-bold text-red-700">Tidak Lulus</span>
                            @else
                                <span class="rounded-full bg-amber-100 px-2.5 py-1 text-[9px] font-bold text-amber-700">Belum Lengkap</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.gradebook.peserta.show', $user->id) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-primary/20 bg-primary/5 px-3 py-2 text-[10px] font-semibold text-primary hover:bg-primary/10">
                                <i data-lucide="file-text" class="h-3.5 w-3.5"></i> Detail Raport
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-6 py-16 text-center text-xs text-text-secondary">Tidak ada peserta yang cocok dengan filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="globalBobotModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-xl border border-border bg-[var(--card)] p-6 shadow-2xl">
        <div class="mb-4 flex items-center justify-between border-b border-border pb-3">
            <div><h3 class="text-base font-bold text-text-primary">Bobot Nilai Akhir Global</h3><p class="text-[10px] text-text-secondary">Total bobot harus tepat 100%.</p></div>
            <button type="button" onclick="closeGlobalWeightModal()" class="text-text-secondary hover:text-text-primary"><i data-lucide="x" class="h-5 w-5"></i></button>
        </div>
        <form method="POST" action="{{ route('admin.gradebook.bobot-global') }}">
            @csrf
            <div class="space-y-3">
                @foreach($componentLabels as $key => $label)
                    <div class="flex items-center justify-between gap-4">
                        <label for="global_{{ $key }}" class="text-xs font-medium text-text-primary">{{ $label }}</label>
                        <div class="flex items-center gap-1">
                            <input id="global_{{ $key }}" name="bobot_{{ $key }}" type="number" min="0" max="100" step="1" value="{{ $gradebookSettings->{'bobot_' . $key} }}" class="global-weight w-20 rounded-lg border border-border bg-[var(--card)] px-2 py-1.5 text-right text-sm font-bold text-text-primary" required>
                            <span class="text-xs text-text-secondary">%</span>
                        </div>
                    </div>
                @endforeach
                <div class="flex items-center justify-between border-t border-border pt-3"><span class="text-xs font-bold text-text-primary">Total Bobot</span><span id="globalWeightTotal" class="text-sm font-extrabold text-primary">100%</span></div>
                <div class="flex items-center justify-between gap-4 border-t border-border pt-3">
                    <label for="global_passing_grade" class="text-xs font-medium text-text-primary">Passing Grade</label>
                    <input id="global_passing_grade" name="passing_grade" type="number" min="0" max="100" step="0.1" value="{{ $gradebookSettings->passing_grade }}" class="w-20 rounded-lg border border-border bg-[var(--card)] px-2 py-1.5 text-right text-sm font-bold text-text-primary" required>
                </div>
            </div>
            <div class="mt-5 flex justify-end gap-2 border-t border-border pt-4">
                <button type="button" onclick="closeGlobalWeightModal()" class="rounded-lg px-4 py-2 text-xs text-text-secondary hover:bg-secondary">Batal</button>
                <button type="submit" id="saveGlobalWeights" class="rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-white hover:bg-primary-hover">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openGlobalWeightModal() {
        const modal = document.getElementById('globalBobotModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeGlobalWeightModal() {
        const modal = document.getElementById('globalBobotModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    function updateGlobalWeightTotal() {
        const total = Array.from(document.querySelectorAll('.global-weight')).reduce((sum, input) => sum + (parseFloat(input.value) || 0), 0);
        const label = document.getElementById('globalWeightTotal');
        const button = document.getElementById('saveGlobalWeights');
        const valid = Math.abs(total - 100) < 0.01;
        label.textContent = total + '%';
        label.className = valid ? 'text-sm font-extrabold text-green-600' : 'text-sm font-extrabold text-red-600';
        button.disabled = !valid;
        button.classList.toggle('opacity-50', !valid);
    }

    document.querySelectorAll('.global-weight').forEach(input => input.addEventListener('input', updateGlobalWeightTotal));
    updateGlobalWeightTotal();
</script>
@endsection
