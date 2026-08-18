@extends('layouts.app')

@section('title', 'Assign Role ke Pengguna')

@section('content')

{{-- Header --}}
<div class="mb-6">
    <div class="flex items-center gap-2 text-sm text-text-secondary mb-3">
        <a href="{{ route('admin.kelola-akses.index') }}" class="hover:text-primary transition-colors">Kelola Akses</a>
        <i data-lucide="chevron-right" class="w-4 h-4"></i>
        <span class="text-text-primary font-medium">Assign Role ke Pengguna</span>
    </div>
    <h1 class="text-2xl font-display font-bold text-primary flex items-center gap-2">
        <i data-lucide="user-cog" class="w-7 h-7"></i> Assign Role ke Pengguna
    </h1>
    <p class="text-text-secondary mt-1">Kelola role kustom yang dimiliki setiap pengguna sistem.</p>
</div>

{{-- Filter & Search --}}
<div class="bg-white rounded-xl shadow-sm border border-border p-4 mb-6">
    <form method="GET" class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nama atau NIP..."
                   class="w-full pl-9 pr-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary/30 focus:border-primary outline-none">
        </div>
        <select name="base_role" class="px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary/30 focus:border-primary outline-none bg-white">
            <option value="">Semua Role Dasar</option>
            <option value="admin" @selected(request('base_role') === 'admin')>Admin</option>
            <option value="peserta" @selected(request('base_role') === 'peserta')>Peserta</option>
        </select>
        <button type="submit" class="flex items-center gap-2 bg-primary hover:bg-primary/90 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
            <i data-lucide="filter" class="w-4 h-4"></i> Filter
        </button>
        @if(request()->hasAny(['search', 'base_role']))
        <a href="{{ route('admin.kelola-akses.users') }}" class="flex items-center gap-2 border border-border hover:bg-secondary text-text-secondary text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
            <i data-lucide="x" class="w-4 h-4"></i> Reset
        </a>
        @endif
    </form>
</div>

