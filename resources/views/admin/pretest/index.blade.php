@extends('layouts.app')
@section('title', 'Kelola Pretest & Rekomendasi')

@section('content')
<div class="space-y-5 py-1">

    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="sep">/</span>
        <span class="current">Kelola Pretest</span>
    </div>

    <div>
        <h1 class="text-2xl font-black text-[var(--text-primary)]">Kelola Pretest Awal</h1>
        <p class="text-[var(--text-secondary)] text-sm mt-1">Konfigurasi asesmen awal, topik pemahaman, dan rekomendasi pelatihan.</p>
    </div>

    <!-- TABS -->
    <div class="flex space-x-1 border-b border-[var(--border)] overflow-x-auto whitespace-nowrap hide-scrollbar" id="tabs">
        <button class="tab-btn active px-4 py-2 text-sm font-bold text-[var(--text-primary)] border-b-2 border-violet-500" data-target="tab-soal">Daftar Soal</button>
        <button class="tab-btn px-4 py-2 text-sm font-bold text-[var(--text-secondary)] border-b-2 border-transparent hover:text-[var(--text-primary)]" data-target="tab-topik">Topik & Rekomendasi</button>
        <button class="tab-btn px-4 py-2 text-sm font-bold text-[var(--text-secondary)] border-b-2 border-transparent hover:text-[var(--text-primary)]" data-target="tab-pengaturan">Pengaturan Umum</button>
    </div>

    <!-- TAB: DAFTAR SOAL -->
    <div id="tab-soal" class="tab-content block space-y-4 mt-4">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <h2 class="text-lg font-bold text-[var(--text-primary)]">Soal Pretest</h2>
            <a href="{{ route('admin.materi.soal.create', $pretest->id) }}" class="btn btn-primary text-sm px-4 py-2">
                <i data-lucide="plus" class="w-4 h-4 mr-1"></i> Tambah Soal Baru
            </a>
        </div>
        
        <div class="game-card">
            @forelse($soals as $index => $soal)
            <div class="p-4 border-b border-[var(--border)] last:border-0 hover:bg-[var(--card)] transition-colors">
                <div class="flex justify-between items-start gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="badge badge-cyan text-xs">Soal {{ $index + 1 }}</span>
                            @if($soal->topik)
                                <span class="badge badge-violet text-xs"><i data-lucide="tag" class="w-3 h-3 inline mr-1"></i> {{ $soal->topik->nama_topik }}</span>
                            @else
                                <span class="badge badge-rose text-xs">Tanpa Topik</span>
                            @endif
                            <span class="text-xs text-[var(--text-secondary)] border border-[var(--border)] rounded px-1">{{ strtoupper(str_replace('_', ' ', $soal->tipe)) }}</span>
                        </div>
                        <div class="text-[var(--text-primary)] text-sm">
                            {!! strip_tags($soal->pertanyaan, '<b><i><u><br>') !!}
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('admin.soal.edit', $soal->id) }}" class="action-btn is-edit" title="Edit Soal">
                            <i data-lucide="edit-2" class="w-4 h-4"></i>
                        </a>
                        <form action="{{ route('admin.soal.destroy', $soal->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus soal ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="action-btn is-delete" title="Hapus Soal">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-[var(--text-muted)]">
                <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-3 opacity-50"></i>
                <p>Belum ada soal pretest.</p>
                <a href="{{ route('admin.materi.soal.create', $pretest->id) }}" class="text-violet-400 hover:underline mt-2 inline-block">Mulai buat soal pertama</a>
            </div>
            @endforelse
        </div>
    </div>

    <!-- TAB: TOPIK -->
    <div id="tab-topik" class="tab-content hidden space-y-4 mt-4">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <h2 class="text-lg font-bold text-[var(--text-primary)]">Kelola Topik & Rekomendasi Pelatihan</h2>
            <button onclick="openModalTopik()" class="btn btn-success text-sm px-4 py-2">
                <i data-lucide="plus" class="w-4 h-4 mr-1"></i> Tambah Topik
            </button>
        </div>

        <div class="game-card overflow-x-auto">
            <table class="w-full text-left">
                <thead class="border-b border-[var(--border)] bg-[var(--card)]">
                    <tr>
                        <th class="p-4 text-xs font-black uppercase text-[var(--text-muted)]">Nama Topik</th>
                        <th class="p-4 text-xs font-black uppercase text-[var(--text-muted)] text-center">Batas Nilai Lulus</th>
                        <th class="p-4 text-xs font-black uppercase text-[var(--text-muted)]">Rekomendasi Pelatihan (Jika Gagal)</th>
                        <th class="p-4 text-xs font-black uppercase text-[var(--text-muted)] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    @forelse($topiks as $topik)
                    <tr class="hover:bg-[var(--card)] transition">
                        <td class="p-4 text-sm text-[var(--text-primary)] font-bold">{{ $topik->nama_topik }}</td>
                        <td class="p-4 text-sm text-center">
                            <span class="badge badge-rose">{{ $topik->batas_nilai }}</span>
                        </td>
                        <td class="p-4 text-sm text-[var(--text-secondary)]">
                            {{ $topik->pelatihan ? $topik->pelatihan->judul : '-' }}
                        </td>
                        <td class="p-4 text-right">
                            <button onclick="editTopik({{ $topik->id }}, '{{ addslashes($topik->nama_topik) }}', {{ $topik->batas_nilai }}, {{ $topik->pelatihan_id ?? 'null' }})" class="action-btn is-edit mx-1">
                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                            </button>
                            <form action="{{ route('admin.pretest.topik.destroy', $topik->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus topik ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="action-btn is-delete mx-1">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-6 text-center text-[var(--text-muted)]">Belum ada topik yang dibuat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB: PENGATURAN -->
    <div id="tab-pengaturan" class="tab-content hidden space-y-4 mt-4">
        <form action="{{ route('admin.pretest.setting') }}" method="POST" class="game-card p-6 space-y-5 max-w-2xl">
            @csrf
            <div>
                <label class="block text-xs font-black uppercase text-[var(--text-muted)] mb-2">Judul Pretest</label>
                <input type="text" name="judul" value="{{ $pretest->judul }}" class="form-input w-full" required>
            </div>
            <div>
                <label class="block text-xs font-black uppercase text-[var(--text-muted)] mb-2">Deskripsi / Instruksi</label>
                <textarea name="deskripsi" class="form-input w-full resize-none" rows="3">{{ $pretest->deskripsi }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-black uppercase text-[var(--text-muted)] mb-2">Mode Tampilan Kuis</label>
                <select name="mode_tampilan" class="form-input w-full">
                    <option value="standard" {{ $pretest->mode_tampilan === 'standard' ? 'selected' : '' }}>Standard (Sederhana)</option>
                    <option value="interaktif" {{ $pretest->mode_tampilan === 'interaktif' ? 'selected' : '' }}>Interaktif (Gamifikasi)</option>
                    <option value="cinematic" {{ $pretest->mode_tampilan === 'cinematic' ? 'selected' : '' }}>Cinematic (Imersif)</option>
                </select>
                <p class="text-[10px] text-[var(--text-muted)] mt-1">Ubah mode visual kuis pretest yang akan dilihat oleh peserta.</p>
            </div>
            <div>
                <label class="block text-xs font-black uppercase text-[var(--text-muted)] mb-2">Batas Waktu (Menit)</label>
                <input type="number" name="durasi_menit" value="{{ $pretest->durasi_menit }}" class="form-input w-full" min="0">
                <p class="text-[10px] text-[var(--text-muted)] mt-1">Isi 0 jika tidak ada batas waktu.</p>
            </div>
            <div>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ $pretest->is_active ? 'checked' : '' }} class="form-checkbox bg-[var(--surface)] border-[var(--border)] text-violet-500 rounded focus:ring-violet-500">
                    <span class="text-sm font-bold text-[var(--text-primary)]">Pretest Aktif</span>
                </label>
                <p class="text-xs text-[var(--text-secondary)] mt-1 ml-7">Jika dinonaktifkan, user baru tidak akan diwajibkan mengikuti pretest.</p>
            </div>
            <div class="pt-4 border-t border-[var(--border)] text-right">
                <button type="submit" class="btn btn-primary px-6">Simpan Pengaturan</button>
            </div>
        </form>
    </div>

