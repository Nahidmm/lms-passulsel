@extends('layouts.app')

@section('title', 'Konfigurasi Sertifikat')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-sans font-bold text-text-primary">Konfigurasi Sertifikat</h1>
    <p class="text-text-secondary mt-1">Atur parameter dan desain sertifikat yang otomatis terbit untuk peserta.</p>
</div>



@if($errors->any())
    <div class="bg-danger/10 border border-danger/20 text-danger px-4 py-3 rounded-lg mb-6 text-sm flex gap-2 items-start">
        <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
        <ul class="list-disc pl-4">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.sertifikat-setting.update') }}" method="POST" enctype="multipart/form-data" class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-2xl overflow-hidden">
    @csrf
    @method('PUT')
    
    <div class="p-6 md:p-8 space-y-6">
        {{-- Pengaturan Status & Kebijakan Penerbitan --}}
        <div class="p-5 rounded-2xl bg-[var(--muted)]/50 border border-[var(--border)] space-y-4">
            <h2 class="text-sm font-bold text-text-primary uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="shield-check" class="w-4 h-4 text-primary"></i> Kebijakan & Kontrol Penerbitan Sertifikat
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-1">
                {{-- Toggle Aktif / Nonaktif --}}
                <div class="flex items-start gap-3.5 p-4 rounded-xl bg-[var(--card)] border border-[var(--border)]">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $setting->is_active ?? true) ? 'checked' : '' }}
                        class="w-5 h-5 mt-0.5 rounded text-primary border-border focus:ring-primary cursor-pointer">
                    <div class="space-y-0.5">
                        <label for="is_active" class="text-sm font-bold text-text-primary cursor-pointer">
                            Aktifkan Penerbitan Sertifikat
                        </label>
                        <p class="text-xs text-text-secondary">
                            Jika dinonaktifkan, seluruh penerbitan sertifikat ditutup sementara dan peserta tidak dapat mengunduh sertifikat.
                        </p>
                    </div>
                </div>

                {{-- Mode Penerbitan (Auto vs Admin Only) --}}
                <div class="space-y-2 p-4 rounded-xl bg-[var(--card)] border border-[var(--border)]">
                    <label for="generation_mode" class="block text-xs font-bold text-text-primary uppercase tracking-wider">
                        Metode Penerbitan Sertifikat
                    </label>
                    <select id="generation_mode" name="generation_mode" class="w-full px-3 py-2 border border-border rounded-xl focus:ring-2 focus:ring-primary outline-none text-xs bg-secondary/50 font-medium">
                        <option value="admin_only" {{ old('generation_mode', $setting->generation_mode ?? 'admin_only') === 'admin_only' ? 'selected' : '' }}>
                            🛡️ Terbitkan oleh Admin Saja (Manual - Direkomendasikan)
                        </option>
                        <option value="auto" {{ old('generation_mode', $setting->generation_mode ?? 'admin_only') === 'auto' ? 'selected' : '' }}>
                            ⚡ Otomatis (Saat peserta menyelesaikan seluruh materi)
                        </option>
                    </select>
                    <p class="text-[11px] text-text-secondary">
                        Pada mode Admin, admin/penguji berhak memverifikasi nilai sebelum menekan tombol "Terbitkan Sertifikat" di Buku Nilai / Transkrip.
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="nama_penandatangan" class="block text-sm font-semibold text-text-primary mb-1.5">Nama Penandatangan</label>
                <input type="text" id="nama_penandatangan" name="nama_penandatangan" value="{{ old('nama_penandatangan', $setting->nama_penandatangan ?? '') }}" required placeholder="Contoh: Dr. H. Fulan, S.H., M.H."
                    class="w-full px-4 py-2.5 border border-border rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm transition-all bg-secondary/50">
            </div>
            <div>
                <label for="jabatan_penandatangan" class="block text-sm font-semibold text-text-primary mb-1.5">Jabatan Penandatangan</label>
                <input type="text" id="jabatan_penandatangan" name="jabatan_penandatangan" value="{{ old('jabatan_penandatangan', $setting->jabatan_penandatangan ?? '') }}" required placeholder="Contoh: Kepala Pusat Pelatihan"
                    class="w-full px-4 py-2.5 border border-border rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm transition-all bg-secondary/50">
            </div>
            <div>
                <label for="tempat_tanda_tangan" class="block text-sm font-semibold text-text-primary mb-1.5">Tempat Penandatanganan</label>
                <input type="text" id="tempat_tanda_tangan" name="tempat_tanda_tangan" value="{{ old('tempat_tanda_tangan', $setting->tempat_tanda_tangan ?? 'Makassar') }}" required placeholder="Contoh: Makassar"
                    class="w-full px-4 py-2.5 border border-border rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm transition-all bg-secondary/50">
            </div>
            
            <div>
                <label for="tipe_ttd" class="block text-sm font-semibold text-text-primary mb-1.5">Tipe Tanda Tangan</label>
                <select id="tipe_ttd" name="tipe_ttd" class="w-full px-3 py-2.5 border border-border rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm bg-secondary/50">
                    <option value="qr" {{ old('tipe_ttd', $setting->tipe_ttd ?? 'qr') === 'qr' ? 'selected' : '' }}>Hanya QR Code (Digital Signature)</option>
                    <option value="image" {{ old('tipe_ttd', $setting->tipe_ttd ?? 'qr') === 'image' ? 'selected' : '' }}>Hanya Scan Tanda Tangan (Image)</option>
                    <option value="both" {{ old('tipe_ttd', $setting->tipe_ttd ?? 'qr') === 'both' ? 'selected' : '' }}>Tampilkan Keduanya (QR & Image)</option>
                </select>
            </div>
        </div>

        <hr class="border-border">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-text-primary mb-1.5">Logo Instansi (Kop Atas)</label>
                @if($setting->logo_instansi)
                    <div class="mb-3">
                        <img src="{{ Storage::url($setting->logo_instansi) }}" alt="Logo" class="h-16 object-contain bg-secondary/30 p-2 rounded-lg border border-border">
                    </div>
                @endif
                <input type="file" name="logo_instansi" accept="image/*" class="w-full px-3 py-2 border border-border rounded-xl text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition-all">
                <p class="text-xs text-text-secondary mt-2">Maks: 2MB. Format: PNG (dengan transparan lebih baik) atau JPG.</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-text-primary mb-1.5">Gambar Tanda Tangan (Opsional)</label>
                @if($setting->ttd_image)
                    <div class="mb-3">
                        <img src="{{ Storage::url($setting->ttd_image) }}" alt="TTD" class="h-16 object-contain bg-secondary/30 p-2 rounded-lg border border-border">
                    </div>
                @endif
                <input type="file" name="ttd_image" accept="image/*" class="w-full px-3 py-2 border border-border rounded-xl text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition-all">
                <p class="text-xs text-text-secondary mt-2">Upload jika Anda menggunakan opsi "Image" atau "Both". Gunakan PNG transparan.</p>
            </div>
        </div>
    </div>
    
    <div class="px-6 md:px-8 py-5 bg-[var(--muted)]/40 border-t border-[var(--border)] flex justify-end gap-3">
        <a href="{{ route('admin.sertifikat-setting.preview') }}" target="_blank" class="btn bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2.5 px-5 rounded-xl shadow-xs transition-all flex items-center gap-2">
            <i data-lucide="eye" class="w-4 h-4"></i> Preview Sertifikat
        </a>
        <button type="submit" class="btn btn-primary text-white font-medium py-2.5 px-6 rounded-xl shadow-xs transition-all flex items-center gap-2">
            <i data-lucide="save" class="w-4 h-4"></i> Simpan Pengaturan
        </button>
    </div>
</form>
@endsection


