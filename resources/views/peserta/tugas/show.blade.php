@extends('layouts.app')

@section('title', 'Penugasan: ' . $tugas->judul)

@section('content')
<div class="mb-6 flex items-center gap-2">
    <a href="{{ route('peserta.pelatihan.show', $pelatihan->id) }}" class="text-text-secondary hover:text-primary flex items-center gap-1 font-medium transition-colors text-sm">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Alur Pelatihan
    </a>
</div>

@if(session('success'))
<div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 flex items-center gap-3 text-sm">
    <i data-lucide="check-circle" class="w-5 h-5 text-green-600 shrink-0"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Kolom Kiri: Instruksi Tugas -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-xl overflow-hidden">
            <div class="p-6 border-b border-border">
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase {{ $tugas->tipe === 'upload_sertifikat' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-800' }}">
                        {{ $tugas->tipe_label }}
                    </span>
                    @if($tugas->deadline)
                        <span class="text-xs text-text-secondary">
                            Deadline: <strong>{{ $tugas->deadline->format('d M Y, H:i') }} WITA</strong>
                        </span>
                    @endif
                </div>
                <h1 class="text-2xl font-bold text-text-primary">{{ $tugas->judul }}</h1>
                <p class="text-xs text-text-secondary mt-1">Pelatihan: {{ $pelatihan->judul }} &bull; Skala Penilaian: 0 - {{ $tugas->bobot_nilai }}</p>
            </div>

            <!-- Petunjuk Pengerjaan -->
            <div class="p-6 space-y-4">
                <h3 class="font-bold text-text-primary text-sm">Petunjuk & Instruksi:</h3>
                @if($tugas->deskripsi)
                    <div class="prose max-w-none text-sm text-text-secondary whitespace-pre-line leading-relaxed">
                        {{ $tugas->deskripsi }}
                    </div>
                @else
                    <p class="text-xs text-text-secondary italic">Tidak ada instruksi khusus tertulis dari instruktur.</p>
                @endif

                <!-- Lembar Soal / Lampiran Instruktur -->
                @if($tugas->file_lampiran)
                    <div class="pt-4 mt-4 border-t border-border">
                        <span class="text-xs font-semibold text-text-primary block mb-2">Dokumen / Lembar Kasus Pendukung:</span>
                        <a href="{{ asset('storage/' . $tugas->file_lampiran) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-primary/20 bg-primary/5 hover:bg-primary/10 text-primary text-xs font-semibold transition-colors">
                            <i data-lucide="download" class="w-4 h-4"></i> Download Berkas Kasus
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Feedback Instruktur jika ada -->
        @if($submission && $submission->feedback_instruktur)
            <div class="bg-[var(--card)] border border-primary/20 rounded-xl p-6 bg-primary/5">
                <h3 class="font-bold text-text-primary text-sm flex items-center gap-2 mb-2">
                    <i data-lucide="message-square" class="w-4 h-4 text-primary"></i> Evaluasi & Catatan Instruktur
                </h3>
                <p class="text-sm text-text-secondary leading-relaxed">
                    {{ $submission->feedback_instruktur }}
                </p>
                <div class="mt-3 text-xs text-text-secondary flex items-center gap-2">
                    <span>Dinilai oleh: <strong>{{ $submission->penilai->nama ?? 'Instruktur' }}</strong></span>
                    @if($submission->dinilai_at)
                        &bull; <span>{{ $submission->dinilai_at->format('d M Y, H:i') }}</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Kolom Kanan: Status & Form Pengumpulan -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Status Pengumpulan -->
        <div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-xl p-5">
            <h3 class="font-bold text-text-primary text-sm mb-4 flex items-center gap-2">
                <i data-lucide="info" class="w-4 h-4 text-primary"></i> Status Pengumpulan
            </h3>

            <div class="space-y-3 text-xs">
                <div class="flex items-center justify-between pb-2 border-b border-border">
                    <span class="text-text-secondary">Status:</span>
                    @if($submission)
                        <span class="px-2.5 py-0.5 rounded-full font-bold {{ $submission->status_color }}">
                            {{ $submission->status_label }}
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full font-bold bg-secondary text-text-secondary">
                            Belum Mengumpulkan
                        </span>
                    @endif
                </div>

                <div class="flex items-center justify-between pb-2 border-b border-border">
                    <span class="text-text-secondary">Nilai:</span>
                    @if($submission && $submission->nilai !== null)
                        <span class="text-base font-extrabold text-primary">
                            {{ number_format($submission->nilai, 1) }} / {{ $tugas->bobot_nilai }}
                        </span>
                    @else
                        <span class="text-text-secondary font-medium">-</span>
                    @endif
                </div>

                @if($submission)
                    <div class="flex items-center justify-between pb-2 border-b border-border">
                        <span class="text-text-secondary">Waktu Kirim:</span>
                        <span class="font-medium text-text-primary">{{ $submission->created_at->format('d M Y, H:i') }}</span>
                    </div>

                    @if($submission->file_path)
                        <div class="pt-1">
                            <span class="text-text-secondary block mb-1">File Terkirim:</span>
                            <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank" class="inline-flex items-center gap-1.5 text-primary hover:underline font-medium break-all">
                                <i data-lucide="file-check" class="w-3.5 h-3.5 shrink-0"></i>
                                {{ $submission->file_nama_asli ?? 'Unduh Berkas' }}
                            </a>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <!-- Form Upload Tugas / Sertifikat -->
        <div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-xl p-5">
            <h3 class="font-bold text-text-primary text-sm mb-3 flex items-center gap-2">
                <i data-lucide="upload" class="w-4 h-4 text-primary"></i>
                {{ $submission ? 'Kirim Pembaruan / Revisi' : 'Form Pengumpulan' }}
            </h3>

            <form action="{{ route('peserta.tugas.submit', $tugas->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="space-y-4">
                    <!-- Khusus Upload Sertifikat -->
                    @if($tugas->tipe === 'upload_sertifikat')
                        <div>
                            <label for="nomor_sertifikat" class="block text-xs font-semibold text-text-secondary mb-1">Nomor Sertifikat</label>
                            <input type="text" name="nomor_sertifikat" id="nomor_sertifikat" value="{{ old('nomor_sertifikat', $submission->nomor_sertifikat ?? '') }}" placeholder="Contoh: 123/SERTIF/LAN/2026" class="w-full px-3 py-2 text-xs rounded-lg border border-border bg-[var(--card)] text-text-primary focus:border-primary outline-none">
                        </div>

                        <div>
                            <label for="penyelenggara" class="block text-xs font-semibold text-text-secondary mb-1">Lembaga Penyelenggara</label>
                            <input type="text" name="penyelenggara" id="penyelenggara" value="{{ old('penyelenggara', $submission->penyelenggara ?? '') }}" placeholder="Contoh: LAN RI / BKN / Ditjenpas" class="w-full px-3 py-2 text-xs rounded-lg border border-border bg-[var(--card)] text-text-primary focus:border-primary outline-none">
                        </div>

                        <div>
                            <label for="tanggal_sertifikat" class="block text-xs font-semibold text-text-secondary mb-1">Tanggal Sertifikat</label>
                            <input type="date" name="tanggal_sertifikat" id="tanggal_sertifikat" value="{{ old('tanggal_sertifikat', $submission && $submission->tanggal_sertifikat ? $submission->tanggal_sertifikat->format('Y-m-d') : '') }}" class="w-full px-3 py-2 text-xs rounded-lg border border-border bg-[var(--card)] text-text-primary focus:border-primary outline-none">
                        </div>
                    @endif

                    <div>
                        <label for="file_tugas" class="block text-xs font-semibold text-text-secondary mb-1">
                            {{ $tugas->tipe === 'upload_sertifikat' ? 'Upload Berkas Sertifikat (PDF/Gambar)' : 'Upload Berkas Tugas / Laporan' }}
                            @if(!$submission) <span class="text-danger">*</span> @endif
                        </label>
                        <input type="file" name="file_tugas" id="file_tugas" class="w-full px-2 py-1.5 text-xs rounded-lg border border-border file:mr-2 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-[11px] file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20" {{ !$submission ? 'required' : '' }}>
                        <p class="text-[11px] text-text-secondary mt-1">Maksimal: {{ $tugas->max_file_size_mb ?? 10 }} MB</p>
                        @error('file_tugas') <p class="text-danger text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="catatan_peserta" class="block text-xs font-semibold text-text-secondary mb-1">Catatan Tambahan (Opsional)</label>
                        <textarea name="catatan_peserta" id="catatan_peserta" rows="3" placeholder="Tuliskan keterangan pengantar bila ada..." class="w-full px-3 py-2 text-xs rounded-lg border border-border bg-[var(--card)] text-text-primary focus:border-primary outline-none">{{ old('catatan_peserta', $submission->catatan_peserta ?? '') }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 rounded-lg bg-primary hover:bg-primary-hover text-white text-xs font-bold shadow-sm flex items-center justify-center gap-2 transition-colors">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        {{ $submission ? 'Simpan Pembaruan Berkas' : 'Kumpulkan Tugas' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
