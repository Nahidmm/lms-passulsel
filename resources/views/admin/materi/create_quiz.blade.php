@extends('layouts.app')

@section('title', 'Buat Kuis Baru — ' . $pelatihan->judul)

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.pelatihan.show', $pelatihan->id) }}"
       class="text-text-secondary hover:text-primary flex items-center gap-1.5 font-medium transition-colors w-fit">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Kurikulum
    </a>
</div>

<div class="max-w-2xl mx-auto space-y-5">

    {{-- HEADER --}}
    <div class="bg-white rounded-2xl shadow-sm border border-border overflow-hidden">
        <div class="px-6 py-5 flex items-start gap-4" style="background:linear-gradient(to right,#FEF9EC,transparent)">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-accent shrink-0" style="background:#FEF9EC;border:1px solid #F0E4B0">
                <i data-lucide="help-circle" class="w-6 h-6"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-text-primary">Buat Kuis Evaluasi</h2>
                <p class="text-sm text-text-secondary mt-1">
                    Menambahkan kuis ke pelatihan
                    <span class="font-semibold text-text-primary">"{{ $pelatihan->judul }}"</span>
                </p>
            </div>
        </div>
        {{-- Step Indicator --}}
        <div class="px-6 py-3 bg-secondary border-t border-border flex items-center gap-2 text-xs overflow-x-auto whitespace-nowrap">
            <div class="flex items-center gap-2 font-bold text-accent">
                <span class="w-5 h-5 rounded-full bg-accent text-white flex items-center justify-center text-xs font-bold shrink-0">1</span>
                Konfigurasi
            </div>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-border shrink-0"></i>
            <div class="flex items-center gap-2 text-text-secondary opacity-50">
                <span class="w-5 h-5 rounded-full bg-border text-white flex items-center justify-center text-xs font-bold shrink-0">2</span>
                Tambah Soal
            </div>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-border shrink-0"></i>
            <div class="flex items-center gap-2 text-text-secondary opacity-50">
                <span class="w-5 h-5 rounded-full bg-border text-white flex items-center justify-center text-xs font-bold shrink-0">3</span>
                Publish
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-danger px-4 py-3 rounded-xl text-sm flex gap-3 items-start">
            <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
            <ul class="list-disc pl-4 space-y-0.5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('admin.pelatihan.materi.store', $pelatihan->id) }}" method="POST">
        @csrf
        <input type="hidden" name="jenis" value="quiz">
        <input type="hidden" name="durasi_baca" value="0">

        <div class="space-y-5">

            {{-- CARD 1: Informasi Dasar --}}
            <div class="bg-white rounded-2xl shadow-sm border border-border">
                <div class="px-5 py-4 border-b border-border flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background:#EFF6FF">
                        <i data-lucide="file-text" class="w-4 h-4 text-primary"></i>
                    </div>
                    <div>
                        <p class="font-bold text-text-primary text-sm">Informasi Dasar</p>
                        <p class="text-xs text-text-secondary">Nama dan instruksi kuis untuk peserta</p>
                    </div>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <label for="judul" class="block text-sm font-semibold text-text-primary mb-1.5">
                            Judul Kuis <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="judul" name="judul" value="{{ old('judul') }}" required
                            placeholder="Contoh: Evaluasi Akhir Modul 1"
                            class="w-full px-4 py-2.5 border border-border rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none text-sm transition-all">
                    </div>
                    <div>
                        <label for="deskripsi" class="block text-sm font-semibold text-text-primary mb-1.5">
                            Instruksi untuk Peserta
                            <span class="text-xs font-normal text-text-secondary">(opsional — tampil sebelum kuis dimulai)</span>
                        </label>
                        <textarea id="deskripsi" name="deskripsi" rows="3"
                            placeholder="Contoh: Kerjakan soal-soal berikut dengan jujur dan mandiri..."
                            class="w-full px-4 py-3 border border-border rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none resize-none text-sm transition-all">{{ old('deskripsi') }}</textarea>
                    </div>
                    <div>
                        <label for="urutan" class="block text-sm font-semibold text-text-primary mb-1.5">
                            Urutan di Kurikulum <span class="text-danger">*</span>
                        </label>
                        <div class="flex items-center gap-3">
                            <input type="number" id="urutan" name="urutan"
                                value="{{ old('urutan', $pelatihan->materis->count() + 1) }}"
                                required min="1"
                                class="w-20 px-3 py-2.5 border border-border rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none text-center font-bold text-sm transition-all">
                            <p class="text-xs text-text-secondary">
                                Saat ini ada <strong>{{ $pelatihan->materis->count() }}</strong> item.
                                Default: urutan ke-{{ $pelatihan->materis->count() + 1 }}.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD 2: Penilaian & Waktu --}}
            <div class="bg-white rounded-2xl shadow-sm border border-border">
                <div class="px-5 py-4 border-b border-border flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background:#FEF9EC">
                        <i data-lucide="sliders-horizontal" class="w-4 h-4 text-accent"></i>
                    </div>
                    <div>
                        <p class="font-bold text-text-primary text-sm">Penilaian & Waktu</p>
                        <p class="text-xs text-text-secondary">Ambang lulus, durasi, dan batas percobaan</p>
                    </div>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="rounded-xl p-4 text-center" style="background:#FEF9EC;border:1px solid #F0E4B0">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center mx-auto mb-3" style="background:#C5A02E22">
                                <i data-lucide="target" class="w-4 h-4 text-accent"></i>
                            </div>
                            <label for="passing_grade" class="block text-xs font-bold text-accent uppercase tracking-widest mb-2">Nilai Lulus</label>
                            <div class="relative">
                                <input type="number" id="passing_grade" name="passing_grade"
                                    value="{{ old('passing_grade', 70) }}" min="0" max="100" required
                                    class="w-full px-2 py-2 border border-border rounded-lg outline-none bg-white text-xl font-bold text-center text-accent focus:ring-2 focus:ring-accent">
                                <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-accent font-bold text-sm">%</span>
                            </div>
                            <p class="text-xs text-text-secondary mt-2">Rentang 0 – 100</p>
                        </div>

                        <div class="rounded-xl p-4 text-center bg-secondary border border-border">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center mx-auto mb-3" style="background:#EFF6FF">
                                <i data-lucide="clock" class="w-4 h-4 text-primary"></i>
                            </div>
                            <label for="durasi_menit" class="block text-xs font-bold text-text-secondary uppercase tracking-widest mb-2">Durasi</label>
                            <input type="number" id="durasi_menit" name="durasi_menit"
                                value="{{ old('durasi_menit', 30) }}" min="0" required
                                class="w-full px-2 py-2 border border-border rounded-lg outline-none bg-white text-xl font-bold text-center focus:ring-2 focus:ring-primary">
                            <p class="text-xs text-text-secondary mt-2">Menit &bull; 0 = ∞</p>
                        </div>

                        <div class="rounded-xl p-4 text-center bg-secondary border border-border">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center mx-auto mb-3" style="background:#FFF7ED">
                                <i data-lucide="refresh-ccw" class="w-4 h-4" style="color:#F97316"></i>
                            </div>
                            <label for="max_attempts" class="block text-xs font-bold text-text-secondary uppercase tracking-widest mb-2">Maks. Coba</label>
                            <input type="number" id="max_attempts" name="max_attempts"
                                value="{{ old('max_attempts', 3) }}" min="0" required
                                class="w-full px-2 py-2 border border-border rounded-lg outline-none bg-white text-xl font-bold text-center">
                            <p class="text-xs text-text-secondary mt-2">0 = ∞</p>
                        </div>

                        <div class="rounded-xl p-4 text-center bg-secondary border border-border">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center mx-auto mb-3" style="background:#F3E8FF">
                                <i data-lucide="award" class="w-4 h-4 text-purple-600"></i>
                            </div>
                            <label for="poin" class="block text-xs font-bold text-text-secondary uppercase tracking-widest mb-2">Poin</label>
                            <input type="number" id="poin" name="poin"
                                value="{{ old('poin', 50) }}" min="0" required
                                class="w-full px-2 py-2 border border-border rounded-lg outline-none bg-white text-xl font-bold text-center focus:ring-2 focus:ring-purple-500">
                            <p class="text-xs text-text-secondary mt-2">Reward Poin</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD 3: Perilaku Soal --}}
            <div class="bg-white rounded-2xl shadow-sm border border-border">
                <div class="px-5 py-4 border-b border-border flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background:#F5F3FF">
                        <i data-lucide="shuffle" class="w-4 h-4" style="color:#7C3AED"></i>
                    </div>
                    <div>
                        <p class="font-bold text-text-primary text-sm">Perilaku Soal</p>
                        <p class="text-xs text-text-secondary">Tampilan soal saat peserta mengerjakan</p>
                    </div>
                </div>
                <div class="divide-y divide-border">
                    <label class="flex items-center justify-between px-5 py-4 cursor-pointer hover:bg-secondary transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#EFF6FF">
                                <i data-lucide="shuffle" class="w-4 h-4 text-primary"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-text-primary">Acak Urutan Soal</p>
                                <p class="text-xs text-text-secondary mt-0.5">Soal tampil acak setiap pengerjaan</p>
                            </div>
                        </div>
                        <input type="checkbox" name="acak_soal" value="1"
                            {{ old('acak_soal', true) ? 'checked' : '' }}
                            class="w-5 h-5 rounded text-accent border-border focus:ring-accent cursor-pointer shrink-0">
                    </label>
                    <label class="flex items-center justify-between px-5 py-4 cursor-pointer hover:bg-secondary transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#F5F3FF">
                                <i data-lucide="list-ordered" class="w-4 h-4" style="color:#7C3AED"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-text-primary">Acak Pilihan Jawaban</p>
                                <p class="text-xs text-text-secondary mt-0.5">A, B, C, D diacak (Multiple Choice)</p>
                            </div>
                        </div>
                        <input type="checkbox" name="acak_jawaban" value="1"
                            {{ old('acak_jawaban', true) ? 'checked' : '' }}
                            class="w-5 h-5 rounded text-accent border-border focus:ring-accent cursor-pointer shrink-0">
                    </label>
                    <label class="flex items-center justify-between px-5 py-4 cursor-pointer hover:bg-secondary transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#ECFDF5">
                                <i data-lucide="eye" class="w-4 h-4 text-success"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-text-primary">Tampilkan Feedback & Kunci</p>
                                <p class="text-xs text-text-secondary mt-0.5">Peserta bisa lihat jawaban benar setelah selesai</p>
                            </div>
                        </div>
                        <input type="checkbox" name="tampilkan_feedback" value="1"
                            {{ old('tampilkan_feedback', true) ? 'checked' : '' }}
                            class="w-5 h-5 rounded text-accent border-border focus:ring-accent cursor-pointer shrink-0">
                    </label>
                </div>
            </div>

            {{-- CARD 4: Anti-Cheat --}}
            <div class="bg-white rounded-2xl shadow-sm border border-border overflow-hidden">
                <div class="px-5 py-4 border-b border-border flex items-center gap-3" style="background:#FEF2F2">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background:#FEE2E2">
                        <i data-lucide="shield-alert" class="w-4 h-4 text-danger"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-text-primary text-sm">Anti-Cheat Proctoring</p>
                        <p class="text-xs text-text-secondary">Pengawasan integritas otomatis berbasis browser</p>
                    </div>
                </div>
                <div class="p-5 space-y-3">
                    <label class="flex items-center justify-between gap-3 cursor-pointer">
                        <div>
                            <p class="text-sm font-semibold text-text-primary">Aktifkan Proctoring</p>
                            <p class="text-xs text-text-secondary mt-0.5">Pantau kejujuran peserta secara otomatis</p>
                        </div>
                        <input type="checkbox" name="strict_anti_cheat" value="1"
                            {{ old('strict_anti_cheat', false) ? 'checked' : '' }}
                            class="w-5 h-5 rounded border-border cursor-pointer shrink-0" style="accent-color:#C0392B">
                    </label>
                    <div class="rounded-xl p-3 space-y-1.5" style="background:#FEF2F2">
                        <p class="text-xs font-bold text-danger mb-1.5">Yang dideteksi:</p>
                        <p class="text-xs text-text-secondary flex items-center gap-1.5">
                            <i data-lucide="eye-off" class="w-3 h-3 text-danger shrink-0"></i>
                            Perpindahan tab / minimize browser
                        </p>
                        <p class="text-xs text-text-secondary flex items-center gap-1.5">
                            <i data-lucide="monitor-off" class="w-3 h-3 text-danger shrink-0"></i>
                            Alt+Tab / kehilangan fokus browser
                        </p>
                        <p class="text-xs text-text-secondary flex items-center gap-1.5">
                            <i data-lucide="triangle-alert" class="w-3 h-3 shrink-0" style="color:#F97316"></i>
                            Auto-submit setelah 3× pelanggaran
                        </p>
                    </div>
                    <button type="button" onclick="openAntiCheatModal()"
                        class="w-full text-sm font-semibold text-danger border border-danger rounded-xl py-2.5 hover:bg-red-50 transition-colors flex items-center justify-center gap-2">
                        <i data-lucide="info" class="w-4 h-4"></i> Lihat Detail Sistem Proctoring
                    </button>
                </div>
            </div>

            {{-- CARD 5: Publish --}}
            <div class="bg-white rounded-2xl shadow-sm border border-border p-5">
                <label class="flex items-center justify-between gap-3 cursor-pointer">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background:#ECFDF5">
                            <i data-lucide="globe" class="w-4 h-4 text-success"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-text-primary">Langsung Publish</p>
                            <p class="text-xs text-text-secondary mt-0.5">Kuis langsung dapat diakses peserta setelah disimpan</p>
                        </div>
                    </div>
                    <input type="checkbox" name="is_active" value="1"
                        {{ old('is_active', true) ? 'checked' : '' }}
                        class="w-5 h-5 rounded text-success border-border focus:ring-success cursor-pointer shrink-0">
                </label>
            </div>

            {{-- Info Box --}}
            <div class="rounded-xl p-4 flex items-start gap-3" style="background:#FEF9EC;border:1px solid #F0E4B0">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 mt-0.5" style="background:#C5A02E22">
                    <i data-lucide="lightbulb" class="w-4 h-4 text-accent"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-accent mb-1">Setelah disimpan</p>
                    <p class="text-sm text-text-secondary leading-relaxed">
                        Anda akan diarahkan ke halaman konfigurasi untuk
                        <strong class="text-text-primary">menambahkan soal-soal kuis</strong>
                        (Pilihan Ganda, Essay, Isian Singkat, atau Menjodohkan).
                    </p>
                </div>
            </div>

            {{-- Tombol --}}
            <div class="flex items-center justify-between gap-3">
                <a href="{{ route('admin.pelatihan.show', $pelatihan->id) }}"
                   class="px-5 py-2.5 border border-border rounded-xl text-text-secondary hover:bg-secondary font-medium text-sm transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="bg-accent hover:bg-accent-hover text-white font-bold py-2.5 px-8 rounded-xl shadow-sm flex items-center gap-2 text-sm transition-all">
                    Lanjut Buat Soal <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </div>

        </div>
    </form>
