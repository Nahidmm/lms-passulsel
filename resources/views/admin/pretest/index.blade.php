@extends('layouts.app')
@section('title', 'Kelola Pretest & Diagnostik - STRAPSUSPAS')

@section('content')
<div class="space-y-6 py-2">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-xs text-[var(--text-secondary)]">
        <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 transition-colors font-medium">Dashboard</a>
        <span>/</span>
        <span class="text-[var(--text-primary)] font-semibold">Kelola Pretest</span>
    </div>

    <div>
        <h1 class="text-2xl font-extrabold text-[var(--text-primary)]">Kelola Pretest Awal & Diagnostik Topik</h1>
        <p class="text-xs text-[var(--text-secondary)] mt-1">Konfigurasi asesmen awal, bank soal indikator, pemetaan topik kelemahan, dan rekomendasi modul otomatis.</p>
    </div>

    {{-- TABS --}}
    <div class="flex space-x-2 border-b border-[var(--border)] overflow-x-auto whitespace-nowrap" id="tabs">
        <button class="tab-btn active px-4 py-2.5 text-xs font-bold text-indigo-600 dark:text-indigo-400 border-b-2 border-indigo-600 dark:border-indigo-400 transition-colors" data-target="tab-soal">
            Bank Soal Pretest ({{ $soals->count() }})
        </button>
        <button class="tab-btn px-4 py-2.5 text-xs font-bold text-[var(--text-secondary)] border-b-2 border-transparent hover:text-[var(--text-primary)] transition-colors" data-target="tab-topik">
            Topik & Rekomendasi Modul
        </button>
        <button class="tab-btn px-4 py-2.5 text-xs font-bold text-[var(--text-secondary)] border-b-2 border-transparent hover:text-[var(--text-primary)] transition-colors" data-target="tab-pengaturan">
            Pengaturan Asesmen
        </button>
    </div>

    {{-- TAB 1: DAFTAR SOAL --}}
    <div id="tab-soal" class="tab-content block space-y-4">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <div>
                <h2 class="text-sm font-bold text-[var(--text-primary)]">Daftar Pertanyaan Pretest</h2>
                <p class="text-xs text-[var(--text-secondary)]">Soal yang akan diujikan saat pertama kali peserta mendaftar</p>
            </div>
            <a href="{{ route('admin.materi.soal.create', $pretest->id) }}" class="btn btn-primary text-xs px-4 py-2">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Soal Baru
            </a>
        </div>
        
        <div class="rounded-2xl border border-[var(--border)] bg-[var(--card)] divide-y divide-[var(--border)] overflow-hidden shadow-xs">
            @forelse($soals as $index => $soal)
            <div class="p-4 md:p-5 hover:bg-[var(--card-hover)] transition-colors">
                <div class="flex justify-between items-start gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <span class="inline-flex items-center text-xs font-bold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-[var(--text-primary)]">
                                No. {{ $index + 1 }}
                            </span>
                            @if($soal->topik)
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                    <i data-lucide="tag" class="w-3 h-3"></i> {{ $soal->topik->nama_topik }}
                                </span>
                            @else
                                <span class="inline-flex items-center text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                                    Tanpa Topik
                                </span>
                            @endif
                            <span class="text-[10px] uppercase font-bold text-[var(--text-muted)] border border-[var(--border)] rounded px-1.5 py-0.5">
                                {{ strtoupper(str_replace('_', ' ', $soal->tipe)) }}
                            </span>
                        </div>
                        <div class="text-[var(--text-primary)] text-xs md:text-sm leading-relaxed font-medium">
                            {!! strip_tags($soal->pertanyaan, '<b><i><u><br>') !!}
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <a href="{{ route('admin.soal.edit', $soal->id) }}" class="p-2 rounded-lg border border-[var(--border)] hover:bg-slate-100 dark:hover:bg-slate-800 text-[var(--text-secondary)] hover:text-indigo-600 transition-colors" title="Edit Soal">
                            <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                        </a>
                        <form action="{{ route('admin.soal.destroy', $soal->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus soal ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-2 rounded-lg border border-[var(--border)] hover:bg-rose-50 dark:hover:bg-rose-950/40 text-[var(--text-secondary)] hover:text-rose-600 transition-colors" title="Hapus Soal">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-10 text-center text-[var(--text-muted)]">
                <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-2 opacity-40"></i>
                <p class="text-xs">Belum ada bank soal pretest terdaftar.</p>
                <a href="{{ route('admin.materi.soal.create', $pretest->id) }}" class="text-indigo-600 hover:underline text-xs mt-2 inline-block font-semibold">+ Tambah Soal Pertama</a>
            </div>
            @endforelse
        </div>
    </div>

    {{-- TAB 2: TOPIK & REKOMENDASI --}}
    <div id="tab-topik" class="tab-content hidden space-y-4">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <div>
                <h2 class="text-sm font-bold text-[var(--text-primary)]">Indikator Topik & Rekomendasi Pelatihan</h2>
                <p class="text-xs text-[var(--text-secondary)]">Peserta yang memiliki skor di bawah ambang batas topik akan diarahkan ke kursus terkait</p>
            </div>
            <button onclick="openModalTopik()" class="btn btn-primary text-xs px-4 py-2">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Topik Baru
            </button>
        </div>

        <div class="rounded-2xl border border-[var(--border)] bg-[var(--card)] overflow-hidden shadow-xs">
            <table class="w-full text-left">
                <thead class="border-b border-[var(--border)] bg-slate-50/50 dark:bg-slate-900/50">
                    <tr>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)]">Nama Topik / Indikator</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] text-center">Batas Nilai</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)]">Rekomendasi Kursus</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    @forelse($topiks as $topik)
                    <tr class="hover:bg-[var(--card-hover)] transition-colors">
                        <td class="p-4 text-xs font-bold text-[var(--text-primary)]">{{ $topik->nama_topik }}</td>
                        <td class="p-4 text-xs text-center font-bold text-rose-600">
                            {{ $topik->batas_nilai }}
                        </td>
                        <td class="p-4 text-xs text-[var(--text-secondary)]">
                            {{ $topik->pelatihan ? $topik->pelatihan->judul : 'Belum dihubungkan' }}
                        </td>
                        <td class="p-4 text-right">
                            <button onclick="editTopik({{ $topik->id }}, '{{ addslashes($topik->nama_topik) }}', {{ $topik->batas_nilai }}, {{ $topik->pelatihan_id ?? 'null' }})" class="p-1.5 rounded-lg border border-[var(--border)] hover:bg-slate-100 text-[var(--text-secondary)] hover:text-indigo-600 transition-colors mx-1" title="Edit">
                                <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                            </button>
                            <form action="{{ route('admin.pretest.topik.destroy', $topik->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus topik ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg border border-[var(--border)] hover:bg-rose-50 text-[var(--text-secondary)] hover:text-rose-600 transition-colors mx-1" title="Hapus">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-xs text-[var(--text-muted)]">Belum ada topik evaluasi diagnostik yang dikonfigurasi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- TAB 3: PENGATURAN UMUM --}}
    <div id="tab-pengaturan" class="tab-content hidden space-y-4">
        <form action="{{ route('admin.pretest.setting') }}" method="POST" class="rounded-2xl border border-[var(--border)] bg-[var(--card)] p-6 md:p-8 space-y-5 max-w-2xl shadow-xs">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Judul Pretest</label>
                <input type="text" name="judul" value="{{ $pretest->judul }}" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-[var(--border)] bg-[var(--surface)] text-[var(--text-primary)] focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Deskripsi / Petunjuk Pelaksanaan</label>
                <textarea name="deskripsi" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-[var(--border)] bg-[var(--surface)] text-[var(--text-primary)] focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none resize-none leading-relaxed" rows="3">{{ $pretest->deskripsi }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Batas Waktu Asesmen (Menit)</label>
                <input type="number" name="durasi_menit" value="{{ $pretest->durasi_menit }}" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-[var(--border)] bg-[var(--surface)] text-[var(--text-primary)] focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" min="0">
                <p class="text-[11px] text-[var(--text-muted)] mt-1">Ketik 0 jika asesmen pretest tidak memiliki batasan waktu.</p>
            </div>
            <div class="p-3.5 rounded-xl border border-[var(--border)] bg-slate-50/50 dark:bg-slate-900/50 flex items-center gap-3">
                <input type="checkbox" name="is_active" id="is_active_pretest" value="1" {{ $pretest->is_active ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 rounded border-[var(--border)] focus:ring-indigo-500">
                <div>
                    <label for="is_active_pretest" class="text-xs font-bold text-[var(--text-primary)] block cursor-pointer">Pretest Aktif Wajib</label>
                    <p class="text-[11px] text-[var(--text-secondary)]">Pengguna baru wajib menyelesaikan pretest ini sebelum mengakses modul Hukdis.</p>
                </div>
            </div>
            <div class="pt-4 border-t border-[var(--border)] flex justify-end">
                <button type="submit" class="btn btn-primary text-xs px-5 py-2.5">Simpan Pengaturan Pretest</button>
            </div>
        </form>
    </div>

</div>

{{-- Modal Tambah / Edit Topik --}}
<div id="modal-topik" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-[var(--surface)] border border-[var(--border)] rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="flex items-center justify-between p-5 border-b border-[var(--border)] bg-slate-50/50 dark:bg-slate-900/50">
            <h3 class="font-bold text-[var(--text-primary)] text-sm" id="modal-topik-title">Tambah Topik Evaluasi</h3>
            <button type="button" onclick="closeModalTopik()" class="text-[var(--text-muted)] hover:text-[var(--text-primary)]">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form id="form-topik" method="POST" action="{{ route('admin.pretest.topik.store') }}">
            @csrf
            <input type="hidden" name="_method" id="method-topik" value="POST">
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Nama Topik / Kompetensi</label>
                    <input type="text" name="nama_topik" id="nama_topik" placeholder="Contoh: Kewajiban Masuk Kerja & Jam Kerja" class="w-full px-3.5 py-2 text-xs rounded-xl border border-[var(--border)] bg-[var(--surface)] text-[var(--text-primary)] focus:border-indigo-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Batas Nilai Lulus (0-100)</label>
                    <input type="number" name="batas_nilai" id="batas_nilai" class="w-full px-3.5 py-2 text-xs rounded-xl border border-[var(--border)] bg-[var(--surface)] text-[var(--text-primary)] focus:border-indigo-500 outline-none" min="0" max="100" value="70" required>
                    <p class="text-[11px] text-[var(--text-muted)] mt-1">Jika rata-rata jawaban peserta pada topik ini < nilai batas, modul terkait akan direkomendasikan.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Rekomendasi Pelatihan Disiplin</label>
                    <select name="pelatihan_id" id="pelatihan_id" class="w-full px-3.5 py-2 text-xs rounded-xl border border-[var(--border)] bg-[var(--surface)] text-[var(--text-primary)] focus:border-indigo-500 outline-none">
                        <option value="">-- Tidak Ada Rekomendasi --</option>
                        @foreach($pelatihans as $p)
                            <option value="{{ $p->id }}">{{ $p->judul }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 p-4 border-t border-[var(--border)] bg-slate-50/50 dark:bg-slate-900/50">
                <button type="button" onclick="closeModalTopik()" class="btn btn-secondary text-xs px-4 py-2">Batal</button>
                <button type="submit" class="btn btn-primary text-xs px-4 py-2">Simpan Topik</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Tab switching logic
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('active', 'border-indigo-600', 'text-indigo-600', 'dark:text-indigo-400', 'dark:border-indigo-400');
                b.classList.add('border-transparent', 'text-[var(--text-secondary)]');
            });
            document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
            
            btn.classList.add('active', 'border-indigo-600', 'text-indigo-600', 'dark:text-indigo-400', 'dark:border-indigo-400');
            btn.classList.remove('border-transparent', 'text-[var(--text-secondary)]');
            document.getElementById(btn.dataset.target)?.classList.remove('hidden');
        });
    });

    function openModalTopik() {
        document.getElementById('modal-topik-title').innerText = 'Tambah Topik Diagnostik';
        document.getElementById('form-topik').action = '{{ route("admin.pretest.topik.store") }}';
        document.getElementById('method-topik').value = 'POST';
        document.getElementById('nama_topik').value = '';
        document.getElementById('batas_nilai').value = '70';
        document.getElementById('pelatihan_id').value = '';
        document.getElementById('modal-topik').classList.remove('hidden');
    }

    function editTopik(id, nama, batas, pelatihanId) {
        document.getElementById('modal-topik-title').innerText = 'Edit Topik Diagnostik';
        document.getElementById('form-topik').action = '/admin/pretest/topik/' + id;
        document.getElementById('method-topik').value = 'PUT';
        document.getElementById('nama_topik').value = nama;
        document.getElementById('batas_nilai').value = batas;
        document.getElementById('pelatihan_id').value = pelatihanId || '';
        document.getElementById('modal-topik').classList.remove('hidden');
    }

    function closeModalTopik() {
        document.getElementById('modal-topik').classList.add('hidden');
    }
</script>
@endpush
