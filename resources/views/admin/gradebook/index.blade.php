@extends('layouts.app')

@section('title', 'Buku Nilai (Gradebook): ' . $pelatihan->judul)

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('admin.pelatihan.show', $pelatihan->id) }}" class="text-text-secondary hover:text-primary transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <span class="inline-block bg-primary/10 text-primary text-xs font-bold px-2.5 py-0.5 rounded-full uppercase">
                Moodle-Style Gradebook
            </span>
        </div>
        <h1 class="text-2xl font-bold text-text-primary">Buku Nilai (Grader Report)</h1>
        <p class="text-xs text-text-secondary mt-1">Pelatihan: <span class="font-semibold text-text-primary">{{ $pelatihan->judul }}</span> &bull; Passing Grade: <span class="font-bold text-primary">{{ $bobot->passing_grade }}</span></p>
    </div>
    
    <div class="flex flex-wrap items-center gap-2">
        <button onclick="document.getElementById('bobotModal').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-border bg-[var(--card)] hover:bg-secondary text-text-primary text-xs font-semibold shadow-sm transition-colors">
            <i data-lucide="sliders" class="w-4 h-4 text-primary"></i> Atur Bobot Nilai
        </button>
        <a href="{{ route('admin.pelatihan.gradebook.export', $pelatihan->id) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white text-xs font-semibold shadow-sm transition-colors">
            <i data-lucide="download" class="w-4 h-4"></i> Export CSV / Excel
        </a>
    </div>
</div>

@if(session('success'))
<div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 flex items-center gap-3 text-sm">
    <i data-lucide="check-circle" class="w-5 h-5 text-green-600 shrink-0"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 flex items-center gap-3 text-sm">
    <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 shrink-0"></i>
    <span>{{ session('error') }}</span>
</div>
@endif