{{-- Users Table --}}
<div class="bg-white rounded-xl shadow-sm border border-border overflow-hidden">
    <div class="p-4 border-b border-border bg-secondary/40 flex items-center justify-between">
        <h3 class="font-display font-bold text-text-primary flex items-center gap-2">
            <i data-lucide="users" class="w-5 h-5 text-primary"></i> Daftar Pengguna
        </h3>
        <span class="text-xs text-text-secondary">{{ $users->total() }} pengguna ditemukan</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-secondary/50 border-b border-border">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Pengguna</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Jabatan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Role Dasar</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Role Kustom</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-text-secondary uppercase tracking-wide">Kelola</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border/50">
                @forelse($users as $user)
                <tr class="hover:bg-secondary/30 transition-colors" id="row-user-{{ $user->id }}">
                    {{-- User Info --}}
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->nama }}" class="w-9 h-9 rounded-full border border-border">
                            <div>
                                <p class="font-semibold text-text-primary">{{ $user->nama }}</p>
                                <p class="text-xs text-text-secondary font-mono">{{ $user->nip }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- Jabatan --}}
                    <td class="px-4 py-3 text-text-secondary text-xs">{{ $user->jabatan?->nama_jabatan ?? '-' }}</td>

                    {{-- Base Role Badge --}}
                    <td class="px-4 py-3">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase
                            {{ $user->role === 'admin' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                            {{ $user->role }}
                        </span>
                    </td>

                    {{-- Custom Roles --}}
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-1" id="custom-roles-{{ $user->id }}">
                            @forelse($user->customRoles as $cr)
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 flex items-center gap-1">
                                    <i data-lucide="star" class="w-3 h-3"></i>{{ $cr->nama }}
                                </span>
                            @empty
                                <span class="text-xs text-text-secondary italic">Tidak ada</span>
                            @endforelse
                        </div>
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-3 text-right">
                        <button type="button"
                                onclick="openAssignModal({{ $user->id }}, '{{ addslashes($user->nama) }}', {{ $user->customRoles->pluck('id')->toJson() }})"
                                class="flex items-center gap-1.5 ml-auto text-xs font-semibold px-3 py-1.5 rounded-lg border border-border hover:border-primary hover:text-primary hover:bg-primary/5 transition-all">
                            <i data-lucide="settings-2" class="w-3.5 h-3.5"></i> Atur Role
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-12 text-center text-text-secondary">
                        <i data-lucide="users" class="w-10 h-10 mx-auto mb-3 opacity-30"></i>
                        <p>Tidak ada pengguna yang ditemukan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($users->hasPages())
    <div class="p-4 border-t border-border">
        {{ $users->links() }}
    </div>
    @endif
</div>

{{-- ============================== --}}
{{-- Assign Role Modal --}}
{{-- ============================== --}}
<div id="assignModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        {{-- Modal Header --}}
        <div class="px-6 py-4 border-b border-border flex items-center justify-between">
            <div>
                <h3 class="font-bold text-lg text-text-primary">Atur Role Kustom</h3>
                <p class="text-sm text-text-secondary" id="modal-username">—</p>
            </div>
            <button onclick="closeAssignModal()" class="text-text-secondary hover:text-danger transition-colors p-1">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        {{-- Modal Body --}}
        <form id="assignForm" method="POST" action="">
            @csrf
            <div class="p-6">
                <p class="text-sm text-text-secondary mb-4">Pilih satu atau lebih role kustom untuk pengguna ini. Role kustom menambah permission di atas role dasar mereka.</p>

                @if($customRoles->isEmpty())
                    <div class="text-center py-6 text-text-secondary">
                        <i data-lucide="shield-x" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
                        <p class="text-sm">Belum ada role kustom. <a href="{{ route('admin.kelola-akses.roles.create') }}" class="text-primary hover:underline font-semibold">Buat role kustom</a>.</p>
                    </div>
                @else
                    <div class="space-y-2" id="role-options">
                        @foreach($customRoles as $cr)
                        <label for="modal-role-{{ $cr->id }}"
                               class="modal-role-card flex items-center gap-3 p-3 rounded-xl border border-border cursor-pointer hover:border-primary/50 hover:bg-primary/3 transition-all">
                            <input type="checkbox"
                                   id="modal-role-{{ $cr->id }}"
                                   name="role_ids[]"
                                   value="{{ $cr->id }}"
                                   class="modal-role-checkbox w-4 h-4 rounded text-primary">
                            <div class="flex-1">
                                <p class="font-semibold text-text-primary text-sm">{{ $cr->nama }}</p>
                                <p class="text-xs text-text-secondary">{{ $cr->permissions_count ?? $cr->permissions->count() }} permission</p>
                            </div>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 font-medium">Kustom</span>
                        </label>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Modal Footer --}}
            <div class="px-6 py-4 bg-secondary/30 border-t border-border flex justify-end gap-2">
                <button type="button" onclick="closeAssignModal()"
                        class="px-4 py-2 text-sm font-semibold border border-border rounded-lg bg-white hover:bg-secondary transition-colors">
                    Batal
                </button>
                @if($customRoles->isNotEmpty())
                <button type="submit"
                        class="flex items-center gap-2 px-5 py-2 text-sm font-bold text-white bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan
                </button>
                @endif
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentUserId = null;

    function openAssignModal(userId, userName, currentRoleIds) {
        currentUserId = userId;
        document.getElementById('modal-username').textContent = userName;
        document.getElementById('assignForm').action = `/admin/kelola-akses/users/${userId}/assign`;

        // Reset checkboxes
        document.querySelectorAll('.modal-role-checkbox').forEach(cb => {
            cb.checked = currentRoleIds.includes(parseInt(cb.value));
            updateModalCardStyle(cb);
        });

        document.getElementById('assignModal').classList.remove('hidden');
        lucide.createIcons();
    }

    function closeAssignModal() {
        document.getElementById('assignModal').classList.add('hidden');
        currentUserId = null;
    }

    function updateModalCardStyle(checkbox) {
        const card = checkbox.closest('.modal-role-card');
        if (!card) return;
        if (checkbox.checked) {
            card.classList.add('border-primary', 'bg-primary/5');
            card.classList.remove('border-border');
        } else {
            card.classList.remove('border-primary', 'bg-primary/5');
            card.classList.add('border-border');
        }
    }

    document.querySelectorAll('.modal-role-checkbox').forEach(cb => {
        cb.addEventListener('change', () => updateModalCardStyle(cb));
    });

    // Close modal on backdrop click
    document.getElementById('assignModal').addEventListener('click', function(e) {
        if (e.target === this) closeAssignModal();
    });
</script>
@endpush
