@extends('layouts.app')

@section('title', 'Kelola Akses Fitur')

@push('styles')
<style>
    .tab-btn.active { @apply bg-primary text-white shadow-sm; }
    .tab-btn { @apply px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-text-secondary hover:bg-primary/10 hover:text-primary; }
    .badge-superadmin { @apply bg-purple-100 text-purple-700; }
    .badge-admin      { @apply bg-violet-900/30 text-[#fcd34d]; }
    .badge-peserta    { @apply bg-green-100 text-green-700; }
    .badge-custom     { @apply bg-amber-100 text-amber-700; }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-display font-bold text-primary flex items-center gap-2">
            <i data-lucide="shield-check" class="w-7 h-7"></i> Kelola Akses Fitur
        </h1>
        <p class="text-text-secondary mt-1">Buat role kustom, atur permission, dan assign ke pengguna sistem.</p>
    </div>
    <a href="{{ route('admin.kelola-akses.roles.create') }}"
       class="inline-flex items-center gap-2 bg-primary hover:bg-primary/90 text-[var(--text-primary)] text-sm font-bold px-5 py-2.5 rounded-lg transition-colors shadow-sm shrink-0">
        <i data-lucide="plus" class="w-4 h-4"></i> Buat Role Baru
    </a>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-[var(--card)] border border-[var(--border)] shadow-sm rounded-xl border border-border p-4 shadow-sm hover:border-primary transition-colors">
        <div class="flex items-center gap-3 mb-1">
            <div class="w-9 h-9 bg-primary/10 rounded-lg flex items-center justify-center text-primary">
                <i data-lucide="shield" class="w-4 h-4"></i>
            </div>
            <span class="text-xs font-semibold text-text-secondary uppercase tracking-wide">Total Role</span>
        </div>
        <p class="text-3xl font-display font-bold text-text-primary mt-2">{{ $stats['total_roles'] }}</p>
    </div>
    <div class="bg-[var(--card)] border border-[var(--border)] shadow-sm rounded-xl border border-border p-4 shadow-sm hover:border-accent transition-colors">
        <div class="flex items-center gap-3 mb-1">
            <div class="w-9 h-9 bg-accent/10 rounded-lg flex items-center justify-center text-accent-hover">
                <i data-lucide="key" class="w-4 h-4"></i>
            </div>
            <span class="text-xs font-semibold text-text-secondary uppercase tracking-wide">Permission</span>
        </div>
        <p class="text-3xl font-display font-bold text-text-primary mt-2">{{ $stats['total_permissions'] }}</p>
    </div>
    <div class="bg-[var(--card)] border border-[var(--border)] shadow-sm rounded-xl border border-border p-4 shadow-sm hover:border-success transition-colors">
        <div class="flex items-center gap-3 mb-1">
            <div class="w-9 h-9 bg-success/10 rounded-lg flex items-center justify-center text-success">
                <i data-lucide="users" class="w-4 h-4"></i>
            </div>
            <span class="text-xs font-semibold text-text-secondary uppercase tracking-wide">Total User</span>
        </div>
        <p class="text-3xl font-display font-bold text-text-primary mt-2">{{ $stats['total_users'] }}</p>
    </div>
    <div class="bg-[var(--card)] border border-[var(--border)] shadow-sm rounded-xl border border-border p-4 shadow-sm hover:border-warning transition-colors">
        <div class="flex items-center gap-3 mb-1">
            <div class="w-9 h-9 bg-warning/10 rounded-lg flex items-center justify-center text-warning">
                <i data-lucide="user-check" class="w-4 h-4"></i>
            </div>
            <span class="text-xs font-semibold text-text-secondary uppercase tracking-wide">User Custom Role</span>
        </div>
        <p class="text-3xl font-display font-bold text-text-primary mt-2">{{ $stats['users_with_custom'] }}</p>
    </div>
</div>

{{-- Tabs --}}
<div class="flex gap-2 mb-6 bg-secondary p-1 rounded-xl w-fit">
    <button onclick="switchTab('roles')" id="tab-roles" class="tab-btn active">
        <i data-lucide="shield" class="w-4 h-4 inline mr-1.5"></i> Daftar Role & Permission
    </button>
    <button onclick="switchTab('matrix')" id="tab-matrix" class="tab-btn">
        <i data-lucide="grid-3x3" class="w-4 h-4 inline mr-1.5"></i> Matrix Permission
    </button>
</div>

{{-- ============================== --}}
{{-- Tab 1: Daftar Role --}}
{{-- ============================== --}}
<div id="content-roles">
    <div class="bg-[var(--card)] border border-[var(--border)] shadow-sm rounded-xl shadow-sm border border-border overflow-hidden">
        <div class="p-4 border-b border-border bg-secondary/40 flex items-center justify-between">
            <h3 class="font-display font-bold text-text-primary flex items-center gap-2">
                <i data-lucide="shield" class="w-5 h-5 text-primary"></i> Semua Role
            </h3>
            <span class="text-xs text-text-secondary">{{ $roles->count() }} role terdaftar</span>
        </div>
        <div class="divide-y divide-border">
            @foreach($roles as $role)
            <div class="p-5 hover:bg-secondary/30 transition-colors">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                    {{-- Role Info --}}
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0
                            {{ $role->base_role === 'superadmin' ? 'bg-purple-100 text-purple-700' : 
                               ($role->base_role === 'admin' ? 'bg-violet-900/30 text-[#fcd34d]' : 
                               ($role->base_role === 'peserta' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700')) }}">
                            <i data-lucide="{{ $role->base_role === 'superadmin' ? 'crown' : ($role->base_role === 'admin' ? 'shield-half' : ($role->base_role === 'peserta' ? 'user' : 'star')) }}" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h4 class="font-bold text-text-primary">{{ $role->nama }}</h4>
                                @if($role->is_default)
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-[#13161c] text-gray-500 font-medium">Default</span>
                                @else
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 font-medium">Kustom</span>
                                @endif
                                @if($role->isProtected())
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 font-medium flex items-center gap-1">
                                        <i data-lucide="lock" class="w-3 h-3"></i> Protected
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm text-text-secondary mt-0.5">{{ $role->deskripsi ?? 'Tidak ada deskripsi.' }}</p>
                            <div class="flex items-center gap-4 mt-2 text-xs text-text-secondary">
                                <span class="flex items-center gap-1">
                                    <i data-lucide="key" class="w-3 h-3"></i>
                                    {{ $role->permissions_count }} permission
                                </span>
                                <span class="flex items-center gap-1">
                                    <i data-lucide="users" class="w-3 h-3"></i>
                                    {{ $role->users_count }} pengguna
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 shrink-0">
                        @if(!$role->isProtected())
                            <a href="{{ route('admin.kelola-akses.roles.edit', $role->id) }}"
                               class="flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg border border-border hover:border-primary hover:text-primary hover:bg-primary/5 transition-all">
                                <i data-lucide="pencil" class="w-3.5 h-3.5"></i> Edit
                            </a>
                            @if(!$role->is_default)
                            <form action="{{ route('admin.kelola-akses.roles.destroy', $role->id) }}" method="POST"
                                  onsubmit="return confirm('Hapus role {{ $role->nama }}? Semua user yang memiliki role ini akan kehilangan akses kustomnya.')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg border border-border text-text-secondary hover:border-danger hover:text-danger hover:bg-danger/5 transition-all">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                                </button>
                            </form>
                            @endif
                        @else
                            <span class="text-xs text-text-secondary italic flex items-center gap-1">
                                <i data-lucide="lock" class="w-3 h-3"></i> Dilindungi sistem
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Permission Tags --}}
                @if($role->permissions->isNotEmpty())
                <div class="mt-3 flex flex-wrap gap-1.5 pl-14">
                    @foreach($role->permissions->take(8) as $perm)
                        <span class="text-xs px-2 py-0.5 rounded-full bg-primary/8 text-primary/80 font-medium border border-primary/10">
                            {{ $perm->nama }}
                        </span>
                    @endforeach
                    @if($role->permissions->count() > 8)
                        <span class="text-xs px-2 py-0.5 rounded-full bg-secondary text-text-secondary font-medium">
                            +{{ $role->permissions->count() - 8 }} lainnya
                        </span>
                    @endif
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ============================== --}}
{{-- Tab 2: Matrix Permission --}}
{{-- ============================== --}}
<div id="content-matrix" class="hidden">
    <div class="bg-[var(--card)] border border-[var(--border)] shadow-sm rounded-xl shadow-sm border border-border overflow-hidden">
        <div class="p-4 border-b border-border bg-secondary/40">
            <h3 class="font-display font-bold text-text-primary flex items-center gap-2">
                <i data-lucide="grid-3x3" class="w-5 h-5 text-primary"></i> Matrix Permission per Role
            </h3>
            <p class="text-xs text-text-secondary mt-1">Gambaran visual permission yang dimiliki setiap role.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-secondary/50 border-b border-border">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide w-56">Fitur / Permission</th>
                        @foreach($roles as $role)
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide whitespace-nowrap
                            {{ $role->base_role === 'superadmin' ? 'text-purple-700' : ($role->base_role === 'admin' ? 'text-[#fcd34d]' : ($role->base_role === 'peserta' ? 'text-green-700' : 'text-amber-700')) }}">
                            {{ $role->nama }}
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/50">
                    @foreach($permissions as $grup => $perms)
                    <tr class="bg-secondary/20">
                        <td colspan="{{ $roles->count() + 1 }}" class="px-4 py-2 text-xs font-bold text-text-secondary uppercase tracking-wider">
                            {{ $grup }}
                        </td>
                    </tr>
                    @foreach($perms as $perm)
                    <tr class="hover:bg-secondary/30 transition-colors">
                        <td class="px-4 py-3">
                            <div>
                                <p class="font-medium text-text-primary text-xs">{{ $perm->nama }}</p>
                                <p class="text-text-secondary text-xs mt-0.5 opacity-70">{{ $perm->kode }}</p>
                            </div>
                        </td>
                        @foreach($roles as $role)
                        <td class="px-4 py-3 text-center">
                            @if($role->permissions->contains('id', $perm->id))
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-success/15 text-success">
                                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                </span>
                            @else
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-secondary text-text-secondary/30">
                                    <i data-lucide="minus" class="w-3.5 h-3.5"></i>
                                </span>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function switchTab(tab) {
        ['roles', 'matrix'].forEach(t => {
            document.getElementById('content-' + t).classList.add('hidden');
            document.getElementById('tab-' + t).classList.remove('active');
            document.getElementById('tab-' + t).classList.add('text-text-secondary');
        });
        document.getElementById('content-' + tab).classList.remove('hidden');
        document.getElementById('tab-' + tab).classList.add('active');
        document.getElementById('tab-' + tab).classList.remove('text-text-secondary');
        lucide.createIcons();
    }
</script>
@endpush