<!-- Bobot Badges Summary -->
<div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
    <div class="p-3 bg-[var(--card)] border border-[var(--border)] rounded-xl">
        <span class="text-[11px] font-semibold text-text-secondary uppercase block">Pretest</span>
        <div class="flex items-baseline gap-1 mt-1">
            <span class="text-xl font-bold text-text-primary">{{ $bobot->bobot_pretest }}%</span>
        </div>
    </div>
    <div class="p-3 bg-[var(--card)] border border-[var(--border)] rounded-xl">
        <span class="text-[11px] font-semibold text-text-secondary uppercase block">Kuis Formatif</span>
        <div class="flex items-baseline gap-1 mt-1">
            <span class="text-xl font-bold text-text-primary">{{ $bobot->bobot_quiz }}%</span>
        </div>
    </div>
    <div class="p-3 bg-[var(--card)] border border-[var(--border)] rounded-xl">
        <span class="text-[11px] font-semibold text-text-secondary uppercase block">Tugas / Sertifikat</span>
        <div class="flex items-baseline gap-1 mt-1">
            <span class="text-xl font-bold text-text-primary">{{ $bobot->bobot_tugas }}%</span>
        </div>
    </div>
    <div class="p-3 bg-[var(--card)] border border-[var(--border)] rounded-xl">
        <span class="text-[11px] font-semibold text-text-secondary uppercase block">Posttest / Ujian</span>
        <div class="flex items-baseline gap-1 mt-1">
            <span class="text-xl font-bold text-text-primary">{{ $bobot->bobot_posttest }}%</span>
        </div>
    </div>
    <div class="p-3 bg-primary/5 border border-primary/20 rounded-xl">
        <span class="text-[11px] font-semibold text-primary uppercase block">Passing Grade</span>
        <div class="flex items-baseline gap-1 mt-1">
            <span class="text-xl font-bold text-primary">{{ $bobot->passing_grade }}</span>
            <span class="text-xs text-text-secondary">/ 100</span>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-xl p-4 mb-6">
    <form method="GET" action="{{ route('admin.pelatihan.gradebook', $pelatihan->id) }}" class="flex flex-wrap items-center gap-3">
        <!-- Course Switcher -->
        <div class="min-w-[240px]">
            <label class="text-[10px] font-bold text-text-secondary uppercase tracking-wider block mb-1">Pilih Kursus LMS</label>
            <select onchange="window.location.href='{{ url('admin/pelatihan') }}/' + this.value + '/gradebook'" class="w-full px-3 py-2 text-xs rounded-lg border border-border bg-[var(--card)] text-text-primary focus:border-primary outline-none font-semibold">
                @foreach($allPelatihans as $p)
                    <option value="{{ $p->id }}" {{ $pelatihan->id == $p->id ? 'selected' : '' }}>
                        {{ $p->judul }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Unit Kerja Filter -->
        <div class="flex-1 min-w-[200px]">
            <label class="text-[10px] font-bold text-text-secondary uppercase tracking-wider block mb-1">Filter Unit Kerja</label>
            <select name="unit_kerja_id" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs rounded-lg border border-border bg-[var(--card)] text-text-primary focus:border-primary outline-none">
                <option value="">Semua Unit Kerja</option>
                @foreach($unitKerjas as $uk)
                    <option value="{{ $uk->id }}" {{ request('unit_kerja_id') == $uk->id ? 'selected' : '' }}>{{ $uk->nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="pt-4 flex items-center gap-2">
            <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-lg bg-secondary text-text-primary hover:bg-secondary/70 transition-colors">
                Terapkan
            </button>
            @if(request('unit_kerja_id'))
                <a href="{{ route('admin.pelatihan.gradebook', $pelatihan->id) }}" class="text-xs text-danger hover:underline">Reset</a>
            @endif
        </div>
    </form>
</div>

<!-- Grader Report Matrix Table -->
<div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-xl overflow-hidden">
    <div class="px-6 py-4 border-b border-border bg-secondary/30 flex items-center justify-between">
        <h3 class="font-bold text-text-primary flex items-center gap-2 text-sm">
            <i data-lucide="table" class="w-4 h-4 text-primary"></i> Matriks Nilai Peserta ({{ count($gradebookData) }} Peserta)
        </h3>
    </div>

    @if(empty($gradebookData))
        <div class="text-center py-16">
            <i data-lucide="user-x" class="w-12 h-12 text-border mx-auto mb-3"></i>
            <h4 class="text-text-primary font-medium">Belum ada data peserta</h4>
            <p class="text-text-secondary text-xs mt-1">Belum ada peserta terdaftar pada pelatihan ini.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-secondary/50 text-text-secondary uppercase border-b border-border text-[11px]">
                    <tr>
                        <th class="px-4 py-3 sticky left-0 bg-secondary/90 backdrop-blur z-10 border-r border-border">Nama Peserta</th>
                        <th class="px-4 py-3 border-r border-border">Unit Kerja</th>
                        <th class="px-4 py-3 text-center border-r border-border">Pretest<br><span class="text-[10px] text-primary lowercase font-normal">({{ $bobot->bobot_pretest }}%)</span></th>
                        <th class="px-4 py-3 text-center border-r border-border">Rata2 Kuis<br><span class="text-[10px] text-primary lowercase font-normal">({{ $bobot->bobot_quiz }}%)</span></th>
                        <th class="px-4 py-3 text-center border-r border-border">Rata2 Tugas<br><span class="text-[10px] text-primary lowercase font-normal">({{ $bobot->bobot_tugas }}%)</span></th>
                        <th class="px-4 py-3 text-center border-r border-border">Posttest<br><span class="text-[10px] text-primary lowercase font-normal">({{ $bobot->bobot_posttest }}%)</span></th>
                        <th class="px-4 py-3 text-center border-r border-border bg-primary/5">Nilai Akhir</th>
                        <th class="px-4 py-3 text-center border-r border-border">Predikat</th>
                        <th class="px-4 py-3 text-center border-r border-border">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach($gradebookData as $item)
                    @php 
                        $u = $item['user'];
                        $r = $item['rekap'];
                    @endphp
                    <tr class="hover:bg-secondary/20 transition-colors">
                        <td class="px-4 py-3 sticky left-0 bg-[var(--card)] z-10 border-r border-border font-medium text-text-primary">
                            <div>{{ $u->nama }}</div>
                            <div class="text-[10px] text-text-secondary">NIP: {{ $u->nip ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3 border-r border-border text-text-secondary">
                            {{ $u->unitKerja->nama ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-center border-r border-border font-medium">
                            @if($r->nilai_pretest !== null)
                                <span class="font-bold text-purple-600">{{ number_format($r->nilai_pretest, 1) }}</span>
                            @else
                                <span class="text-text-secondary opacity-40">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center border-r border-border font-medium">
                            @if($r->nilai_quiz !== null)
                                <span class="font-bold text-indigo-600" title="{{ count($item['quizDetails']) }} kuis">{{ number_format($r->nilai_quiz, 1) }}</span>
                            @else
                                <span class="text-text-secondary opacity-40">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center border-r border-border font-medium">
                            @if($r->nilai_tugas !== null)
                                <span class="font-bold text-amber-600">{{ number_format($r->nilai_tugas, 1) }}</span>
                            @else
                                <span class="text-text-secondary opacity-40">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center border-r border-border font-medium">
                            @if($r->nilai_posttest !== null)
                                <span class="font-bold text-emerald-600">{{ number_format($r->nilai_posttest, 1) }}</span>
                            @else
                                <span class="text-text-secondary opacity-40">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center border-r border-border bg-primary/5 font-bold text-primary text-sm">
                            {{ number_format($r->nilai_akhir, 1) }}
                        </td>
                        <td class="px-4 py-3 text-center border-r border-border">
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-bold {{ $r->predikat_color }}">
                                {{ $r->predikat ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center border-r border-border">
                            @if($r->status_lulus)
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-green-700 bg-green-100 px-2 py-0.5 rounded-full">
                                    <i data-lucide="check" class="w-3 h-3"></i> Lulus
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-red-700 bg-red-100 px-2 py-0.5 rounded-full">
                                    <i data-lucide="x" class="w-3 h-3"></i> Belum
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.penilaian.show', $u->id) }}" class="inline-flex items-center gap-1 text-[11px] font-semibold px-2.5 py-1 rounded bg-secondary hover:bg-primary/10 text-primary border border-border transition-colors" title="Lihat Transkrip & Kuis">
                                <i data-lucide="eye" class="w-3 h-3"></i> Transkrip
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- Modal Konfigurasi Bobot Nilai -->
<div id="bobotModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4">
    <div class="bg-[var(--card)] border border-[var(--border)] rounded-xl max-w-md w-full p-6 shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-border mb-4">
            <h3 class="font-bold text-text-primary text-base">Konfigurasi Bobot Nilai</h3>
            <button onclick="document.getElementById('bobotModal').classList.add('hidden')" class="text-text-secondary hover:text-text-primary">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form method="POST" action="{{ route('admin.pelatihan.gradebook.bobot', $pelatihan->id) }}" id="bobotForm">
            @csrf
            <p class="text-xs text-text-secondary mb-4">
                Total persentase bobot harus pas <strong class="text-primary">100%</strong>. Perubahan akan langsung diaplikasikan ke semua nilai akhir peserta.
            </p>

            <div class="space-y-3">
                <div class="flex items-center justify-between gap-4">
                    <label class="text-xs font-medium text-text-primary">Bobot Pretest (%)</label>
                    <input type="number" step="1" min="0" max="100" name="bobot_pretest" id="b_pretest" value="{{ $bobot->bobot_pretest }}" class="w-24 px-3 py-1.5 rounded-lg border border-border bg-[var(--card)] text-text-primary text-right font-bold text-sm" required>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <label class="text-xs font-medium text-text-primary">Bobot Kuis Formatif (%)</label>
                    <input type="number" step="1" min="0" max="100" name="bobot_quiz" id="b_quiz" value="{{ $bobot->bobot_quiz }}" class="w-24 px-3 py-1.5 rounded-lg border border-border bg-[var(--card)] text-text-primary text-right font-bold text-sm" required>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <label class="text-xs font-medium text-text-primary">Bobot Tugas / Kasus (%)</label>
                    <input type="number" step="1" min="0" max="100" name="bobot_tugas" id="b_tugas" value="{{ $bobot->bobot_tugas }}" class="w-24 px-3 py-1.5 rounded-lg border border-border bg-[var(--card)] text-text-primary text-right font-bold text-sm" required>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <label class="text-xs font-medium text-text-primary">Bobot Posttest / Ujian (%)</label>
                    <input type="number" step="1" min="0" max="100" name="bobot_posttest" id="b_posttest" value="{{ $bobot->bobot_posttest }}" class="w-24 px-3 py-1.5 rounded-lg border border-border bg-[var(--card)] text-text-primary text-right font-bold text-sm" required>
                </div>

                <div class="pt-2 border-t border-border flex items-center justify-between">
                    <span class="text-xs font-bold text-text-primary">Total Bobot:</span>
                    <span id="totalBobotLabel" class="text-sm font-extrabold text-primary">100%</span>
                </div>

                <div class="pt-3 border-t border-border flex items-center justify-between gap-4">
                    <label class="text-xs font-medium text-text-primary">Passing Grade Kelulusan</label>
                    <input type="number" step="0.1" min="0" max="100" name="passing_grade" value="{{ $bobot->passing_grade }}" class="w-24 px-3 py-1.5 rounded-lg border border-border bg-[var(--card)] text-text-primary text-right font-bold text-sm" required>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-border flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('bobotModal').classList.add('hidden')" class="px-4 py-2 rounded-lg text-xs text-text-secondary hover:bg-secondary">Batal</button>
                <button type="submit" id="btnSimpanBobot" class="px-4 py-2 rounded-lg text-xs font-semibold bg-primary hover:bg-primary-hover text-white shadow-sm flex items-center gap-1.5">
                    <i data-lucide="check" class="w-4 h-4"></i> Simpan Konfigurasi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function calculateTotal() {
        const p = parseFloat(document.getElementById('b_pretest').value) || 0;
        const q = parseFloat(document.getElementById('b_quiz').value) || 0;
        const t = parseFloat(document.getElementById('b_tugas').value) || 0;
        const pt = parseFloat(document.getElementById('b_posttest').value) || 0;
        const total = p + q + t + pt;

        const label = document.getElementById('totalBobotLabel');
        label.textContent = total + '%';
        if (Math.abs(total - 100) < 0.01) {
            label.className = 'text-sm font-extrabold text-green-600';
            document.getElementById('btnSimpanBobot').disabled = false;
        } else {
            label.className = 'text-sm font-extrabold text-red-600';
            document.getElementById('btnSimpanBobot').disabled = true;
        }
    }

    ['b_pretest', 'b_quiz', 'b_tugas', 'b_posttest'].forEach(id => {
        document.getElementById(id).addEventListener('input', calculateTotal);
    });
    calculateTotal();
</script>
@endsection