</div>

{{-- MODAL: Anti-Cheat Detail --}}
<div id="anti-cheat-modal" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;padding:1rem;">
    <div onclick="closeAntiCheatModal()" style="position:absolute;inset:0;background:rgba(0,0,0,0.55);cursor:pointer;"></div>
    <div style="position:relative;background:#fff;border-radius:1rem;box-shadow:0 20px 60px rgba(0,0,0,0.2);width:100%;max-width:520px;max-height:90vh;overflow-y:auto;z-index:1;">
        <div style="position:sticky;top:0;background:#fff;border-bottom:1px solid #E2E6EC;padding:1rem 1.5rem;display:flex;align-items:center;gap:0.75rem;border-radius:1rem 1rem 0 0;">
            <div style="width:2.5rem;height:2.5rem;background:#FEF2F2;border-radius:0.75rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i data-lucide="shield-alert" style="width:1.25rem;height:1.25rem;color:#C0392B;"></i>
            </div>
            <div style="flex:1">
                <p style="font-weight:700;color:#1A1A1A;font-size:0.9375rem;">Sistem Anti-Cheat Proctoring</p>
                <p style="font-size:0.75rem;color:#5C6470;margin-top:0.125rem;">Cara kerja pengawasan integritas kuis</p>
            </div>
            <button onclick="closeAntiCheatModal()"
                style="width:2rem;height:2rem;display:flex;align-items:center;justify-content:center;border-radius:0.5rem;border:1px solid #E2E6EC;background:#fff;cursor:pointer;color:#5C6470;">
                <i data-lucide="x" style="width:1rem;height:1rem;"></i>
            </button>
        </div>
        <div style="padding:1.5rem;display:flex;flex-direction:column;gap:1.25rem;">
            <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:0.75rem;padding:1rem;">
                <p style="font-size:0.875rem;color:#1A1A1A;line-height:1.6;">
                    Sistem proctoring bekerja <strong>secara otomatis di browser peserta</strong> tanpa software tambahan.
                    Saat aktif, sistem <strong style="color:#C0392B;">memantau perilaku peserta</strong> sepanjang pengerjaan.
                </p>
            </div>
            <div>
                <p style="font-size:0.6875rem;font-weight:700;color:#5C6470;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:0.75rem;">Yang Dideteksi Sistem</p>
                <div style="display:flex;flex-direction:column;gap:0.5rem;">
                    <div style="display:flex;align-items:flex-start;gap:0.75rem;background:#FEF2F2;border:1px solid #FECACA;border-radius:0.75rem;padding:0.875rem;">
                        <div style="width:2rem;height:2rem;background:#FEE2E2;border-radius:0.5rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:0.125rem;">
                            <i data-lucide="layers" style="width:1rem;height:1rem;color:#C0392B;"></i>
                        </div>
                        <div>
                            <p style="font-size:0.875rem;font-weight:700;color:#C0392B;">Perpindahan Tab / Jendela</p>
                            <p style="font-size:0.75rem;color:#5C6470;margin-top:0.25rem;line-height:1.5;">Terdeteksi saat peserta berpindah tab (Google, ChatGPT, dll). Menggunakan <code style="background:#FEE2E2;padding:0 4px;border-radius:3px;font-size:0.7rem;color:#C0392B;">visibilitychange</code> API.</p>
                        </div>
                    </div>
                    <div style="display:flex;align-items:flex-start;gap:0.75rem;background:#FEF2F2;border:1px solid #FECACA;border-radius:0.75rem;padding:0.875rem;">
                        <div style="width:2rem;height:2rem;background:#FEE2E2;border-radius:0.5rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:0.125rem;">
                            <i data-lucide="monitor-x" style="width:1rem;height:1rem;color:#C0392B;"></i>
                        </div>
                        <div>
                            <p style="font-size:0.875rem;font-weight:700;color:#C0392B;">Kehilangan Fokus Browser</p>
                            <p style="font-size:0.75rem;color:#5C6470;margin-top:0.25rem;line-height:1.5;">Terdeteksi saat peserta Alt+Tab, minimize, atau klik taskbar. Menggunakan event <code style="background:#FEE2E2;padding:0 4px;border-radius:3px;font-size:0.7rem;color:#C0392B;">window.blur</code>.</p>
                        </div>
                    </div>
                    <div style="display:flex;align-items:flex-start;gap:0.75rem;background:#FFF7ED;border:1px solid #FED7AA;border-radius:0.75rem;padding:0.875rem;">
                        <div style="width:2rem;height:2rem;background:#FFEDD5;border-radius:0.5rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:0.125rem;">
                            <i data-lucide="minimize-2" style="width:1rem;height:1rem;color:#F97316;"></i>
                        </div>
                        <div>
                            <p style="font-size:0.875rem;font-weight:700;color:#EA580C;">Minimize / Background</p>
                            <p style="font-size:0.75rem;color:#5C6470;margin-top:0.25rem;line-height:1.5;">Terdeteksi saat browser diminimize atau halaman masuk background karena notifikasi.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <p style="font-size:0.6875rem;font-weight:700;color:#5C6470;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:0.75rem;">Mekanisme Peringatan & Sanksi</p>
                <div style="display:flex;flex-direction:column;gap:0;border:1px solid #E2E6EC;border-radius:0.75rem;overflow:hidden;">
                    <div style="display:flex;align-items:flex-start;gap:0.75rem;padding:0.875rem;border-bottom:1px solid #E2E6EC;background:#FEFCE8;">
                        <div style="width:1.75rem;height:1.75rem;background:#FEF08A;border:2px solid #EAB308;border-radius:9999px;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;color:#CA8A04;flex-shrink:0;">1</div>
                        <div><p style="font-size:0.875rem;font-weight:700;color:#1A1A1A;">Pelanggaran ke-1</p><p style="font-size:0.75rem;color:#5C6470;margin-top:0.25rem;">Popup peringatan muncul. Peserta masih dapat melanjutkan.</p></div>
                    </div>
                    <div style="display:flex;align-items:flex-start;gap:0.75rem;padding:0.875rem;border-bottom:1px solid #E2E6EC;background:#FFF7ED;">
                        <div style="width:1.75rem;height:1.75rem;background:#FFEDD5;border:2px solid #F97316;border-radius:9999px;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;color:#EA580C;flex-shrink:0;">2</div>
                        <div><p style="font-size:0.875rem;font-weight:700;color:#1A1A1A;">Pelanggaran ke-2</p><p style="font-size:0.75rem;color:#5C6470;margin-top:0.25rem;">Peringatan terakhir — satu lagi = kuis dikumpulkan otomatis.</p></div>
                    </div>
                    <div style="display:flex;align-items:flex-start;gap:0.75rem;padding:0.875rem;background:#FEF2F2;">
                        <div style="width:1.75rem;height:1.75rem;background:#C0392B;border:2px solid #C0392B;border-radius:9999px;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;color:#fff;flex-shrink:0;">3</div>
                        <div><p style="font-size:0.875rem;font-weight:700;color:#C0392B;">Pelanggaran ke-3 → Auto Submit</p><p style="font-size:0.75rem;color:#5C6470;margin-top:0.25rem;">Kuis dikumpulkan otomatis dengan jawaban yang sudah terisi.</p></div>
                    </div>
                </div>
            </div>
            <div style="background:#F5F7FA;border:1px solid #E2E6EC;border-radius:0.75rem;padding:1rem;">
                <p style="font-size:0.6875rem;font-weight:700;color:#5C6470;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:0.625rem;">Yang Tidak Terdeteksi</p>
                <p style="font-size:0.75rem;color:#5C6470;">— Membaca buku / modul cetak</p>
                <p style="font-size:0.75rem;color:#5C6470;margin-top:0.25rem;">— HP / tablet yang digunakan bersamaan</p>
                <p style="font-size:0.75rem;color:#5C6470;margin-top:0.25rem;">— Diskusi dengan orang di sekitar</p>
                <p style="font-size:0.6875rem;color:#5C6470;margin-top:0.75rem;padding-top:0.75rem;border-top:1px solid #E2E6EC;line-height:1.5;">Untuk ujian bernilai tinggi, pertimbangkan pengawas manual tambahan.</p>
            </div>
        </div>
        <div style="position:sticky;bottom:0;background:#fff;border-top:1px solid #E2E6EC;padding:1rem 1.5rem;display:flex;justify-content:flex-end;border-radius:0 0 1rem 1rem;">
            <button onclick="closeAntiCheatModal()"
                style="padding:0.5rem 1.5rem;background:#F5F7FA;border:1px solid #E2E6EC;border-radius:0.75rem;font-weight:600;font-size:0.875rem;color:#1A1A1A;cursor:pointer;"
                onmouseover="this.style.background='#E2E6EC'" onmouseout="this.style.background='#F5F7FA'">
                Mengerti
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openAntiCheatModal() {
    const m = document.getElementById('anti-cheat-modal');
    if (!m) return;
    m.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeAntiCheatModal() {
    const m = document.getElementById('anti-cheat-modal');
    if (!m) return;
    m.style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAntiCheatModal(); });
</script>
@endpush
