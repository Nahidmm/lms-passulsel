@extends('layouts.app')

@section('title', 'Edit Soal - ' . $materi->judul)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Top Back Navigation --}}
    <div class="flex items-center justify-between">
        @if($materi->is_pretest)
            <a href="{{ route('admin.pretest.index') }}"
               class="text-xs font-semibold text-[var(--text-secondary)] hover:text-primary flex items-center gap-1.5 transition-colors group">
                <i data-lucide="arrow-left" class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform"></i>
                <span>Kembali ke Bank Soal Pretest</span>
            </a>
        @else
            <a href="{{ route('admin.materi.edit', $materi->id) }}"
               class="text-xs font-semibold text-[var(--text-secondary)] hover:text-primary flex items-center gap-1.5 transition-colors group">
                <i data-lucide="arrow-left" class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform"></i>
                <span>Kembali ke Kelola Kuis</span>
            </a>
        @endif
        <div class="text-xs text-[var(--text-secondary)]">
            Kuis: <strong class="text-[var(--text-primary)] font-semibold">{{ $materi->judul }}</strong>
        </div>
    </div>

    {{-- Main Form Card --}}
    <div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-2xl p-6 md:p-8 space-y-6">
        <div class="pb-4 border-b border-[var(--border)] flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                <i data-lucide="edit-3" class="w-5 h-5"></i>
            </div>
            <div>
                <h1 class="text-lg font-bold text-[var(--text-primary)]">Edit Butir Soal</h1>
                <p class="text-xs text-[var(--text-secondary)] mt-0.5">Perbarui pertanyaan, pilihan jawaban, kunci penilaian, atau pembahasan.</p>
            </div>
        </div>

        @if(isset($errors) && $errors->any())
            <div class="bg-rose-500/10 border border-rose-500/20 text-rose-700 dark:text-rose-300 p-4 rounded-2xl text-sm flex gap-3 items-start">
                <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5 text-rose-600"></i>
                <div>
                    <p class="font-bold text-xs uppercase tracking-wider mb-1">Periksa kesalahan input:</p>
                    <ul class="list-disc pl-4 space-y-0.5 text-xs">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @php
            $currentTipe = old('tipe', $soal->tipe);
            $pilihanJawabans = $soal->pilihanJawaban;

            if ($currentTipe === 'multi_select') {
                $correctIndexes = $pilihanJawabans->filter(fn($p) => $p->is_correct)->map(fn($p, $i) => $i)->values()->toArray();
            } elseif ($currentTipe === 'pilihan_ganda') {
                $correctIndex = $pilihanJawabans->search(fn($p) => $p->is_correct);
            } elseif ($currentTipe === 'isian_singkat') {
                $isianTeks = $pilihanJawabans->first()?->teks ?? '';
            } elseif ($currentTipe === 'menjodohkan') {
                $pairs = $pilihanJawabans->map(fn($p) => explode('|||', $p->teks))->toArray();
            }
        @endphp

        <form action="{{ route('admin.soal.update', $soal->id) }}" method="POST" id="soal-form" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Question Type Selector --}}
            <div class="space-y-2.5">
                <label for="tipe" class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)]">
                    Jenis Pertanyaan <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-2.5">
                    @php
                        $types = [
                            ['value' => 'pilihan_ganda', 'label' => 'Multiple Choice', 'sub' => '1 jawaban benar', 'icon' => 'circle-dot'],
                            ['value' => 'multi_select',  'label' => 'Multi Select',     'sub' => '>1 jawaban benar', 'icon' => 'check-square'],
                            ['value' => 'essay',         'label' => 'Uraian / Essay',   'sub' => 'Jawaban bebas', 'icon' => 'align-left'],
                            ['value' => 'isian_singkat', 'label' => 'Isian Singkat',    'sub' => 'Teks kunci', 'icon' => 'underline'],
                            ['value' => 'menjodohkan',   'label' => 'Menjodohkan',      'sub' => 'Pasangkan kolom', 'icon' => 'git-merge'],
                        ];
                    @endphp
                    @foreach($types as $type)
                        <label class="type-card cursor-pointer block" data-type="{{ $type['value'] }}">
                            <input type="radio" name="tipe" value="{{ $type['value'] }}" class="sr-only" {{ $currentTipe === $type['value'] ? 'checked' : '' }}>
                            <div class="type-card-inner border-2 border-[var(--border)] rounded-xl p-3 text-center transition-all hover:border-primary/50 flex flex-col items-center justify-center min-h-[95px]">
                                <i data-lucide="{{ $type['icon'] }}" class="w-5 h-5 mb-1.5 text-[var(--text-secondary)] transition-colors"></i>
                                <p class="text-xs font-bold text-[var(--text-primary)]">{{ $type['label'] }}</p>
                                <p class="text-[10px] text-[var(--text-secondary)] mt-0.5 leading-tight">{{ $type['sub'] }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Teks Pertanyaan --}}
            <div class="space-y-1.5">
                <label for="pertanyaan" class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)]">
                    Teks Pertanyaan <span class="text-rose-500">*</span>
                </label>
                <textarea id="pertanyaan" name="pertanyaan" rows="3" required
                    class="w-full px-4 py-3 bg-[var(--input)] border border-[var(--border)] rounded-xl text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none resize-none transition-all">{{ old('pertanyaan', $soal->pertanyaan) }}</textarea>
            </div>

            {{-- ============================================================ --}}
            {{-- PILIHAN GANDA & MULTI SELECT --}}
            {{-- ============================================================ --}}
            <div id="section-choices" class="space-y-3 pt-2">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)]">
                            Pilihan Jawaban & Kunci Jawaban <span class="text-rose-500">*</span>
                        </label>
                        <p class="text-[11px] text-[var(--text-muted)] mt-0.5">Pilih tombol radio/centang di sebelah kiri opsi untuk menandai kunci jawaban yang benar.</p>
                    </div>
                    <button type="button" id="btn-add-choice" class="text-xs font-semibold text-primary hover:text-primary/80 transition-colors flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-primary/20 hover:bg-primary/5">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Tambah Opsi</span>
                    </button>
                </div>

                <div id="choices-container" class="space-y-2.5">
                    @php $labels = ['A','B','C','D','E','F','G','H']; @endphp
                    @if(in_array($currentTipe, ['pilihan_ganda', 'multi_select']) && $pilihanJawabans->isNotEmpty())
                        @foreach($pilihanJawabans as $idx => $p)
                        <div class="flex items-center gap-3 choice-row p-2.5 rounded-xl border border-[var(--border)] bg-[var(--card)] hover:border-[var(--text-muted)] transition-all" data-index="{{ $idx }}">
                            <div class="correct-indicator shrink-0 flex items-center justify-center pl-1">
                                <input type="radio" name="jawaban_benar" value="{{ $idx }}" class="pg-radio w-4 h-4 text-primary border-[var(--border)] focus:ring-primary cursor-pointer {{ $currentTipe !== 'pilihan_ganda' ? 'hidden' : '' }}" {{ $p->is_correct && $currentTipe === 'pilihan_ganda' ? 'checked' : '' }} title="Tandai sebagai kunci benar">
                                <input type="checkbox" name="jawaban_benar[]" value="{{ $idx }}" class="ms-checkbox w-4 h-4 text-primary border-[var(--border)] focus:ring-primary rounded cursor-pointer {{ $currentTipe !== 'multi_select' ? 'hidden' : '' }}" {{ $p->is_correct && $currentTipe === 'multi_select' ? 'checked' : '' }} title="Tandai sebagai kunci benar">
                            </div>

                            <span class="w-6 h-6 rounded-lg bg-[var(--muted)] text-[var(--text-secondary)] flex items-center justify-center text-xs font-bold shrink-0">
                                {{ $labels[$idx] ?? $idx+1 }}
                            </span>

                            <input type="text" name="pilihan[]" value="{{ old("pilihan.$idx", $p->teks) }}" placeholder="Teks pilihan"
                                class="flex-1 px-3 py-2 bg-[var(--input)] border border-[var(--border)] rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none text-xs text-[var(--text-primary)] transition-all">

                            @if($idx >= 2)
                                <button type="button" onclick="removeChoice(this)" class="p-1.5 text-[var(--text-muted)] hover:text-rose-600 hover:bg-rose-500/10 rounded-lg transition-colors" title="Hapus Opsi">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                            @else
                                <div class="w-7"></div>
                            @endif
                        </div>
                        @endforeach
                    @else
                        @foreach(['A','B','C','D'] as $i => $label)
                        <div class="flex items-center gap-3 choice-row p-2.5 rounded-xl border border-[var(--border)] bg-[var(--card)] hover:border-[var(--text-muted)] transition-all" data-index="{{ $i }}">
                            <div class="correct-indicator shrink-0 flex items-center justify-center pl-1">
                                <input type="radio" name="jawaban_benar" value="{{ $i }}" class="pg-radio w-4 h-4 text-primary border-[var(--border)] focus:ring-primary cursor-pointer" {{ $i === 0 ? 'checked' : '' }}>
                                <input type="checkbox" name="jawaban_benar[]" value="{{ $i }}" class="ms-checkbox hidden w-4 h-4 text-primary border-[var(--border)] focus:ring-primary rounded cursor-pointer">
                            </div>
                            <span class="w-6 h-6 rounded-lg bg-[var(--muted)] text-[var(--text-secondary)] flex items-center justify-center text-xs font-bold shrink-0">{{ $label }}</span>
                            <input type="text" name="pilihan[]" placeholder="Teks pilihan {{ $label }}"
                                class="flex-1 px-3 py-2 bg-[var(--input)] border border-[var(--border)] rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none text-xs text-[var(--text-primary)] transition-all">
                            @if($i >= 2)
                                <button type="button" onclick="removeChoice(this)" class="p-1.5 text-[var(--text-muted)] hover:text-rose-600 hover:bg-rose-500/10 rounded-lg transition-colors"><i data-lucide="x" class="w-4 h-4"></i></button>
                            @else
                                <div class="w-7"></div>
                            @endif
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- ISIAN SINGKAT --}}
            {{-- ============================================================ --}}
            <div id="section-isian" class="hidden space-y-2 pt-2">
                <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)]">
                    Kunci Jawaban Isian <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="jawaban_teks" placeholder="Masukkan teks jawaban yang dianggap tepat..." value="{{ old('jawaban_teks', $isianTeks ?? '') }}"
                    class="w-full px-4 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                <p class="text-[11px] text-[var(--text-muted)]">Sistem akan mencocokkan teks jawaban peserta dengan kata kunci ini (tidak case-sensitive).</p>
            </div>

            {{-- ============================================================ --}}
            {{-- ESSAY --}}
            {{-- ============================================================ --}}
            <div id="section-essay" class="hidden pt-2">
                <div class="bg-blue-500/5 border border-blue-500/20 rounded-xl p-4 flex items-start gap-3 text-xs text-blue-900 dark:text-blue-200">
                    <i data-lucide="info" class="w-4 h-4 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5"></i>
                    <div>
                        <p class="font-bold text-blue-700 dark:text-blue-300">Format Uraian / Esai Mandiri</p>
                        <p class="mt-0.5 text-blue-800/80 dark:text-blue-300/80">Pertanyaan ini tidak memerlukan opsi jawaban. Jawaban peserta akan tersimpan untuk dinilai manual oleh instruktur.</p>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- MENJODOHKAN --}}
            {{-- ============================================================ --}}
            <div id="section-matching" class="hidden space-y-3 pt-2">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)]">
                            Pasangan Kolom Kiri & Kanan <span class="text-rose-500">*</span>
                        </label>
                        <p class="text-[11px] text-[var(--text-muted)] mt-0.5">Sistem akan mengacak urutan kolom kanan saat peserta mengerjakan kuis.</p>
                    </div>
                    <button type="button" id="btn-add-pair" class="text-xs font-semibold text-primary hover:text-primary/80 transition-colors flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-primary/20 hover:bg-primary/5">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Tambah Pasangan</span>
                    </button>
                </div>

                <div class="grid grid-cols-11 gap-2 text-[11px] font-bold uppercase tracking-wider text-[var(--text-secondary)] px-1">
                    <div class="col-span-5">Kolom Kiri (Pernyataan)</div>
                    <div class="col-span-1 text-center">&rarr;</div>
                    <div class="col-span-5">Kolom Kanan (Pasangan Benar)</div>
                </div>

                <div id="pairs-container" class="space-y-2">
                    @if($currentTipe === 'menjodohkan' && !empty($pairs))
                        @foreach($pairs as $p)
                        <div class="grid grid-cols-11 gap-2 pair-row items-center">
                            <input type="text" name="pasangan_kiri[]" value="{{ $p[0] ?? '' }}" class="col-span-5 px-3 py-2 bg-[var(--input)] border border-[var(--border)] rounded-lg text-xs text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                            <div class="col-span-1 flex items-center justify-center text-[var(--text-secondary)] font-bold">&rarr;</div>
                            <input type="text" name="pasangan_kanan[]" value="{{ $p[1] ?? '' }}" class="col-span-5 px-3 py-2 bg-[var(--input)] border border-[var(--border)] rounded-lg text-xs text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                        </div>
                        @endforeach
                    @else
                        @foreach(['1','2','3'] as $p)
                        <div class="grid grid-cols-11 gap-2 pair-row items-center">
                            <input type="text" name="pasangan_kiri[]" placeholder="Item kiri {{ $p }}" class="col-span-5 px-3 py-2 bg-[var(--input)] border border-[var(--border)] rounded-lg text-xs text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                            <div class="col-span-1 flex items-center justify-center text-[var(--text-secondary)] font-bold">&rarr;</div>
                            <input type="text" name="pasangan_kanan[]" placeholder="Pasangan kanan {{ $p }}" class="col-span-5 px-3 py-2 bg-[var(--input)] border border-[var(--border)] rounded-lg text-xs text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <hr class="border-[var(--border)]">

            {{-- Parameter Nilai, Bobot & Pembahasan --}}
            <div class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="bobot" class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                            Bobot Poin Soal <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" id="bobot" name="bobot" value="{{ old('bobot', $soal->bobot) }}" min="1" required
                            class="w-full px-4 py-2 bg-[var(--input)] border border-[var(--border)] rounded-xl text-sm font-bold text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                    </div>

                    <div class="flex items-center sm:pt-6">
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-[var(--border)] hover:bg-[var(--muted)]/40 cursor-pointer w-full transition-colors">
                            <input type="checkbox" name="is_active" value="1" {{ $soal->is_active ? 'checked' : '' }}
                                class="w-4 h-4 rounded text-primary border-[var(--border)] focus:ring-primary cursor-pointer shrink-0">
                            <div>
                                <p class="text-xs font-bold text-[var(--text-primary)]">Soal Aktif</p>
                                <p class="text-[10px] text-[var(--text-secondary)]">Akan dimuat saat peserta mengerjakan kuis.</p>
                            </div>
                        </label>
                    </div>
                </div>

                @if($materi->is_pretest)
                <div>
                    <label for="topik_pelatihan_id" class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                        Topik Penilaian Diagnostik <span class="text-rose-500">*</span>
                    </label>
                    <select name="topik_pelatihan_id" id="topik_pelatihan_id" required
                        class="w-full px-4 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                        <option value="">-- Pilih Topik Indikator --</option>
                        @foreach($topiks as $topik)
                            <option value="{{ $topik->id }}" {{ old('topik_pelatihan_id', $soal->topik_pelatihan_id) == $topik->id ? 'selected' : '' }}>{{ $topik->nama_topik }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div>
                    <label for="pembahasan" class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                        Pembahasan / Penjelasan Jawaban <span class="text-[11px] font-normal text-[var(--text-muted)] lowercase">(opsional)</span>
                    </label>
                    <textarea id="pembahasan" name="pembahasan" rows="2" placeholder="Catatan pembahasan yang akan ditampilkan setelah kuis selesai..."
                        class="w-full px-4 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-xs text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none resize-none transition-all">{{ old('pembahasan', $soal->pembahasan) }}</textarea>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-[var(--border)]">
                @if($materi->is_pretest)
                    <a href="{{ route('admin.pretest.index') }}" class="btn btn-secondary text-xs font-semibold py-2.5 px-5 rounded-xl border border-[var(--border)] text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition-all">
                        Batal
                    </a>
                @else
                    <a href="{{ route('admin.materi.edit', $materi->id) }}" class="btn btn-secondary text-xs font-semibold py-2.5 px-5 rounded-xl border border-[var(--border)] text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition-all">
                        Batal
                    </a>
                @endif
                <button type="submit" class="btn btn-primary text-white text-xs font-semibold py-2.5 px-6 rounded-xl shadow-xs transition-all flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Simpan Perubahan Soal</span>
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
const sections = {
    pilihan_ganda: 'section-choices',
    multi_select:  'section-choices',
    essay:         'section-essay',
    isian_singkat: 'section-isian',
    menjodohkan:   'section-matching',
};
const allSections = [...new Set(Object.values(sections))];

function showSection(type) {
    allSections.forEach(s => {
        const el = document.getElementById(s);
        if (el) {
            el.classList.add('hidden');
            el.querySelectorAll('input, select, textarea').forEach(input => {
                input.disabled = true;
                input.required = false;
            });
        }
    });

    const targetId = sections[type];
    if (targetId) {
        const targetEl = document.getElementById(targetId);
        if (targetEl) {
            targetEl.classList.remove('hidden');
            targetEl.querySelectorAll('input, select, textarea').forEach(input => {
                input.disabled = false;
            });
        }
    }

    if (type === 'pilihan_ganda' || type === 'multi_select') {
        document.querySelectorAll('#choices-container input[name="pilihan[]"]').forEach(input => {
            input.required = true;
        });
        const radios = document.querySelectorAll('.pg-radio');
        const checks = document.querySelectorAll('.ms-checkbox');
        if (type === 'multi_select') {
            radios.forEach(r => r.classList.add('hidden'));
            checks.forEach(c => c.classList.remove('hidden'));
        } else {
            radios.forEach(r => r.classList.remove('hidden'));
            checks.forEach(c => c.classList.add('hidden'));
        }
    } else if (type === 'isian_singkat') {
        const isianInput = document.querySelector('input[name="jawaban_teks"]');
        if (isianInput) isianInput.required = true;
    } else if (type === 'menjodohkan') {
        document.querySelectorAll('input[name="pasangan_kiri[]"]').forEach(i => i.required = true);
        document.querySelectorAll('input[name="pasangan_kanan[]"]').forEach(i => i.required = true);
    }
}

function updateTypeCards(activeType) {
    document.querySelectorAll('.type-card').forEach(card => {
        const inner = card.querySelector('.type-card-inner');
        const icon = inner.querySelector('i');
        const label = inner.querySelector('p');
        if (card.dataset.type === activeType) {
            inner.className = 'type-card-inner border-2 border-primary bg-primary/5 rounded-xl p-3 text-center transition-all flex flex-col items-center justify-center min-h-[95px] shadow-2xs';
            if (icon) icon.className = 'w-5 h-5 mb-1.5 text-primary transition-colors';
            if (label) label.className = 'text-xs font-bold text-primary';
        } else {
            inner.className = 'type-card-inner border-2 border-[var(--border)] rounded-xl p-3 text-center transition-all hover:border-primary/40 flex flex-col items-center justify-center min-h-[95px]';
            if (icon) icon.className = 'w-5 h-5 mb-1.5 text-[var(--text-secondary)] transition-colors';
            if (label) label.className = 'text-xs font-bold text-[var(--text-primary)]';
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const selected = document.querySelector('input[name="tipe"]:checked')?.value || 'pilihan_ganda';
    showSection(selected);
    updateTypeCards(selected);
    if (window.lucide) {
        window.lucide.createIcons();
    }
});

document.querySelectorAll('.type-card').forEach(card => {
    card.addEventListener('click', function() {
        const type = this.dataset.type;
        const radio = this.querySelector('input[type="radio"]');
        if (radio) radio.checked = true;
        showSection(type);
        updateTypeCards(type);
    });
});

const labels = ['A','B','C','D','E','F','G','H'];

document.getElementById('btn-add-choice')?.addEventListener('click', function() {
    const rows = document.querySelectorAll('#choices-container .choice-row');
    if (rows.length >= 8) return alert('Maksimal 8 pilihan jawaban.');
    const index = rows.length;
    const label = labels[index] || (index + 1);
    const container = document.getElementById('choices-container');
    const div = document.createElement('div');
    div.className = 'flex items-center gap-3 choice-row p-2.5 rounded-xl border border-[var(--border)] bg-[var(--card)] hover:border-[var(--text-muted)] transition-all';
    div.dataset.index = index;
    const activeType = document.querySelector('input[name="tipe"]:checked')?.value || 'pilihan_ganda';
    const isMulti = activeType === 'multi_select';
    div.innerHTML = `
        <div class="correct-indicator shrink-0 flex items-center justify-center pl-1">
            <input type="radio" name="jawaban_benar" value="${index}" class="pg-radio w-4 h-4 text-primary border-[var(--border)] focus:ring-primary cursor-pointer ${isMulti ? 'hidden' : ''}" title="Tandai sebagai kunci benar">
            <input type="checkbox" name="jawaban_benar[]" value="${index}" class="ms-checkbox w-4 h-4 text-primary border-[var(--border)] focus:ring-primary rounded cursor-pointer ${!isMulti ? 'hidden' : ''}" title="Tandai sebagai kunci benar">
        </div>
        <span class="w-6 h-6 rounded-lg bg-[var(--muted)] text-[var(--text-secondary)] flex items-center justify-center text-xs font-bold shrink-0">${label}</span>
        <input type="text" name="pilihan[]" placeholder="Teks pilihan ${label}" required
            class="flex-1 px-3 py-2 bg-[var(--input)] border border-[var(--border)] rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none text-xs text-[var(--text-primary)] transition-all">
        <button type="button" onclick="removeChoice(this)" class="p-1.5 text-[var(--text-muted)] hover:text-rose-600 hover:bg-rose-500/10 rounded-lg transition-colors" title="Hapus Opsi">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>`;
    container.appendChild(div);
    if (window.lucide) {
        window.lucide.createIcons();
    }
});

function removeChoice(btn) {
    const row = btn.closest('.choice-row');
    if (document.querySelectorAll('.choice-row').length <= 2) return alert('Minimal 2 pilihan jawaban.');
    row.remove();
    reindexChoices();
}

function reindexChoices() {
    const rows = document.querySelectorAll('#choices-container .choice-row');
    rows.forEach((row, i) => {
        const label = labels[i] || (i + 1);
        row.dataset.index = i;
        const radio = row.querySelector('.pg-radio');
        const check = row.querySelector('.ms-checkbox');
        const span = row.querySelector('span');
        const input = row.querySelector('input[name="pilihan[]"]');
        if (radio) radio.value = i;
        if (check) check.value = i;
        if (span) span.textContent = label;
        if (input) input.placeholder = `Teks pilihan ${label}`;
    });
}

document.getElementById('btn-add-pair')?.addEventListener('click', function() {
    const rows = document.querySelectorAll('#pairs-container .pair-row');
    const pairCount = rows.length + 1;
    const container = document.getElementById('pairs-container');
    const div = document.createElement('div');
    div.className = 'grid grid-cols-11 gap-2 pair-row items-center';
    div.innerHTML = `
        <input type="text" name="pasangan_kiri[]" placeholder="Item kiri ${pairCount}" required
            class="col-span-5 px-3 py-2 bg-[var(--input)] border border-[var(--border)] rounded-lg text-xs text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
        <div class="col-span-1 flex items-center justify-center text-[var(--text-secondary)] font-bold">&rarr;</div>
        <input type="text" name="pasangan_kanan[]" placeholder="Pasangan kanan ${pairCount}" required
            class="col-span-4 px-3 py-2 bg-[var(--input)] border border-[var(--border)] rounded-lg text-xs text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
        <button type="button" onclick="removePair(this)" class="col-span-1 flex items-center justify-center p-1.5 text-[var(--text-muted)] hover:text-rose-600 hover:bg-rose-500/10 rounded-lg transition-colors">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>`;
    container.appendChild(div);
    if (window.lucide) {
        window.lucide.createIcons();
    }
});

function removePair(btn) {
    const rows = document.querySelectorAll('#pairs-container .pair-row');
    if (rows.length <= 2) return alert('Minimal 2 pasangan.');
    btn.closest('.pair-row').remove();
    reindexPairs();
}

function reindexPairs() {
    const rows = document.querySelectorAll('#pairs-container .pair-row');
    rows.forEach((row, i) => {
        const left = row.querySelector('input[name="pasangan_kiri[]"]');
        const right = row.querySelector('input[name="pasangan_kanan[]"]');
        if (left) left.placeholder = `Item kiri ${i + 1}`;
        if (right) right.placeholder = `Pasangan kanan ${i + 1}`;
    });
}

document.getElementById('soal-form')?.addEventListener('submit', function(e) {
    const activeType = document.querySelector('input[name="tipe"]:checked')?.value || 'pilihan_ganda';
    if (activeType === 'pilihan_ganda') {
        const checked = document.querySelector('input[name="jawaban_benar"]:checked');
        if (!checked) {
            e.preventDefault();
            alert('Silakan pilih salah satu opsi sebagai kunci jawaban benar.');
            return false;
        }
    } else if (activeType === 'multi_select') {
        const checked = document.querySelectorAll('input[name="jawaban_benar[]"]:checked');
        if (checked.length === 0) {
            e.preventDefault();
            alert('Silakan pilih setidaknya satu opsi sebagai kunci jawaban benar.');
            return false;
        }
    }
});
</script>
@endpush
