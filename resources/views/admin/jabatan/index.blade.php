@extends('layouts.app')

@section('title', 'Kelola Jabatan')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-display font-bold text-text-primary">Kelola Jabatan</h1>
        <p class="text-text-secondary mt-1">Manajemen daftar jabatan yang digunakan dalam sistem.</p>
    </div>
    <button onclick="document.getElementById('modal-tambah').classList.remove('hidden')"
            class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-[var(--text-primary)] font-bold py-2.5 px-5 rounded-xl shadow-sm transition-all text-sm">
        <i data-lucide="plus" class="w-4 h-4"></i> Tambah Jabatan
    </button>
</div>

{{-- Table --}}
<div class="bg-[var(--card)] border border-[var(--border)] shadow-sm rounded-xl border border-border shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-secondary text-xs text-text-secondary uppercase border-b border-border">
                <tr>
                    <th class="px-5 py-3 w-10">No</th>
                    <th class="px-5 py-3">Kode Eselon</th>
                    <th class="px-5 py-3">Nama Jabatan</th>
                    <th class="px-5 py-3 text-center">Pengguna</th>
                    <th class="px-5 py-3 text-center">Status</th>
                    <th class="px-5 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jabatans as $j)
                <tr class="border-b border-border hover:bg-secondary/30 transition-colors group">
                    <td class="px-5 py-3.5 text-text-secondary text-center">{{ $loop->iteration }}</td>
                    <td class="px-5 py-3.5">
                        <span class="font-mono font-bold text-primary bg-primary/5 px-2.5 py-1 rounded-lg text-xs">{{ $j->kode_eselon }}</span>
                    </td>
                    <td class="px-5 py-3.5">
                        <p class="font-bold text-text-primary">{{ $j->nama_jabatan }}</p>
                        @if($j->tupoksi_deskripsi)
                            <p class="text-xs text-text-secondary mt-0.5 line-clamp-1">{{ $j->tupoksi_deskripsi }}</p>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        <span class="font-bold text-text-primary">{{ $j->users_count }}</span>
                        <span class="text-text-secondary text-xs"> pengguna</span>
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        <form action="{{ route('admin.jabatan.toggle', $j->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold transition-colors
                                {{ $j->is_active ? 'bg-success/10 text-success hover:bg-success/20' : 'bg-[#13161c] text-gray-500 hover:bg-[#1c202a]' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $j->is_active ? 'bg-success' : 'bg-gray-400' }}"></span>
                                {{ $j->is_active ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick="openEditModal({{ $j->id }}, '{{ $j->kode_eselon }}', '{{ addslashes($j->nama_jabatan) }}', '{{ addslashes($j->tupoksi_deskripsi ?? '') }}')"
                                    class="p-2 text-primary hover:bg-primary/10 rounded-lg transition-colors" title="Edit">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </button>
                            @if($j->users_count === 0)
                            <form action="{{ route('admin.jabatan.destroy', $j->id) }}" method="POST"
                                  onsubmit="return confirm('Hapus jabatan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-danger hover:bg-danger/10 rounded-lg transition-colors" title="Hapus">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-secondary flex items-center justify-center mx-auto mb-3">
                            <i data-lucide="briefcase" class="w-5 h-5 text-text-secondary"></i>
                        </div>
                        <p class="text-text-secondary font-medium">Belum ada jabatan. Tambahkan jabatan pertama Anda.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Tambah --}}
<div id="modal-tambah" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
    <div class="bg-[var(--card)] border border-[var(--border)] shadow-sm rounded-2xl shadow-xl w-full max-w-lg">
        <div class="px-6 py-4 border-b border-border flex items-center justify-between">
            <h3 class="font-display font-bold text-text-primary text-lg">Tambah Jabatan</h3>
            <button onclick="document.getElementById('modal-tambah').classList.add('hidden')"
                    class="p-2 hover:bg-secondary rounded-xl transition-colors text-text-secondary">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form action="{{ route('admin.jabatan.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-text-primary mb-1.5">Kode Eselon <span class="text-danger">*</span></label>
                <input type="text" name="kode_eselon" required maxlength="20" placeholder="Contoh: III/a"
                       class="w-full px-4 py-2.5 border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-text-primary mb-1.5">Nama Jabatan <span class="text-danger">*</span></label>
                <input type="text" name="nama_jabatan" required maxlength="200" placeholder="Nama lengkap jabatan"
                       class="w-full px-4 py-2.5 border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-text-primary mb-1.5">Deskripsi Tupoksi</label>
                <textarea name="tupoksi_deskripsi" rows="3" placeholder="Tugas pokok dan fungsi jabatan (opsional)"
                          class="w-full px-4 py-2.5 border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary text-sm resize-none"></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-tambah').classList.add('hidden')"
                        class="px-5 py-2.5 text-sm font-bold text-text-secondary bg-secondary hover:bg-border rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 text-sm font-bold text-[var(--text-primary)] bg-primary hover:bg-primary-hover rounded-xl transition-colors shadow-sm">
                    Simpan Jabatan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
    <div class="bg-[var(--card)] border border-[var(--border)] shadow-sm rounded-2xl shadow-xl w-full max-w-lg">
        <div class="px-6 py-4 border-b border-border flex items-center justify-between">
            <h3 class="font-display font-bold text-text-primary text-lg">Edit Jabatan</h3>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')"
                    class="p-2 hover:bg-secondary rounded-xl transition-colors text-text-secondary">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="form-edit" action="" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-semibold text-text-primary mb-1.5">Kode Eselon <span class="text-danger">*</span></label>
                <input type="text" id="edit-kode-eselon" name="kode_eselon" required maxlength="20"
                       class="w-full px-4 py-2.5 border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-text-primary mb-1.5">Nama Jabatan <span class="text-danger">*</span></label>
                <input type="text" id="edit-nama-jabatan" name="nama_jabatan" required maxlength="200"
                       class="w-full px-4 py-2.5 border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-text-primary mb-1.5">Deskripsi Tupoksi</label>
                <textarea id="edit-tupoksi" name="tupoksi_deskripsi" rows="3"
                          class="w-full px-4 py-2.5 border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary text-sm resize-none"></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')"
                        class="px-5 py-2.5 text-sm font-bold text-text-secondary bg-secondary hover:bg-border rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 text-sm font-bold text-[var(--text-primary)] bg-primary hover:bg-primary-hover rounded-xl transition-colors shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openEditModal(id, kode, nama, tupoksi) {
    document.getElementById('form-edit').action = '/admin/jabatan/' + id;
    document.getElementById('edit-kode-eselon').value = kode;
    document.getElementById('edit-nama-jabatan').value = nama;
    document.getElementById('edit-tupoksi').value = tupoksi;
    document.getElementById('modal-edit').classList.remove('hidden');
}
</script>
@endpush

@endsection

