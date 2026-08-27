@extends('layouts.app')

@section('title', 'Profil Pengguna')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-display font-bold text-primary">Profil Saya</h1>
    <p class="text-text-secondary mt-1">Kelola informasi pribadi dan keamanan akun Anda.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Profile Info Card -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-[var(--card)] border border-[var(--border)] shadow-sm rounded-xl shadow-sm border border-border p-6 text-center">
            <div class="relative w-32 h-32 mx-auto mb-4 group">
                <img src="{{ $user->avatar_url }}" alt="Avatar" class="w-full h-full object-cover rounded-full border-4 border-secondary shadow-sm">
                <!-- Avatar upload trigger could go here -->
            </div>
            <h2 class="text-xl font-bold text-text-primary">{{ $user->nama }}</h2>
            <p class="text-text-secondary">{{ $user->nip }}</p>
            
            <div class="mt-4 pt-4 border-t border-border">
                <span class="inline-block bg-primary/10 text-primary text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-2">
                    {{ $user->role }}
                </span>
                @if($user->jabatan)
                    <p class="text-sm font-medium text-text-primary mt-1">{{ $user->jabatan->nama_jabatan }}</p>
                    <p class="text-xs text-text-secondary">{{ $user->golongan ?? 'Eselon V' }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Edit Forms -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Update Profile Form -->
        <div class="bg-[var(--card)] border border-[var(--border)] shadow-sm rounded-xl shadow-sm border border-border p-6 md:p-8">
            <h3 class="text-lg font-display font-bold text-text-primary mb-4 flex items-center gap-2">
                <i data-lucide="user-cog" class="w-5 h-5 text-primary"></i> Informasi Dasar
            </h3>

            <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                
                <div>
                    <label for="nama" class="block text-sm font-medium text-text-primary mb-1">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" value="{{ old('nama', $user->nama) }}" required
                        class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none">
                    @error('nama') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-text-primary mb-1">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                        class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none">
                    @error('email') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="avatar" class="block text-sm font-medium text-text-primary mb-1">Foto Profil (Opsional)</label>
                    <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/jpg"
                        class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none text-sm">
                    <p class="text-xs text-text-secondary mt-1">Format: JPG, PNG. Maks 2MB.</p>
                    @error('avatar') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="bg-primary hover:bg-primary-hover text-[var(--text-primary)] font-bold py-2 px-6 rounded-lg transition-colors shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- Update Password Form -->
        <div class="bg-[var(--card)] border border-[var(--border)] shadow-sm rounded-xl shadow-sm border border-border p-6 md:p-8">
            <h3 class="text-lg font-display font-bold text-text-primary mb-4 flex items-center gap-2">
                <i data-lucide="shield-check" class="w-5 h-5 text-warning"></i> Keamanan (Ganti Password)
            </h3>

            <form action="{{ route('profil.password') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label for="current_password" class="block text-sm font-medium text-text-primary mb-1">Password Saat Ini</label>
                    <input type="password" id="current_password" name="current_password" required
                        class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none">
                    @error('current_password') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-text-primary mb-1">Password Baru</label>
                        <input type="password" id="password" name="password" required minlength="8"
                            class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none">
                        @error('password') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-text-primary mb-1">Ulangi Password Baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                            class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none">
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="bg-warning hover:bg-warning/90 text-[var(--text-primary)] font-bold py-2 px-6 rounded-lg transition-colors shadow-sm">
                        Ubah Password
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection

