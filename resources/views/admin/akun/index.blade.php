@extends('layouts.app')

@section('title', 'Manajemen Akun')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-display font-bold text-primary">Manajemen Akun</h1>
    <p class="text-text-secondary mt-1">Kelola persetujuan pendaftaran dan akun pengguna sistem.</p>
</div>

@if(session('success_reset'))
    <div class="bg-success/10 border border-success/30 p-4 rounded-lg mb-6 shadow-sm">
        <h3 class="font-bold text-success flex items-center gap-2 mb-2">
            <i data-lucide="check-circle" class="w-5 h-5"></i> Password Berhasil Direset!
        </h3>
        <p class="text-sm text-text-primary mb-2">Harap beritahukan informasi berikut kepada pengguna:</p>
        <div class="bg-white border border-border rounded p-3 text-sm font-mono">
            <div>NIP: <span class="font-bold">{{ session('success_reset')['nip'] }}</span></div>
            <div>Nama: <span class="font-bold">{{ session('success_reset')['nama'] }}</span></div>
            <div class="mt-2 text-danger">Password Baru: <span class="font-bold text-lg bg-gray-100 px-2 rounded">{{ session('success_reset')['password_baru'] }}</span></div>
        </div>
        <p class="text-xs text-text-secondary mt-2">*Pengguna akan diminta mengganti password ini saat login pertama kali.</p>
    </div>
@endif

<!-- Pending Requests Section -->
<div class="bg-white rounded-xl shadow-sm border border-warning/30 overflow-hidden mb-8">
    <div class="p-4 border-b border-warning/20 bg-warning/5 flex items-center justify-between">
        <h3 class="font-display font-bold text-warning flex items-center gap-2">
            <i data-lucide="user-plus" class="w-5 h-5"></i> Menunggu Persetujuan
            <span class="bg-warning text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingRequests->count() }}</span>
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-text-secondary uppercase bg-secondary border-b border-border">
                <tr>
                    <th class="px-4 py-3">NIP / Nama</th>
                    <th class="px-4 py-3">Jabatan & Golongan</th>
                    <th class="px-4 py-3">Tanggal Daftar</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingRequests as $req)
                    <tr class="border-b border-border hover:bg-secondary/50">
                        <td class="px-4 py-3">
                            <p class="font-bold text-text-primary">{{ $req->nama }}</p>
                            <p class="text-text-secondary">{{ $req->nip }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-text-primary">{{ $req->jabatan->nama_jabatan ?? '-' }}</p>
                            <p class="text-text-secondary">{{ $req->golongan ?? '-' }}</p>
                        </td>
                        <td class="px-4 py-3 text-text-secondary">
                            {{ $req->created_at->format('d M Y, H:i') }}
                        </td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <form action="{{ route('admin.akun.approve', $req->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-success text-white px-3 py-1.5 rounded text-xs font-bold hover:bg-success/90 transition-colors">
                                    Setujui
                                </button>
                            </form>
                            <!-- Reject Button triggers modal -->
                            <button type="button" onclick="openRejectModal({{ $req->id }}, '{{ $req->nama }}')" class="bg-danger text-white px-3 py-1.5 rounded text-xs font-bold hover:bg-danger/90 transition-colors">
                                Tolak
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-text-secondary">Tidak ada pendaftaran yang menunggu persetujuan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Active Users Section -->
<div class="bg-white rounded-xl shadow-sm border border-border overflow-hidden">
    <div class="p-4 border-b border-border bg-secondary/50 flex items-center justify-between">
        <h3 class="font-display font-bold text-text-primary flex items-center gap-2">
            <i data-lucide="users" class="w-5 h-5 text-primary"></i> Pengguna Aktif
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-text-secondary uppercase bg-secondary border-b border-border">
                <tr>
                    <th class="px-4 py-3">NIP / Nama</th>
                    <th class="px-4 py-3">Jabatan</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr class="border-b border-border hover:bg-secondary/50">
                        <td class="px-4 py-3 flex items-center gap-3">
                            <img src="{{ $user->avatar_url }}" alt="Avatar" class="w-8 h-8 rounded-full border border-border">
                            <div>
                                <p class="font-bold text-text-primary">{{ $user->nama }}</p>
                                <p class="text-text-secondary text-xs">{{ $user->nip }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-text-primary">
                            {{ $user->jabatan->nama_jabatan ?? '-' }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-xs font-bold {{ $user->role === 'admin' ? 'bg-primary/10 text-primary' : 'bg-gray-100 text-gray-600' }} uppercase">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <form action="{{ route('admin.akun.reset-password', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin mereset password user ini? Password baru akan digenerate otomatis.')">
                                @csrf
                                <button type="submit" class="border border-border text-text-secondary hover:text-primary hover:border-primary px-2 py-1.5 rounded text-xs transition-colors" title="Reset Password">
                                    <i data-lucide="key" class="w-4 h-4"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.akun.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus permanen user ini beserta semua data progresnya?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="border border-border text-text-secondary hover:text-danger hover:border-danger px-2 py-1.5 rounded text-xs transition-colors" title="Hapus User">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md overflow-hidden">
        <div class="p-4 border-b border-border flex justify-between items-center">
            <h3 class="font-bold text-lg text-text-primary">Tolak Pendaftaran</h3>
            <button onclick="closeRejectModal()" class="text-text-secondary hover:text-danger"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>
        <form id="rejectForm" method="POST" action="">
            @csrf
            <div class="p-4">
                <p class="text-sm mb-3 text-text-secondary">Tolak pendaftaran untuk: <strong id="rejectName" class="text-text-primary"></strong></p>
                <div>
                    <label class="block text-sm font-medium text-text-primary mb-1">Alasan Penolakan <span class="text-danger">*</span></label>
                    <textarea name="alasan_tolak" required rows="3" class="w-full px-3 py-2 border border-border rounded focus:ring-1 focus:ring-danger outline-none" placeholder="Masukkan alasan penolakan..."></textarea>
                </div>
            </div>
            <div class="p-4 border-t border-border bg-secondary/30 flex justify-end gap-2">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 text-sm font-medium border border-border rounded bg-white hover:bg-secondary">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-danger hover:bg-danger/90 rounded">Tolak Akun</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openRejectModal(id, name) {
        document.getElementById('rejectModal').classList.remove('hidden');
        document.getElementById('rejectName').innerText = name;
        document.getElementById('rejectForm').action = `/admin/akun/reject/${id}`;
    }
    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }
</script>
@endpush