</div>

<!-- Modal Topik -->
<div id="modal-topik" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-[var(--surface)] border border-[var(--border)] rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between p-5 border-b border-[var(--border)]">
            <h3 class="font-black text-[var(--text-primary)] text-base" id="modal-topik-title">Tambah Topik</h3>
            <button type="button" onclick="closeModalTopik()" class="text-[var(--text-muted)] hover:text-[var(--text-primary)] transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="form-topik" method="POST" action="{{ route('admin.pretest.topik.store') }}">
            @csrf
            <input type="hidden" name="_method" id="method-topik" value="POST">
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-xs font-black uppercase text-[var(--text-muted)] mb-2">Nama Topik</label>
                    <input type="text" name="nama_topik" id="nama_topik" class="form-input w-full" required>
                </div>
                <div>
                    <label class="block text-xs font-black uppercase text-[var(--text-muted)] mb-2">Batas Nilai Lulus (0-100)</label>
                    <input type="number" name="batas_nilai" id="batas_nilai" class="form-input w-full" min="0" max="100" value="70" required>
                    <p class="text-[10px] text-[var(--text-muted)] mt-1">Jika nilai peserta pada topik ini di bawah batas, maka pelatihan direkomendasikan.</p>
                </div>
                <div>
                    <label class="block text-xs font-black uppercase text-[var(--text-muted)] mb-2">Rekomendasi Pelatihan</label>
                    <select name="pelatihan_id" id="pelatihan_id" class="form-input w-full">
                        <option value="">-- Tidak Ada --</option>
                        @foreach($pelatihans as $p)
                            <option value="{{ $p->id }}">{{ $p->judul }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3 p-5 border-t border-[var(--border)] bg-[var(--card)] rounded-b-2xl">
                <button type="button" onclick="closeModalTopik()" class="btn btn-ghost text-sm px-4 py-2">Batal</button>
                <button type="submit" class="btn btn-success text-sm px-4 py-2">Simpan</button>
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
            // reset all buttons
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('active', 'border-violet-500', 'text-white');
                b.classList.add('border-transparent', 'text-[var(--text-secondary)]');
            });
            // hide all contents
            document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
            
            // activate clicked
            btn.classList.add('active', 'border-violet-500', 'text-white');
            btn.classList.remove('border-transparent', 'text-[var(--text-secondary)]');
            document.getElementById(btn.dataset.target).classList.remove('hidden');
        });
    });

    function openModalTopik() {
        document.getElementById('modal-topik-title').innerText = 'Tambah Topik';
        document.getElementById('form-topik').action = '{{ route("admin.pretest.topik.store") }}';
        document.getElementById('method-topik').value = 'POST';
        document.getElementById('nama_topik').value = '';
        document.getElementById('batas_nilai').value = '70';
        document.getElementById('pelatihan_id').value = '';
        document.getElementById('modal-topik').classList.remove('hidden');
    }

    function editTopik(id, nama, batas, pelatihanId) {
        document.getElementById('modal-topik-title').innerText = 'Edit Topik';
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
