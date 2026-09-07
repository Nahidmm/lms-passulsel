@extends('layouts.app')
@section('title', 'Manajemen Pengguna & Akun')

@section('content')
<div class="space-y-6">

    {{-- Top Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[var(--text-primary)]">Manajemen Pengguna & Akun</h1>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Kelola data pengguna, hak akses peran, data kepegawaian, serta persetujuan registrasi peserta.</p>
        </div>
        <div class="flex items-center flex-wrap gap-2.5">
            <button type="button" onclick="openCreateUserModal()" class="btn btn-primary text-white text-sm font-semibold py-2.5 px-4 rounded-xl flex items-center gap-2 shadow-xs transition-all">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>Tambah User Baru</span>
            </button>
            <a href="{{ route('admin.users.import') }}" class="btn btn-secondary text-[var(--text-primary)] text-sm font-medium py-2.5 px-4 rounded-xl flex items-center gap-2 shadow-xs transition-all border border-[var(--border)]">
                <i data-lucide="upload" class="w-4 h-4 text-primary"></i>
                <span>Import Excel</span>
            </a>
        </div>
    </div>

    {{-- Password reset success notification --}}
    @if(session('success_reset'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-start gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5"></i>
            <div>
                <p class="font-bold text-sm text-emerald-800 dark:text-emerald-300">Password Berhasil Direset</p>
                <div class="mt-2 bg-[var(--card)] border border-[var(--border)] rounded-xl p-3 text-xs font-mono space-y-1 shadow-2xs">
                    <div>NIP: <span class="font-bold text-[var(--text-primary)]">{{ session('success_reset')['nip'] }}</span></div>
                    <div>Nama: <span class="font-bold text-[var(--text-primary)]">{{ session('success_reset')['nama'] }}</span></div>
                    <div class="mt-1">Password Baru: <span class="font-bold text-amber-600 dark:text-amber-400 bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 rounded-md">{{ session('success_reset')['password_baru'] }}</span></div>
                </div>
                <p class="text-xs mt-2 text-[var(--text-secondary)]">*Pengguna dapat login dengan password baru ini dan wajib memperbaruinya.</p>
            </div>
        </div>
    @endif

    {{-- PENDING REQUESTS --}}
    @if($pendingRequests->count() > 0)
    <div class="bg-[var(--card)] border border-amber-500/30 rounded-2xl overflow-hidden shadow-xs">
        <div class="px-6 py-4 border-b border-amber-500/20 flex items-center justify-between bg-amber-500/5">
            <h3 class="text-sm font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="clock" class="w-4 h-4"></i>
                <span>Pendaftaran Menunggu Persetujuan</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/30">
                    {{ $pendingRequests->count() }} Permohonan
                </span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-[var(--text-secondary)] uppercase bg-[var(--muted)]/50 border-b border-[var(--border)]">
                    <tr>
                        <th class="py-3.5 px-6">NIP / Nama</th>
                        <th class="py-3.5 px-6">Jabatan & Golongan</th>
                        <th class="py-3.5 px-6">Tanggal Daftar</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    @foreach($pendingRequests as $req)
                        <tr class="hover:bg-[var(--muted)]/30 transition-colors">
                            <td class="py-4 px-6">
                                <p class="font-semibold text-[var(--text-primary)]">{{ $req->nama }}</p>
                                <p class="text-xs text-[var(--text-secondary)] font-mono mt-0.5">{{ $req->nip }}</p>
                            </td>
                            <td class="py-4 px-6">
                                <p class="text-xs font-semibold text-[var(--text-primary)]">{{ $req->jabatan->nama_jabatan ?? '-' }}</p>
                                <p class="text-xs text-[var(--text-secondary)] mt-0.5">Golongan: {{ $req->golongan ?? '-' }}</p>
                            </td>
                            <td class="py-4 px-6 text-xs text-[var(--text-secondary)]">{{ $req->created_at->format('d M Y, H:i') }}</td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form action="{{ route('admin.akun.approve', $req->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="btn btn-primary text-white text-xs font-medium py-1.5 px-3 rounded-xl shadow-xs transition-all inline-flex items-center gap-1.5">
                                            <i data-lucide="check" class="w-3.5 h-3.5"></i> Setujui
                                        </button>
                                    </form>
                                    <button type="button" onclick="openRejectModal({{ $req->id }}, '{{ $req->nama }}')"
                                        class="btn btn-secondary text-danger hover:bg-danger/10 text-xs font-medium py-1.5 px-3 rounded-xl border-danger/20 transition-all inline-flex items-center gap-1.5">
                                        <i data-lucide="x" class="w-3.5 h-3.5"></i> Tolak
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- USERS LIST & FILTER --}}
    <div class="bg-[var(--card)] border border-[var(--border)] rounded-2xl overflow-hidden shadow-xs space-y-4">
        
        {{-- Card Header & Filter --}}
        <div class="p-5 border-b border-[var(--border)] space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                        <i data-lucide="users" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-[var(--text-primary)]">Daftar Pengguna Sistem</h3>
                        <p class="text-xs text-[var(--text-secondary)]">Total {{ $users->total() }} user terdaftar</p>
                    </div>
                </div>
            </div>

            {{-- Filter Form --}}
            <form method="GET" action="{{ route('admin.akun.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 pt-2">
                <div class="sm:col-span-5 relative">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-[var(--text-secondary)]"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIP, atau email..."
                        class="w-full pl-9 pr-4 py-2 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>

                <div class="sm:col-span-3">
                    <select name="role" class="w-full px-3 py-2 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                        <option value="">Semua Peran (Role)</option>
                        <option value="peserta" {{ request('role') == 'peserta' ? 'selected' : '' }}>Peserta</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        @if(auth()->user()->isSuperadmin())
                            <option value="superadmin" {{ request('role') == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                        @endif
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <select name="status" class="w-full px-3 py-2 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                        <option value="">Semua Status</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved (Aktif)</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div class="sm:col-span-2 flex items-center gap-2">
                    <button type="submit" class="btn btn-primary text-white text-xs font-semibold py-2.5 px-4 rounded-xl flex-1 justify-center inline-flex items-center gap-1.5 shadow-xs">
                        <i data-lucide="filter" class="w-3.5 h-3.5"></i> Filter
                    </button>
                    @if(request()->hasAny(['search', 'role', 'status']))
                        <a href="{{ route('admin.akun.index') }}" class="btn btn-secondary text-xs py-2.5 px-3 rounded-xl text-[var(--text-secondary)] hover:text-[var(--text-primary)] border border-[var(--border)]" title="Reset Filter">
                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-[var(--text-secondary)] uppercase bg-[var(--muted)]/50 border-b border-[var(--border)]">
                    <tr>
                        <th class="py-3.5 px-6">Pengguna</th>
                        <th class="py-3.5 px-6">Unit Kerja & Jabatan</th>
                        <th class="py-3.5 px-6">Golongan / Kontak</th>
                        <th class="py-3.5 px-6">Peran</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    @forelse($users as $user)
                        <tr class="hover:bg-[var(--muted)]/30 transition-colors">
                            {{-- Pengguna --}}
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->nama }}" class="w-9 h-9 rounded-full object-cover border border-[var(--border)] shrink-0">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-[var(--text-primary)] truncate">{{ $user->nama }}</p>
                                        <div class="flex items-center gap-2 text-xs text-[var(--text-secondary)] font-mono mt-0.5">
                                            <span>{{ $user->nip }}</span>
                                            @if($user->email)
                                                <span>•</span>
                                                <span class="font-sans truncate max-w-[150px]">{{ $user->email }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Unit Kerja & Jabatan --}}
                            <td class="py-4 px-6 text-xs">
                                <p class="font-semibold text-[var(--text-primary)]">{{ $user->unitKerja->nama_unit ?? '-' }}</p>
                                <p class="text-[var(--text-secondary)] mt-0.5">{{ $user->jabatan->nama_jabatan ?? '-' }}</p>
                            </td>

                            {{-- Golongan & Kontak --}}
                            <td class="py-4 px-6 text-xs">
                                <p class="font-medium text-[var(--text-primary)]">Gol: {{ $user->golongan ?: '-' }}</p>
                                <p class="text-[var(--text-secondary)] mt-0.5">{{ $user->no_hp ?: '-' }}</p>
                            </td>

                            {{-- Role --}}
                            <td class="py-4 px-6">
                                @if($user->role === 'superadmin')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20 uppercase">
                                        Superadmin
                                    </span>
                                @elseif($user->role === 'admin')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary/10 text-primary border border-primary/20 uppercase">
                                        Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20 uppercase">
                                        Peserta
                                    </span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="py-4 px-6">
                                @if($user->status_akun === 'approved')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                    </span>
                                @elseif($user->status_akun === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Ditolak
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    {{-- Edit Button --}}
                                    @if(!$user->isSuperadmin() || auth()->user()->isSuperadmin())
                                        <button type="button" 
                                            onclick='openEditUserModal(@json($user))'
                                            class="inline-flex p-2 border border-[var(--border)] text-[var(--text-secondary)] hover:text-primary hover:border-primary/40 rounded-xl transition-all hover:bg-primary/5"
                                            title="Edit Data User">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </button>
                                    @endif

                                    {{-- Reset Password Button --}}
                                    @if(!$user->isSuperadmin() || auth()->user()->isSuperadmin())
                                        <form action="{{ route('admin.akun.reset-password', $user->id) }}" method="POST" class="inline"
                                            onsubmit="return confirm('Yakin ingin mereset password akun {{ $user->nama }}?')">
                                            @csrf
                                            <button type="submit" class="inline-flex p-2 border border-[var(--border)] text-[var(--text-secondary)] hover:text-amber-500 hover:border-amber-500/40 rounded-xl transition-all hover:bg-amber-500/10" title="Reset Password ke Password Acak">
                                                <i data-lucide="key" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Delete Button --}}
                                    @if(!$user->isSuperadmin() && $user->id !== auth()->id())
                                        <form action="{{ route('admin.akun.destroy', $user->id) }}" method="POST" class="inline"
                                            onsubmit="return confirm('PERINGATAN: Hapus permanen user {{ $user->nama }} dan seluruh riwayat hasil belajarnya?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex p-2 border border-[var(--border)] text-[var(--text-secondary)] hover:text-danger hover:border-danger/40 rounded-xl transition-all hover:bg-danger/10" title="Hapus User">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-[var(--text-secondary)]">
                                <i data-lucide="user-x" class="w-10 h-10 text-[var(--text-muted)] mx-auto mb-2 opacity-50"></i>
                                <p class="font-medium">Tidak ada data pengguna ditemukan.</p>
                                <p class="text-xs text-[var(--text-muted)] mt-1">Coba sesuaikan kata kunci pencarian atau filter yang dipilih.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
            <div class="p-4 border-t border-[var(--border)]">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>

