@extends('layouts.app')

@section('title', 'Kelola Tim Pembinaan')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-text-primary">Kelola Tim Pembinaan</h1>
        <p class="text-xs text-text-secondary mt-1">Kelola akun 4 Tim Pembinaan (Mental, Fisik, Akhlak, Kompetensi). Username login = NIP koordinator.</p>
    </div>
    <a href="{{ route('admin.tim.penilaian') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-primary hover:bg-primary-hover text-white text-xs font-semibold shadow-sm transition-colors">
        <i data-lucide="bar-chart-2" class="w-4 h-4"></i> Rekap Penilaian Tim
    </a>
</div>

@if(session('success'))
<div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
    {{-- Daftar akun Tim --}}
    <div class="bg-[var(--card)] border border-[var(--border)] rounded-2xl overflow-hidden shadow-xs">
        <div class="px-5 py-4 border-b border-border bg-secondary/30">
            <h3 class="font-bold text-text-primary text-sm">Akun Tim Terdaftar</h3>
        </div>
        <div class="divide-y divide-border">
            @forelse($tims as $t)
            <div class="p-4 flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-bold text-text-primary">{{ \App\Models\PenilaianTim::teamLabel($t->tim) }}</p>
                    <p class="text-xs text-text-secondary">{{ $t->nama }} &bull; NIP: {{ $t->nip }}</p>
                    @if($t->anggota_tim)
                        <p class="text-[11px] text-text-secondary mt-1">Anggota: {{ $t->anggota_tim }}</p>
                    @endif
                </div>
                <form action="{{ route('admin.tim.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Hapus akun tim ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-rose-500 hover:text-rose-700 text-xs font-semibold">Hapus</button>
                </form>
            </div>
            @empty
            <div class="p-8 text-center text-xs text-text-muted">Belum ada akun Tim yang dibuat.</div>
            @endforelse
        </div>
    </div>

    {{-- Form buat/edit Tim --}}
    <div class="bg-[var(--card)] border border-[var(--border)] rounded-2xl p-6 shadow-xs">
        <h3 class="font-bold text-text-primary text-sm mb-4">Buat / Perbarui Akun Tim</h3>
        <form method="POST" action="{{ route('admin.tim.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-text-secondary mb-1.5">Jenis Tim <span class="text-danger">*</span></label>
                <select name="tim" required class="w-full px-3.5 py-2.5 rounded-xl border border-border bg-[var(--input)] text-sm text-text-primary focus:border-primary outline-none">
                    @foreach($teams as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-text-secondary mb-1.5">Nama Koordinator <span class="text-danger">*</span></label>
                <input type="text" name="nama" required placeholder="Nama lengkap koordinator" class="w-full px-3.5 py-2.5 rounded-xl border border-border bg-[var(--input)] text-sm text-text-primary focus:border-primary outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-text-secondary mb-1.5">NIP Koordinator (username) <span class="text-danger">*</span></label>
                <input type="text" name="nip" required placeholder="NIP digunakan untuk login" class="w-full px-3.5 py-2.5 rounded-xl border border-border bg-[var(--input)] text-sm text-text-primary focus:border-primary outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-text-secondary mb-1.5">Password</label>
                <input type="password" name="password" minlength="6" placeholder="Kosongkan jika tidak diganti" class="w-full px-3.5 py-2.5 rounded-xl border border-border bg-[var(--input)] text-sm text-text-primary focus:border-primary outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-text-secondary mb-1.5">Nama-nama Anggota Tim (penilai)</label>
                <textarea name="anggota_tim" rows="3" placeholder="Pisahkan dengan koma, misal: Andi, Budi, Citra" class="w-full px-3.5 py-2.5 rounded-xl border border-border bg-[var(--input)] text-sm text-text-primary focus:border-primary outline-none"></textarea>
            </div>
            <button type="submit" class="btn btn-primary text-white text-sm px-5 py-2.5 rounded-xl font-semibold w-full">Simpan Akun Tim</button>
        </form>
    </div>
</div>
@endsection
