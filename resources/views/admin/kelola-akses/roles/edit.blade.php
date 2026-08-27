@extends('layouts.app')

@section('title', 'Edit Role: ' . $role->nama)

@section('content')

{{-- Header --}}
<div class="mb-6">
    <div class="flex items-center gap-2 text-sm text-text-secondary mb-3">
        <a href="{{ route('admin.kelola-akses.index') }}" class="hover:text-primary transition-colors">Kelola Akses</a>
        <i data-lucide="chevron-right" class="w-4 h-4"></i>
        <span class="text-text-primary font-medium">Edit Role</span>
    </div>
    <h1 class="text-2xl font-display font-bold text-primary flex items-center gap-2">
        <i data-lucide="shield-check" class="w-7 h-7"></i> Edit Role: {{ $role->nama }}
    </h1>
    <p class="text-text-secondary mt-1">Perbarui nama, deskripsi, dan permission untuk role ini.</p>
</div>

@if($role->is_default)
<div class="bg-warning/10 border border-warning/30 rounded-xl p-4 mb-6 flex items-start gap-3">
    <i data-lucide="alert-triangle" class="w-5 h-5 text-warning shrink-0 mt-0.5"></i>
    <div>
        <p class="font-semibold text-warning text-sm">Role Default Sistem</p>
        <p class="text-sm text-text-secondary mt-0.5">Mengubah permission role default akan mempengaruhi seluruh pengguna dengan role dasar <strong>{{ strtoupper($role->base_role) }}</strong>.</p>
    </div>
</div>
@endif

<form action="{{ route('admin.kelola-akses.roles.update', $role->id) }}" method="POST">
    @csrf @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Role Info --}}
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-[var(--card)] border border-[var(--border)] shadow-sm rounded-xl shadow-sm border border-border p-5">
                <h3 class="font-bold text-text-primary mb-4 flex items-center gap-2">
                    <i data-lucide="info" class="w-4 h-4 text-primary"></i> Informasi Role
                </h3>

                <div class="space-y-4">
                    <div>
                        <label for="nama" class="block text-sm font-semibold text-text-primary mb-1.5">
                            Nama Role <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="nama" name="nama" value="{{ old('nama', $role->nama) }}" required
                               @if($role->is_default) readonly class="w-full px-3 py-2.5 border border-border rounded-lg text-sm bg-secondary text-text-secondary cursor-not-allowed outline-none" @else
                               class="w-full px-3 py-2.5 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary/30 focus:border-primary outline-none transition-all @error('nama') border-danger @enderror" @endif>
                        @if($role->is_default)
                            <p class="text-xs text-text-secondary mt-1 flex items-center gap-1"><i data-lucide="lock" class="w-3 h-3"></i> Nama role default tidak dapat diubah.</p>
                        @endif
                        @error('nama')
                            <p class="text-danger text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="deskripsi" class="block text-sm font-semibold text-text-primary mb-1.5">Deskripsi</label>
                        <textarea id="deskripsi" name="deskripsi" rows="4"
                                  placeholder="Jelaskan tujuan atau fungsi role ini..."
                                  class="w-full px-3 py-2.5 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary/30 focus:border-primary outline-none transition-all resize-none">{{ old('deskripsi', $role->deskripsi) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Stats --}}
            <div class="bg-primary/5 border border-primary/20 rounded-xl p-4 space-y-2">
                <p class="text-sm font-semibold text-primary flex items-center gap-2">
                    <i data-lucide="key" class="w-4 h-4"></i>
                    <span id="selected-count">{{ count($rolePermissionIds) }}</span> permission dipilih
                </p>
                <p class="text-xs text-text-secondary">Role ini digunakan oleh <strong>{{ $role->users_count }}</strong> pengguna.</p>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col gap-2">
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 bg-primary hover:bg-primary/90 text-[var(--text-primary)] text-sm font-bold px-5 py-2.5 rounded-lg transition-colors shadow-sm">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan Perubahan
                </button>
                <a href="{{ route('admin.kelola-akses.index') }}"
                   class="w-full flex items-center justify-center gap-2 border border-border hover:bg-secondary text-text-secondary text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
                </a>
            </div>
        </div>

        {{-- Right: Permission Grid --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Select All --}}
            <div class="bg-[var(--card)] border border-[var(--border)] shadow-sm rounded-xl shadow-sm border border-border p-4 flex items-center justify-between">
                <p class="text-sm font-semibold text-text-primary">Permission yang Diizinkan</p>
                <div class="flex gap-2">
                    <button type="button" onclick="selectAll()" class="text-xs font-semibold text-primary hover:underline px-3 py-1.5 rounded-lg hover:bg-primary/5 transition-colors">Pilih Semua</button>
                    <button type="button" onclick="deselectAll()" class="text-xs font-semibold text-danger hover:underline px-3 py-1.5 rounded-lg hover:bg-danger/5 transition-colors">Hapus Semua</button>
                </div>
            </div>

            @php $checkedIds = old('permissions', $rolePermissionIds); @endphp

            @foreach($permissions as $grup => $perms)
            <div class="bg-[var(--card)] border border-[var(--border)] shadow-sm rounded-xl shadow-sm border border-border overflow-hidden">
                {{-- Group Header --}}
                <div class="px-5 py-3 bg-secondary/50 border-b border-border flex items-center justify-between">
                    <h4 class="font-bold text-sm text-text-primary flex items-center gap-2">
                        <i data-lucide="{{ match($grup) {
                            'Konten' => 'book-open',
                            'Evaluasi' => 'clipboard-list',
                            'Pengguna' => 'users',
                            'Laporan' => 'bar-chart-2',
                            'Sistem' => 'settings',
                            'Pembelajaran' => 'graduation-cap',
                            default => 'layers'
                        } }}" class="w-4 h-4 text-primary"></i>
                        {{ $grup }}
                    </h4>
                    <button type="button" onclick="selectGroup('{{ $grup }}')" class="text-xs text-primary font-semibold hover:underline">Pilih grup</button>
                </div>

                {{-- Permissions --}}
                <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($perms as $perm)
                    <label for="perm-{{ $perm->id }}"
                           class="permission-card flex items-start gap-3 p-3 rounded-lg border cursor-pointer hover:border-primary/50 hover:bg-primary/3 transition-all group
                                  {{ in_array($perm->id, (array)$checkedIds) ? 'border-primary bg-primary/5' : 'border-border' }}"
                           data-grup="{{ $grup }}">
                        <input type="checkbox"
                               id="perm-{{ $perm->id }}"
                               name="permissions[]"
                               value="{{ $perm->id }}"
                               class="perm-checkbox mt-0.5 w-4 h-4 rounded border-border text-primary focus:ring-primary cursor-pointer"
                               @if(in_array($perm->id, (array)$checkedIds)) checked @endif>
                        <div>
                            <p class="text-sm font-semibold text-text-primary group-hover:text-primary transition-colors">{{ $perm->nama }}</p>
                            <p class="text-xs text-text-secondary mt-0.5">{{ $perm->deskripsi }}</p>
                            <span class="inline-block text-xs font-mono text-text-secondary/60 mt-1 bg-secondary px-1.5 py-0.5 rounded">{{ $perm->kode }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
    const checkboxes = document.querySelectorAll('.perm-checkbox');
    const countEl = document.getElementById('selected-count');

    function updateCount() {
        const checked = document.querySelectorAll('.perm-checkbox:checked').length;
        countEl.textContent = checked;
    }

    function updateCardStyle(checkbox) {
        const card = checkbox.closest('.permission-card');
        if (checkbox.checked) {
            card.classList.add('border-primary', 'bg-primary/5');
            card.classList.remove('border-border');
        } else {
            card.classList.remove('border-primary', 'bg-primary/5');
            card.classList.add('border-border');
        }
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            updateCardStyle(cb);
            updateCount();
        });
    });

    function selectAll() {
        checkboxes.forEach(cb => { cb.checked = true; updateCardStyle(cb); });
        updateCount();
    }

    function deselectAll() {
        checkboxes.forEach(cb => { cb.checked = false; updateCardStyle(cb); });
        updateCount();
    }

    function selectGroup(grup) {
        document.querySelectorAll(`.permission-card[data-grup="${grup}"] .perm-checkbox`).forEach(cb => {
            cb.checked = true; updateCardStyle(cb);
        });
        updateCount();
    }
</script>
@endpush

