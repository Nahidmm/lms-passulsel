@extends('layouts.app')
@section('title', 'Manajemen Akun')

@section('content')
<div class="space-y-5 py-1">

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="sep">/</span>
        <span class="current">Manajemen Akun</span>
    </div>

    <div>
        <h1 class="text-2xl font-black text-[var(--text-primary)]">Manajemen Akun</h1>
        <p class="text-[var(--text-secondary)] text-sm mt-1">Kelola persetujuan pendaftaran dan akun pengguna sistem.</p>
    </div>

    {{-- Password reset success --}}
    @if(session('success_reset'))
        <div class="alert alert-info">
            <i data-lucide="check-circle" class="w-5 h-5 shrink-0"></i>
            <div>
                <p class="font-bold">Password Berhasil Direset</p>
                <div class="mt-2 bg-[var(--card)] border border-[var(--border)] shadow-sm rounded-lg p-3 text-sm font-mono space-y-1">
                    <div>NIP: <span class="font-bold text-[var(--text-primary)]">{{ session('success_reset')['nip'] }}</span></div>
                    <div>Nama: <span class="font-bold text-[var(--text-primary)]">{{ session('success_reset')['nama'] }}</span></div>
                    <div class="mt-1">Password Baru: <span class="font-black text-amber-400 bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 rounded">{{ session('success_reset')['password_baru'] }}</span></div>
                </div>
                <p class="text-xs mt-2 opacity-70">*Pengguna akan diminta mengganti password ini saat login pertama kali.</p>
            </div>
        </div>
    @endif

    {{-- â•â•â• PENDING REQUESTS â•â•â• --}}
    <div class="game-card overflow-hidden">
        <div class="card-header">
            <h3>
                <i data-lucide="user-plus" class="w-4 h-4 text-amber-400"></i>
                Menunggu Persetujuan
                @if($pendingRequests->count() > 0)
                    <span class="badge badge-rose text-[9px]">{{ $pendingRequests->count() }}</span>
                @endif
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/7 bg-[var(--card)] border border-[var(--border)] shadow-sm">
                        <th class="py-3.5 px-5 text-left text-xs font-black uppercase tracking-widest text-[var(--text-muted)]">NIP / Nama</th>
                        <th class="py-3.5 px-5 text-left text-xs font-black uppercase tracking-widest text-[var(--text-muted)]">Jabatan & Golongan</th>
                        <th class="py-3.5 px-5 text-left text-xs font-black uppercase tracking-widest text-[var(--text-muted)]">Tanggal Daftar</th>
                        <th class="py-3.5 px-5 text-right text-xs font-black uppercase tracking-widest text-[var(--text-muted)]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[0.04]">
                    @forelse($pendingRequests as $req)
                        <tr class="hover:bg-[var(--card)] border border-[var(--border)] shadow-sm transition-colors">
                            <td class="py-4 px-5">
                                <p class="font-bold text-[var(--text-primary)] text-sm">{{ $req->nama }}</p>
                                <p class="text-xs text-[var(--text-muted)] font-mono mt-0.5">{{ $req->nip }}</p>
                            </td>
                            <td class="py-4 px-5">
                                <p class="text-sm text-[var(--text-primary)] font-semibold">{{ $req->jabatan->nama_jabatan ?? '-' }}</p>
                                <p class="text-xs text-[var(--text-muted)] mt-0.5">Gol. {{ $req->golongan ?? '-' }}</p>
                            </td>
                            <td class="py-4 px-5 text-sm text-[var(--text-secondary)]">{{ $req->created_at->format('d M Y, H:i') }}</td>
                            <td class="py-4 px-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form action="{{ route('admin.akun.approve', $req->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success text-xs px-3 py-2 min-h-0 h-8">
                                            <i data-lucide="check" class="w-3.5 h-3.5"></i> Setujui
                                        </button>
                                    </form>
                                    <button type="button" onclick="openRejectModal({{ $req->id }}, '{{ $req->nama }}')"
                                        class="btn btn-danger text-xs px-3 py-2 min-h-0 h-8">
                                        <i data-lucide="x" class="w-3.5 h-3.5"></i> Tolak
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-state py-8">
                                    <div class="empty-state-icon">
                                        <i data-lucide="check-circle-2" class="w-6 h-6 text-emerald-400"></i>
                                    </div>
                                    <h4>Semua Sudah Ditangani</h4>
                                    <p>Tidak ada pendaftaran yang menunggu persetujuan saat ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- â•â•â• ACTIVE USERS â•â•â• --}}
    <div class="game-card overflow-hidden">
        <div class="card-header flex items-center justify-between">
            <h3>
                <i data-lucide="users" class="w-4 h-4 text-violet-400"></i>
                Pengguna Aktif
                <span class="badge badge-violet text-[9px] ml-1">{{ $users->count() }}</span>
            </h3>
            <a href="{{ route('admin.users.import') }}" class="btn btn-primary text-xs px-3 py-2 min-h-0 h-8 flex items-center gap-1 bg-violet-600 hover:bg-violet-700 text-white rounded">
                <i data-lucide="upload" class="w-3.5 h-3.5"></i> Import Excel
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/7 bg-[var(--card)] border border-[var(--border)] shadow-sm">
                        <th class="py-3.5 px-5 text-left text-xs font-black uppercase tracking-widest text-[var(--text-muted)]">NIP / Nama</th>
                        <th class="py-3.5 px-5 text-left text-xs font-black uppercase tracking-widest text-[var(--text-muted)]">Jabatan</th>
                        <th class="py-3.5 px-5 text-left text-xs font-black uppercase tracking-widest text-[var(--text-muted)]">Role</th>
                        <th class="py-3.5 px-5 text-right text-xs font-black uppercase tracking-widest text-[var(--text-muted)]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[0.04]">
                    @foreach($users as $user)
                        <tr class="hover:bg-[var(--card)] border border-[var(--border)] shadow-sm transition-colors">
                            <td class="py-4 px-5">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $user->avatar_url }}" alt="" class="w-8 h-8 rounded-xl border border-[var(--border)] shrink-0">
                                    <div class="min-w-0">
                                        <p class="font-bold text-[var(--text-primary)] text-sm truncate">{{ $user->nama }}</p>
                                        <p class="text-xs text-[var(--text-muted)] font-mono mt-0.5">{{ $user->nip }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-5 text-sm text-[var(--text-primary)]">{{ $user->jabatan->nama_jabatan ?? '-' }}</td>
                            <td class="py-4 px-5">
                                <span class="badge {{ $user->role === 'admin' || $user->role === 'superadmin' ? 'badge-violet' : 'badge-cyan' }} text-[10px] uppercase">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="py-4 px-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form action="{{ route('admin.akun.reset-password', $user->id) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Yakin ingin mereset password user ini?')">
                                        @csrf
                                        <button type="submit" class="action-btn is-edit" title="Reset Password">
                                            <i data-lucide="key" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.akun.destroy', $user->id) }}" method="POST" class="inline"
                                          onsubmit="return confirm('BAHAYA: Yakin hapus permanen user ini dan seluruh data progresnya?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn is-delete" title="Hapus User Permanen">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-[var(--surface)] border border-[var(--border)] rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between p-5 border-b border-[var(--border)]">
            <h3 class="font-black text-[var(--text-primary)] text-base">Tolak Pendaftaran</h3>
            <button onclick="closeRejectModal()" class="text-[var(--text-muted)] hover:text-[var(--text-primary)] transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="rejectForm" method="POST" action="">
            @csrf
            <div class="p-5 space-y-4">
                <p class="text-sm text-[var(--text-secondary)]">Tolak pendaftaran untuk: <strong id="rejectName" class="text-[var(--text-primary)]"></strong></p>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-[var(--text-muted)] mb-2">
                        Alasan Penolakan <span class="text-rose-400">*</span>
                    </label>
                    <textarea name="alasan_tolak" required rows="3"
                        class="form-input resize-none"
                        placeholder="Masukkan alasan penolakan..."></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 p-5 border-t border-[var(--border)]">
                <button type="button" onclick="closeRejectModal()" class="btn btn-ghost text-sm px-4 py-2 min-h-0 h-9">Batal</button>
                <button type="submit" class="btn btn-danger text-sm px-4 py-2 min-h-0 h-9">Tolak Akun</button>
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