{{-- MODAL TAMBAH USER --}}
<div id="modalCreateUser" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-[var(--card)] border border-[var(--border)] rounded-2xl shadow-2xl w-full max-w-2xl my-8 overflow-hidden transition-all animate-in fade-in zoom-in-95 duration-200">
        <div class="flex items-center justify-between p-5 border-b border-[var(--border)] bg-[var(--muted)]/20">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="font-bold text-[var(--text-primary)] text-base">Tambah Pengguna Baru</h3>
                    <p class="text-xs text-[var(--text-secondary)]">Lengkapi formulir di bawah untuk membuat akun baru.</p>
                </div>
            </div>
            <button type="button" onclick="closeCreateUserModal()" class="text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition-colors p-1.5 rounded-lg hover:bg-[var(--muted)]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('admin.akun.store') }}" method="POST" class="p-6 space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Nama Lengkap --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                        Nama Lengkap <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="nama" required placeholder="Contoh: Dr. H. Ahmad Fauzi, M.Si"
                        class="w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>

                {{-- NIP --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                        NIP <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="nip" required placeholder="18 digit angka NIP"
                        class="w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm font-mono focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                        Email
                    </label>
                    <input type="email" name="email" placeholder="user@lms-passulsel.id"
                        class="w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>

                {{-- No HP --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                        No. HP / WhatsApp
                    </label>
                    <input type="text" name="no_hp" placeholder="Contoh: 081234567890"
                        class="w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>

                {{-- Pangkat / Golongan --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                        Pangkat / Golongan
                    </label>
                    <input list="golongan-list" name="golongan" placeholder="Pilih atau ketik golongan..."
                        class="w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>

                {{-- Unit Kerja --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                        Unit Kerja
                    </label>
                    <select name="unit_kerja_id" class="w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                        <option value="">-- Pilih Unit Kerja --</option>
                        @foreach($unitKerjas as $uk)
                            <option value="{{ $uk->id }}">{{ $uk->nama_unit }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Jabatan --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                        Jabatan
                    </label>
                    <select name="jabatan_id" class="w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                        <option value="">-- Pilih Jabatan --</option>
                        @foreach($jabatans as $jb)
                            <option value="{{ $jb->id }}">{{ $jb->nama_jabatan }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Role --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                        Peran (Role) <span class="text-danger">*</span>
                    </label>
                    <select name="role" required class="w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                        <option value="peserta" selected>Peserta</option>
                        <option value="admin">Admin</option>
                        @if(auth()->user()->isSuperadmin())
                            <option value="superadmin">Superadmin</option>
                        @endif
                    </select>
                </div>

                {{-- Status Akun --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                        Status Akun <span class="text-danger">*</span>
                    </label>
                    <select name="status_akun" required class="w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                        <option value="approved" selected>Approved (Aktif)</option>
                        <option value="pending">Pending</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>

                {{-- Password --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                        Password Baru <span class="text-danger">*</span>
                    </label>
                    <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter"
                        class="w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>

                {{-- Alamat --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                        Alamat
                    </label>
                    <textarea name="alamat" rows="2" placeholder="Alamat tempat tinggal / kantor..."
                        class="w-full px-3.5 py-2 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none resize-none transition-all"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-[var(--border)]">
                <button type="button" onclick="closeCreateUserModal()" class="btn btn-secondary text-[var(--text-primary)] text-sm px-4 py-2.5 rounded-xl border border-[var(--border)]">
                    Batal
                </button>
                <button type="submit" class="btn btn-primary text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-xs transition-all flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Simpan Pengguna</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT USER --}}
<div id="modalEditUser" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-[var(--card)] border border-[var(--border)] rounded-2xl shadow-2xl w-full max-w-2xl my-8 overflow-hidden transition-all animate-in fade-in zoom-in-95 duration-200">
        <div class="flex items-center justify-between p-5 border-b border-[var(--border)] bg-[var(--muted)]/20">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="font-bold text-[var(--text-primary)] text-base">Edit Data Pengguna</h3>
                    <p class="text-xs text-[var(--text-secondary)]" id="editModalSubtitle">Perbarui rincian akun pengguna.</p>
                </div>
            </div>
            <button type="button" onclick="closeEditUserModal()" class="text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition-colors p-1.5 rounded-lg hover:bg-[var(--muted)]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="editUserForm" method="POST" action="" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Nama Lengkap --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                        Nama Lengkap <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="nama" id="edit_nama" required
                        class="w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>

                {{-- NIP --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                        NIP <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="nip" id="edit_nip" required
                        class="w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm font-mono focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                        Email
                    </label>
                    <input type="email" name="email" id="edit_email"
                        class="w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>

                {{-- No HP --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                        No. HP / WhatsApp
                    </label>
                    <input type="text" name="no_hp" id="edit_no_hp"
                        class="w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>

                {{-- Pangkat / Golongan --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                        Pangkat / Golongan
                    </label>
                    <input list="golongan-list" name="golongan" id="edit_golongan"
                        class="w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>

                {{-- Unit Kerja --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                        Unit Kerja
                    </label>
                    <select name="unit_kerja_id" id="edit_unit_kerja_id" class="w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                        <option value="">-- Pilih Unit Kerja --</option>
                        @foreach($unitKerjas as $uk)
                            <option value="{{ $uk->id }}">{{ $uk->nama_unit }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Jabatan --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                        Jabatan
                    </label>
                    <select name="jabatan_id" id="edit_jabatan_id" class="w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                        <option value="">-- Pilih Jabatan --</option>
                        @foreach($jabatans as $jb)
                            <option value="{{ $jb->id }}">{{ $jb->nama_jabatan }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Role --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                        Peran (Role) <span class="text-danger">*</span>
                    </label>
                    <select name="role" id="edit_role" required class="w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                        <option value="peserta">Peserta</option>
                        <option value="admin">Admin</option>
                        @if(auth()->user()->isSuperadmin())
                            <option value="superadmin">Superadmin</option>
                        @endif
                    </select>
                </div>

                {{-- Status Akun --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                        Status Akun <span class="text-danger">*</span>
                    </label>
                    <select name="status_akun" id="edit_status_akun" required class="w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                        <option value="approved">Approved (Aktif)</option>
                        <option value="pending">Pending</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>

                {{-- Ganti Password (Opsional) --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                        Password Baru <span class="text-xs font-normal text-[var(--text-secondary)] lowercase">(kosongkan jika tidak ingin mengubah password)</span>
                    </label>
                    <input type="password" name="password" minlength="6" placeholder="Biarkan kosong jika tidak diganti"
                        class="w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>

                {{-- Alamat --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                        Alamat
                    </label>
                    <textarea name="alamat" id="edit_alamat" rows="2" placeholder="Alamat tempat tinggal / kantor..."
                        class="w-full px-3.5 py-2 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none resize-none transition-all"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-[var(--border)]">
                <button type="button" onclick="closeEditUserModal()" class="btn btn-secondary text-[var(--text-primary)] text-sm px-4 py-2.5 rounded-xl border border-[var(--border)]">
                    Batal
                </button>
                <button type="submit" class="btn btn-primary text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-xs transition-all flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-[var(--card)] border border-[var(--border)] rounded-2xl shadow-xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        <div class="flex items-center justify-between p-5 border-b border-[var(--border)]">
            <h3 class="font-bold text-[var(--text-primary)] text-base">Tolak Pendaftaran</h3>
            <button onclick="closeRejectModal()" class="text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition-colors p-1 rounded-lg">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="rejectForm" method="POST" action="">
            @csrf
            <div class="p-5 space-y-4">
                <p class="text-sm text-[var(--text-secondary)]">Tolak pendaftaran untuk: <strong id="rejectName" class="text-[var(--text-primary)]"></strong></p>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                        Alasan Penolakan <span class="text-danger">*</span>
                    </label>
                    <textarea name="alasan_tolak" required rows="3"
                        class="w-full px-4 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none resize-none transition-all"
                        placeholder="Contoh: NIP tidak sesuai data kepegawaian Kanwil..."></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 p-5 border-t border-[var(--border)] bg-[var(--muted)]/30">
                <button type="button" onclick="closeRejectModal()" class="btn btn-secondary text-[var(--text-primary)] text-sm px-4 py-2 rounded-xl">Batal</button>
                <button type="submit" class="btn bg-rose-600 hover:bg-rose-700 text-white text-sm px-4 py-2 rounded-xl font-medium shadow-xs transition-all">Tolak Pendaftaran</button>
            </div>
        </form>
    </div>
</div>

{{-- Datalist Golongan --}}
<datalist id="golongan-list">
    <option value="I/a (Juru Muda)">
    <option value="I/b (Juru Muda Tk. I)">
    <option value="I/c (Juru)">
    <option value="I/d (Juru Tk. I)">
    <option value="II/a (Pengatur Muda)">
    <option value="II/b (Pengatur Muda Tk. I)">
    <option value="II/c (Pengatur)">
    <option value="II/d (Pengatur Tk. I)">
    <option value="III/a (Penata Muda)">
    <option value="III/b (Penata Muda Tk. I)">
    <option value="III/c (Penata)">
    <option value="III/d (Penata Tk. I)">
    <option value="IV/a (Pembina)">
    <option value="IV/b (Pembina Tk. I)">
    <option value="IV/c (Pembina Utama Muda)">
    <option value="IV/d (Pembina Utama Madya)">
    <option value="IV/e (Pembina Utama)">
    <option value="PPPK">
    <option value="Non-ASN">
</datalist>

@endsection

@push('scripts')
<script>
// Create User Modal
function openCreateUserModal() {
    document.getElementById('modalCreateUser').classList.remove('hidden');
    if (window.lucide) {
        window.lucide.createIcons();
    }
}
function closeCreateUserModal() {
    document.getElementById('modalCreateUser').classList.add('hidden');
}

// Edit User Modal
function openEditUserModal(user) {
    const form = document.getElementById('editUserForm');
    form.action = `/admin/akun/${user.id}`;
    
    document.getElementById('editModalSubtitle').innerText = `${user.nama} (${user.nip})`;
    document.getElementById('edit_nama').value = user.nama || '';
    document.getElementById('edit_nip').value = user.nip || '';
    document.getElementById('edit_email').value = user.email || '';
    document.getElementById('edit_no_hp').value = user.no_hp || '';
    document.getElementById('edit_golongan').value = user.golongan || '';
    document.getElementById('edit_unit_kerja_id').value = user.unit_kerja_id || '';
    document.getElementById('edit_jabatan_id').value = user.jabatan_id || '';
    document.getElementById('edit_role').value = user.role || 'peserta';
    document.getElementById('edit_status_akun').value = user.status_akun || 'approved';
    document.getElementById('edit_alamat').value = user.alamat || '';

    document.getElementById('modalEditUser').classList.remove('hidden');
    if (window.lucide) {
        window.lucide.createIcons();
    }
}
function closeEditUserModal() {
    document.getElementById('modalEditUser').classList.add('hidden');
}

// Reject Modal
function openRejectModal(id, name) {
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('rejectName').innerText = name;
    document.getElementById('rejectForm').action = `/admin/akun/reject/${id}`;
    if (window.lucide) {
        window.lucide.createIcons();
    }
}
function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

// Close modals when clicking outside
window.addEventListener('click', function(e) {
    const createModal = document.getElementById('modalCreateUser');
    const editModal = document.getElementById('modalEditUser');
    const rejectModal = document.getElementById('rejectModal');
    
    if (e.target === createModal) closeCreateUserModal();
    if (e.target === editModal) closeEditUserModal();
    if (e.target === rejectModal) closeRejectModal();
});
</script>
@endpush
