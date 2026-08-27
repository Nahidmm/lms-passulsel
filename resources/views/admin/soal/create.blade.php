@extends('layouts.app')

@section('title', 'Tambah Soal â€” ' . $materi->judul)

@section('content')

<div class="mb-6 flex items-center gap-2">
    @if($materi->is_pretest)
    <a href="{{ route('admin.pretest.index') }}" class="text-text-secondary hover:text-primary flex items-center gap-1.5 font-medium transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Kelola Pretest
    </a>
    @else
    <a href="{{ route('admin.materi.edit', $materi->id) }}" class="text-text-secondary hover:text-primary flex items-center gap-1.5 font-medium transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Konfigurasi Kuis
    </a>
    @endif
</div>

<div class="max-w-3xl">
    <div class="bg-[var(--card)] border border-[var(--border)] shadow-sm rounded-xl shadow-sm border border-border p-6 md:p-8">
        <div class="mb-6 pb-4 border-b border-border">
            <h2 class="text-xl font-bold text-text-primary flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-5 h-5 text-accent"></i> Tambah Soal Baru
            </h2>
            <p class="text-sm text-text-secondary mt-1">Kuis: <span class="font-medium text-text-primary">{{ $materi->judul }}</span></p>
        </div>

        @if($errors->any())
            <div class="bg-danger/10 border border-danger/20 text-danger px-4 py-3 rounded-lg mb-6 text-sm">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.materi.soal.store', $materi->id) }}" method="POST" id="soal-form">
            @csrf

            {{-- Question Type --}}
            <div class="mb-6">
                <label for="tipe" class="block text-sm font-semibold text-text-primary mb-2">Jenis Soal <span class="text-danger">*</span></label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @php
                        $types = [
                            ['value' => 'pilihan_ganda', 'label' => 'Multiple Choice', 'sub' => 'Satu jawaban benar', 'icon' => 'circle-dot'],
                            ['value' => 'multi_select',  'label' => 'Multiple Select',  'sub' => 'Lebih dari satu benar', 'icon' => 'check-square'],
                            ['value' => 'essay',         'label' => 'Free Text',         'sub' => 'Jawaban uraian bebas', 'icon' => 'align-left'],
                            ['value' => 'isian_singkat', 'label' => 'Fill in the Blank', 'sub' => 'Isian singkat', 'icon' => 'underline'],
                            ['value' => 'menjodohkan',   'label' => 'Matching',          'sub' => 'Pasangkan kolom', 'icon' => 'git-merge'],
                        ];
                    @endphp
                    @foreach($types as $type)
                        <label class="type-card cursor-pointer" data-type="{{ $type['value'] }}">
                            <input type="radio" name="tipe" value="{{ $type['value'] }}" class="sr-only" {{ old('tipe', 'pilihan_ganda') === $type['value'] ? 'checked' : '' }}>
                            <div class="type-card-inner border-2 border-border rounded-xl p-3 text-center transition-all hover:border-accent/50">
                                <i data-lucide="{{ $type['icon'] }}" class="w-6 h-6 mx-auto mb-1.5 text-text-secondary"></i>
                                <p class="text-xs font-bold text-text-primary">{{ $type['label'] }}</p>
                                <p class="text-[10px] text-text-secondary mt-0.5 leading-tight">{{ $type['sub'] }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <hr class="border-border mb-6">

            {{-- Pertanyaan --}}
            <div class="mb-5">
                <label for="pertanyaan" class="block text-sm font-semibold text-text-primary mb-1">Teks Pertanyaan <span class="text-danger">*</span></label>
                <textarea id="pertanyaan" name="pertanyaan" rows="3" required
                    class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none resize-none">{{ old('pertanyaan') }}</textarea>
            </div>

            {{-- ============================================================ --}}
            {{-- PILIHAN GANDA & MULTI SELECT --}}
            {{-- ============================================================ --}}
            <div id="section-choices" class="space-y-3 mb-5">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-sm font-semibold text-text-primary">Pilihan Jawaban <span class="text-danger">*</span></label>
                    <button type="button" id="btn-add-choice" class="text-xs text-primary hover:text-primary-hover font-medium flex items-center gap-1">
                        <i data-lucide="plus" class="w-3 h-3"></i> Tambah Pilihan
                    </button>
                </div>
                <div id="choices-container">
                    @php $labels = ['A','B','C','D']; @endphp
                    @foreach($labels as $i => $label)
                    <div class="flex items-center gap-3 choice-row" data-index="{{ $i }}">
                        {{-- Radio (pilihan_ganda) / Checkbox (multi_select) --}}
                        <div class="correct-indicator shrink-0">
                            <input type="radio" name="jawaban_benar" value="{{ $i }}" class="pg-radio w-4 h-4 text-accent cursor-pointer" {{ $i === 0 ? 'checked' : '' }}>
                            <input type="checkbox" name="jawaban_benar[]" value="{{ $i }}" class="ms-checkbox hidden w-4 h-4 text-accent cursor-pointer">
                        </div>
                        <span class="w-6 h-6 rounded-full bg-secondary border border-border flex items-center justify-center text-xs font-bold text-text-secondary shrink-0">{{ $label }}</span>
                        <input type="text" name="pilihan[]" placeholder="Teks pilihan {{ $label }}" required
                            class="flex-1 px-3 py-2 border border-border rounded-lg focus:ring-1 focus:ring-accent focus:border-accent outline-none text-sm">
                        @if($i >= 2)
                        <button type="button" onclick="removeChoice(this)" class="p-1 text-text-secondary hover:text-danger transition-colors">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                        @else
                        <div class="w-6"></div>
                        @endif
                    </div>
                    @endforeach
                </div>
                <p class="text-xs text-text-secondary"><i data-lucide="info" class="w-3 h-3 inline mr-1"></i>Klik <strong>radio/centang</strong> di kiri untuk menandai jawaban yang benar.</p>
            </div>

            {{-- ============================================================ --}}
            {{-- ISIAN SINGKAT (FILL IN THE BLANK) --}}
            {{-- ============================================================ --}}
            <div id="section-isian" class="hidden mb-5">
                <label class="block text-sm font-semibold text-text-primary mb-2">Kunci Jawaban <span class="text-danger">*</span></label>
                <input type="text" name="jawaban_teks" placeholder="Masukkan jawaban yang dianggap benar..." value="{{ old('jawaban_teks') }}"
                    class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none text-sm">
                <p class="text-xs text-text-secondary mt-1">Sistem akan mencocokkan jawaban peserta dengan teks ini (tidak case-sensitive).</p>
            </div>

            {{-- ============================================================ --}}
            {{-- ESSAY (FREE TEXT) --}}
            {{-- ============================================================ --}}
            <div id="section-essay" class="hidden mb-5">
                <div class="bg-secondary/40 border border-border rounded-xl p-4 flex items-start gap-3">
                    <i data-lucide="info" class="w-5 h-5 text-primary shrink-0 mt-0.5"></i>
                    <div>
                        <p class="text-sm font-semibold text-text-primary">Jawaban Teks Bebas</p>
                        <p class="text-xs text-text-secondary mt-1">Soal tipe Essay tidak memerlukan pilihan jawaban. Jawaban peserta akan direkam dan perlu dinilai secara manual oleh instruktur.</p>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- MENJODOHKAN (MATCHING) --}}
            {{-- ============================================================ --}}
            <div id="section-matching" class="hidden mb-5">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-semibold text-text-primary">Pasangan Jawaban <span class="text-danger">*</span></label>
                    <button type="button" id="btn-add-pair" class="text-xs text-primary hover:text-primary-hover font-medium flex items-center gap-1">
                        <i data-lucide="plus" class="w-3 h-3"></i> Tambah Pasangan
                    </button>
                </div>
                <div class="grid grid-cols-11 gap-2 mb-2 text-xs font-semibold text-text-secondary px-1">
                    <div class="col-span-5">Kolom Kiri (Soal)</div>
                    <div class="col-span-1 text-center">â†’</div>
                    <div class="col-span-5">Kolom Kanan (Jawaban)</div>
                </div>
                <div id="pairs-container" class="space-y-2">
                    @foreach(['1','2','3'] as $p)
                    <div class="grid grid-cols-11 gap-2 pair-row">
                        <input type="text" name="pasangan_kiri[]" placeholder="Item kiri {{ $p }}" required
                            class="col-span-5 px-3 py-2 border border-border rounded-lg focus:ring-1 focus:ring-accent focus:border-accent outline-none text-sm">
                        <div class="col-span-1 flex items-center justify-center text-text-secondary">â†’</div>
                        <input type="text" name="pasangan_kanan[]" placeholder="Pasangannya {{ $p }}" required
                            class="col-span-5 px-3 py-2 border border-border rounded-lg focus:ring-1 focus:ring-accent focus:border-accent outline-none text-sm">
                    </div>
                    @endforeach
                </div>
                <p class="text-xs text-text-secondary mt-2">Sistem akan mengacak urutan tampilan kolom kanan saat pengerjaan.</p>
            </div>

            <hr class="border-border my-5">

            {{-- Pembahasan & Metadata --}}
            <div class="space-y-4 mb-6">
                <div>
                    <label for="pembahasan" class="block text-sm font-semibold text-text-primary mb-1">Pembahasan / Penjelasan (Opsional)</label>
                    <textarea id="pembahasan" name="pembahasan" rows="2" placeholder="Tampilkan setelah peserta menjawab..."
                        class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none text-sm resize-none">{{ old('pembahasan') }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="bobot" class="block text-sm font-semibold text-text-primary mb-1">Bobot Poin</label>
                        <input type="number" id="bobot" name="bobot" value="{{ old('bobot', 10) }}" min="1"
                            class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none text-sm">
                    </div>
                    <div class="flex items-end pb-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 text-accent rounded border-border focus:ring-accent">
                            <span class="text-sm font-semibold text-text-primary">Soal Aktif</span>
                        </label>
                    </div>
                </div>
                @if($materi->is_pretest)
                <div>
                    <label for="topik_pelatihan_id" class="block text-sm font-semibold text-text-primary mb-1">Pilih Topik Penilaian (Untuk Pretest) <span class="text-danger">*</span></label>
                    <select name="topik_pelatihan_id" id="topik_pelatihan_id" class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none text-sm" required>
                        <option value="">-- Pilih Topik --</option>
                        @foreach($topiks as $topik)
                            <option value="{{ $topik->id }}" {{ old('topik_pelatihan_id') == $topik->id ? 'selected' : '' }}>{{ $topik->nama_topik }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-border">
                @if($materi->is_pretest)
                <a href="{{ route('admin.pretest.index') }}" class="px-6 py-2 border border-border rounded-lg text-text-secondary hover:bg-secondary font-medium transition-colors">Batal</a>
                @else
                <a href="{{ route('admin.materi.edit', $materi->id) }}" class="px-6 py-2 border border-border rounded-lg text-text-secondary hover:bg-secondary font-medium transition-colors">Batal</a>
                @endif
                <button type="submit" class="bg-accent hover:bg-accent-hover text-[var(--text-primary)] font-bold py-2 px-6 rounded-lg transition-colors shadow-sm">
                    Simpan Soal
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ====================================================
// Question Type Switching
// ====================================================
const sections = {
    pilihan_ganda: 'section-choices',
    multi_select:  'section-choices',
    essay:         'section-essay',
    isian_singkat: 'section-isian',
    menjodohkan:   'section-matching',
};
const allSections = [...new Set(Object.values(sections))];

function showSection(type) {
    allSections.forEach(s => document.getElementById(s)?.classList.add('hidden'));
    const target = sections[type];
    if (target) document.getElementById(target)?.classList.remove('hidden');

    // Toggle radio vs checkbox for correct answers
    const radios = document.querySelectorAll('.pg-radio');
    const checks = document.querySelectorAll('.ms-checkbox');
    if (type === 'multi_select') {
        radios.forEach(r => r.classList.add('hidden'));
        checks.forEach(c => c.classList.remove('hidden'));
    } else {
        radios.forEach(r => r.classList.remove('hidden'));
        checks.forEach(c => c.classList.add('hidden'));
    }
}

// Init on load
document.addEventListener('DOMContentLoaded', function() {
    const selected = document.querySelector('input[name="tipe"]:checked')?.value || 'pilihan_ganda';
    showSection(selected);
    updateTypeCards(selected);
    lucide.createIcons();
});

// Type card selection
document.querySelectorAll('.type-card').forEach(card => {
    card.addEventListener('click', function() {
        const type = this.dataset.type;
        const radio = this.querySelector('input[type="radio"]');
        radio.checked = true;
        showSection(type);
        updateTypeCards(type);
    });
});

function updateTypeCards(activeType) {
    document.querySelectorAll('.type-card').forEach(card => {
        const inner = card.querySelector('.type-card-inner');
        if (card.dataset.type === activeType) {
            inner.classList.remove('border-border');
            inner.classList.add('border-accent', 'bg-accent/5');
            inner.querySelectorAll('i, p').forEach(el => {
                el.classList.remove('text-text-secondary');
                el.classList.add('text-accent');
            });
        } else {
            inner.classList.add('border-border');
            inner.classList.remove('border-accent', 'bg-accent/5');
            inner.querySelectorAll('i').forEach(el => {
                el.classList.remove('text-accent');
                el.classList.add('text-text-secondary');
            });
        }
    });
}

// ====================================================
// Dynamic Choices (Pilihan Ganda / Multi Select)
// ====================================================
const labels = ['A','B','C','D','E','F','G','H'];
let choiceCount = 4;

document.getElementById('btn-add-choice').addEventListener('click', function() {
    if (choiceCount >= 8) return alert('Maksimal 8 pilihan jawaban.');
    const index = choiceCount;
    const label = labels[index] || (index + 1);
    const container = document.getElementById('choices-container');
    const div = document.createElement('div');
    div.className = 'flex items-center gap-3 choice-row';
    div.dataset.index = index;
    div.innerHTML = `
        <div class="correct-indicator shrink-0">
            <input type="radio" name="jawaban_benar" value="${index}" class="pg-radio w-4 h-4 text-accent cursor-pointer">
            <input type="checkbox" name="jawaban_benar[]" value="${index}" class="ms-checkbox hidden w-4 h-4 text-accent cursor-pointer">
        </div>
        <span class="w-6 h-6 rounded-full bg-secondary border border-border flex items-center justify-center text-xs font-bold text-text-secondary shrink-0">${label}</span>
        <input type="text" name="pilihan[]" placeholder="Teks pilihan ${label}" required
            class="flex-1 px-3 py-2 border border-border rounded-lg focus:ring-1 focus:ring-accent focus:border-accent outline-none text-sm">
        <button type="button" onclick="removeChoice(this)" class="p-1 text-text-secondary hover:text-danger transition-colors">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>`;
    container.appendChild(div);
    choiceCount++;
    const activeType = document.querySelector('input[name="tipe"]:checked')?.value;
    if (activeType === 'multi_select') {
        div.querySelector('.pg-radio').classList.add('hidden');
        div.querySelector('.ms-checkbox').classList.remove('hidden');
    }
    lucide.createIcons();
});

function removeChoice(btn) {
    const row = btn.closest('.choice-row');
    if (document.querySelectorAll('.choice-row').length <= 2) return alert('Minimal 2 pilihan jawaban.');
    row.remove();
    choiceCount--;
}

// ====================================================
// Dynamic Pairs (Menjodohkan)
// ====================================================
let pairCount = 3;

document.getElementById('btn-add-pair').addEventListener('click', function() {
    pairCount++;
    const container = document.getElementById('pairs-container');
    const div = document.createElement('div');
    div.className = 'grid grid-cols-11 gap-2 pair-row';
    div.innerHTML = `
        <input type="text" name="pasangan_kiri[]" placeholder="Item kiri ${pairCount}" required
            class="col-span-5 px-3 py-2 border border-border rounded-lg focus:ring-1 focus:ring-accent focus:border-accent outline-none text-sm">
        <div class="col-span-1 flex items-center justify-center text-text-secondary">â†’</div>
        <input type="text" name="pasangan_kanan[]" placeholder="Pasangannya ${pairCount}" required
            class="col-span-4 px-3 py-2 border border-border rounded-lg focus:ring-1 focus:ring-accent focus:border-accent outline-none text-sm">
        <button type="button" onclick="removePair(this)" class="col-span-1 flex items-center justify-center p-1 text-text-secondary hover:text-danger transition-colors">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>`;
    container.appendChild(div);
    lucide.createIcons();
});

function removePair(btn) {
    const rows = document.querySelectorAll('.pair-row');
    if (rows.length <= 2) return alert('Minimal 2 pasangan.');
    btn.closest('.pair-row').remove();
    pairCount--;
}
</script>
@endpush

