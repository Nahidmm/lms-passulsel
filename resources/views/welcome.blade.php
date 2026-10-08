<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>STRAPSUSPAS - Petugas Paten, Pembinaan Pasti, Pemasyarakatan Berdampak</title>
    <meta name="description" content="STRAPSUSPAS - Platform Pembelajaran & Pengukuran Pemahaman Hukum Disiplin Pegawai Pemasyarakatan Sulawesi Selatan berbasis PP No. 94 Tahun 2021.">
    <link rel="icon" type="image/png" href="{{ asset('logo/strapsuspas.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Instrument+Serif:ital@1&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- GSAP for smooth animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            background-color: #06090e;
            color: #E2E8F0;
            font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
            overflow-x: hidden;
        }

        .font-instrument {
            font-family: 'Instrument Serif', serif;
        }

        .text-prisma {
            color: #E2E8F0;
        }

        .bg-prisma {
            background-color: #E2E8F0;
        }

        .noise-overlay {
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
            opacity: 0.45;
            mix-blend-mode: overlay;
            pointer-events: none;
            z-index: 10;
        }

        .nav-link {
            color: rgba(226, 232, 240, 0.75);
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            color: #ffffff;
        }

        /* Hero typography */
        .hero-title {
            font-size: clamp(3rem, 9vw, 9.5rem);
            line-height: 0.9;
            letter-spacing: -0.04em;
        }

        .feature-card {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .feature-card:hover {
            border-color: rgba(14, 165, 233, 0.4);
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -15px rgba(14, 165, 233, 0.15);
        }

        .step-pill {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>

<body class="antialiased selection:bg-sky-500 selection:text-white">

    <!-- NAVBAR FIXED -->
    <div class="fixed top-4 md:top-6 left-1/2 -translate-x-1/2 z-[100] transition-transform duration-300 w-[92%] max-w-2xl">
        <nav class="bg-[#090d16]/90 backdrop-blur-md rounded-full px-5 py-2.5 md:px-7 md:py-3 flex items-center justify-between shadow-2xl border border-white/10">
            <a href="#" class="flex items-center gap-2.5">
                <img src="{{ asset('logo/strapsuspas.png') }}" alt="Logo STRAPSUSPAS" class="w-8 h-8 object-contain">
                <div class="leading-none">
                    <span class="text-xs font-black tracking-wider text-white block">STRAPSUSPAS</span>
                    <span class="text-[9px] font-bold text-sky-400 tracking-wider uppercase">Kanwil Ditjenpas Sulsel</span>
                </div>
            </a>

            <div class="flex items-center gap-5 sm:gap-7">
                <a href="#about" class="nav-link text-xs font-semibold hidden sm:inline-block">Tentang</a>
                <a href="#alur" class="nav-link text-xs font-semibold hidden sm:inline-block">Alur Pembelajaran</a>
                <a href="#features" class="nav-link text-xs font-semibold">Fasilitas</a>
                
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-4 py-1.5 rounded-full bg-sky-500 hover:bg-sky-400 text-slate-950 font-bold text-xs transition-all shadow-md">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-1.5 rounded-full bg-white/10 hover:bg-white/20 text-white font-semibold text-xs border border-white/15 transition-all">
                        Masuk
                    </a>
                @endauth
            </div>
        </nav>
    </div>

    <!-- SECTION 1: HERO -->
    <section class="relative min-h-screen p-3 md:p-6 flex flex-col justify-between pt-24 md:pt-28">
        <div class="relative w-full rounded-2xl md:rounded-[2.5rem] overflow-hidden bg-gradient-to-b from-[#0b121e] via-[#070b12] to-[#04060a] border border-white/10 flex-1 flex flex-col justify-between p-6 sm:p-10 lg:p-16">
            <!-- Background Elements -->
            <div class="noise-overlay"></div>
            <div class="absolute -top-40 -right-40 w-96 h-96 bg-sky-600/20 rounded-full blur-[120px] pointer-events-none"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-indigo-600/20 rounded-full blur-[120px] pointer-events-none"></div>

            <!-- Top Tagline -->
            <div class="relative z-20 flex flex-wrap items-center gap-3 mb-6">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-sky-500/10 text-sky-400 border border-sky-500/20">
                    <i data-lucide="shield-alert" class="w-3.5 h-3.5"></i> PP No. 94 Tahun 2021
                </span>
                <span class="text-xs text-slate-400 font-medium">
                    Petugas Paten, Pembinaan Pasti, Pemasyarakatan Berdampak
                </span>
            </div>

            <!-- Main Heading & Title -->
            <div class="relative z-20 my-auto py-8">
                <h1 class="hero-title font-extrabold text-white leading-none">
                    STRAPSUSPAS<br>
                    <span class="font-instrument italic font-normal text-sky-400 tracking-normal">Petugas Paten, Pemasyarakatan Berdampak.</span>
                </h1>
                <p class="mt-6 text-sm sm:text-base md:text-lg text-slate-300 max-w-2xl font-normal leading-relaxed">
                    Platform pembelajaran mandiri dan evaluasi kepatuhan hukum disiplin petugas pemasyarakatan berbasis digital di lingkungan Kantor Wilayah Direktorat Jenderal Pemasyarakatan Sulawesi Selatan.
                </p>
            </div>

            <!-- Bottom CTA & Quick Stats -->
            <div class="relative z-20 pt-6 border-t border-white/10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex flex-wrap items-center gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-2.5 px-6 py-3 rounded-full bg-sky-500 hover:bg-sky-400 text-slate-950 font-bold text-sm transition-all shadow-lg shadow-sky-500/20 group">
                            <span>Akses Dashboard Saya</span>
                            <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2.5 px-6 py-3 rounded-full bg-sky-500 hover:bg-sky-400 text-slate-950 font-bold text-sm transition-all shadow-lg shadow-sky-500/20 group">
                            <span>Mulai Pembelajaran</span>
                            <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-full bg-white/5 hover:bg-white/10 text-white font-semibold text-sm border border-white/10 transition-all">
                            Daftar Akun Baru
                        </a>
                    @endauth
                </div>

                <div class="flex items-center gap-6 text-xs text-slate-400">
                    <div>
                        <div class="text-white font-extrabold text-base">4 Pilar</div>
                        <span class="text-[11px]">Pretest &bull; Kuis &bull; Kasus &bull; Ujian</span>
                    </div>
                    <div class="w-px h-8 bg-white/10"></div>
                    <div>
                        <div class="text-white font-extrabold text-base">Otomatis</div>
                        <span class="text-[11px]">Grader Report & Sertifikasi</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 2: ALUR BELAJAR (FLOW) -->
    <section id="alur" class="py-20 md:py-28 px-4 md:px-6 relative bg-[#06090e]">
        <div class="max-w-6xl mx-auto">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-sky-400 text-xs font-bold uppercase tracking-widest mb-2 block">Metodologi Terukur</span>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-white">Alur Pembelajaran Disiplin Berstandar</h2>
                <p class="text-slate-400 text-xs sm:text-sm mt-3">Dirancang untuk memastikan setiap aparatur memahami batasan kewenangan, jenis pelanggaran, serta prosedur penjatuhan hukuman disiplin.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Tahap 1 -->
                <div class="p-6 rounded-2xl bg-[#0d1424] border border-white/5 relative">
                    <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center font-black text-sm mb-4 border border-purple-500/20">
                        01
                    </div>
                    <h3 class="text-base font-bold text-white mb-2">Pretest Diagnostik</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">Uji kinerja awal untuk mengidentifikasi topik mana dari regulasi disiplin yang paling lemah dan memerlukan pendalaman.</p>
                </div>

                <!-- Tahap 2 -->
                <div class="p-6 rounded-2xl bg-[#0d1424] border border-white/5 relative">
                    <div class="w-10 h-10 rounded-xl bg-sky-500/10 text-sky-400 flex items-center justify-center font-black text-sm mb-4 border border-sky-500/20">
                        02
                    </div>
                    <h3 class="text-base font-bold text-white mb-2">Modul & Video Interaktif</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">Pelajari bahan materi resmi, simulasi kasus penegakan disiplin, dan video pembelajaran secara berurutan.</p>
                </div>

                <!-- Tahap 3 -->
                <div class="p-6 rounded-2xl bg-[#0d1424] border border-white/5 relative">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center font-black text-sm mb-4 border border-amber-500/20">
                        03
                    </div>
                    <h3 class="text-base font-bold text-white mb-2">Penugasan & Praktik Kasus</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">Unggah telaah analisis kasus pelanggaran atau sertifikat pelatihan eksternal untuk diverifikasi dan dinilai instruktur.</p>
                </div>

                <!-- Tahap 4 -->
                <div class="p-6 rounded-2xl bg-[#0d1424] border border-white/5 relative">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center font-black text-sm mb-4 border border-emerald-500/20">
                        04
                    </div>
                    <h3 class="text-base font-bold text-white mb-2">Posttest & Buku Nilai</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">Ujian akhir kelulusan. Sistem otomatis mengkalkulasi nilai komposit dan menerbitkan sertifikat digital jika lulus.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 3: FEATURES GRID -->
    <section id="features" class="py-20 px-4 md:px-6 relative bg-[#080d17]">
        <div class="max-w-6xl mx-auto">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-14">
                <div>
                    <span class="text-sky-400 text-xs font-bold uppercase tracking-widest mb-2 block">Fitur Unggulan</span>
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-white">Ekosistem STRAPSUSPAS Terpadu</h2>
                </div>
                <p class="text-xs text-slate-400 max-w-md">Menggabungkan kemudahan pembelajaran mandiri modern dengan visual intuitif bagi instruktur maupun aparatur peserta pemasyarakatan.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <div class="feature-card p-6 rounded-2xl">
                    <div class="w-12 h-12 rounded-xl bg-sky-500/10 text-sky-400 flex items-center justify-center mb-5 border border-sky-500/20">
                        <i data-lucide="file-check-2" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-base font-bold text-white mb-2">Upload Tugas & Pengakuan Sertifikat</h3>
                    <p class="text-xs text-slate-400 leading-relaxed mb-4">
                        Dukung pengumpulan tugas studi kasus disiplin dan upload sertifikat pembelajaran eksternal/webinar tanpa prosedur birokrasi berbelit.
                    </p>
                    <ul class="text-xs text-slate-400 space-y-1.5">
                        <li class="flex items-center gap-2"><i data-lucide="check" class="w-3.5 h-3.5 text-sky-400"></i> Dukungan PDF, DOCX, ZIP</li>
                        <li class="flex items-center gap-2"><i data-lucide="check" class="w-3.5 h-3.5 text-sky-400"></i> Antarmuka grading & review instruktur</li>
                    </ul>
                </div>

                <!-- Card 2 -->
                <div class="feature-card p-6 rounded-2xl">
                    <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center mb-5 border border-purple-500/20">
                        <i data-lucide="target" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-base font-bold text-white mb-2">Diagnostik Kelemahan Pemahaman</h3>
                    <p class="text-xs text-slate-400 leading-relaxed mb-4">
                        Hasil pretest secara otomatis memetakan sub-topik hukum disiplin mana yang di bawah batas nilai agar peserta fokus belajar pada kelemahannya.
                    </p>
                    <ul class="text-xs text-slate-400 space-y-1.5">
                        <li class="flex items-center gap-2"><i data-lucide="check" class="w-3.5 h-3.5 text-purple-400"></i> Indikator "Perlu Penguatan"</li>
                        <li class="flex items-center gap-2"><i data-lucide="check" class="w-3.5 h-3.5 text-purple-400"></i> Rekomendasi modul otomatis</li>
                    </ul>
                </div>

                <!-- Card 3 -->
                <div class="feature-card p-6 rounded-2xl">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center mb-5 border border-emerald-500/20">
                        <i data-lucide="award" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-base font-bold text-white mb-2">Gradebook & Bobot Fleksibel</h3>
                    <p class="text-xs text-slate-400 leading-relaxed mb-4">
                        Laporan nilai komposit berstandar akademis dengan konfigurasi persentase bobot terstandarisasi (Pretest + Kuis + Tugas + Posttest = 100%) dan ekspor Excel.
                    </p>
                    <ul class="text-xs text-slate-400 space-y-1.5">
                        <li class="flex items-center gap-2"><i data-lucide="check" class="w-3.5 h-3.5 text-emerald-400"></i> Penentuan predikat otomatis A/B/C/D</li>
                        <li class="flex items-center gap-2"><i data-lucide="check" class="w-3.5 h-3.5 text-emerald-400"></i> Verifikasi sertifikat via QR & ID</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 4: ABOUT KANWIL SULSEL -->
    <section id="about" class="py-20 px-4 md:px-6 relative bg-[#06090e]">
        <div class="max-w-4xl mx-auto rounded-3xl p-8 sm:p-12 bg-gradient-to-br from-[#0c1527] to-[#080d18] border border-white/10 text-center relative overflow-hidden">
            <div class="mb-6">
                <img src="{{ asset('logo/strapsuspas.png') }}" alt="Logo STRAPSUSPAS" class="w-16 h-16 object-contain mx-auto drop-shadow-lg">
            </div>

            <h2 class="text-2xl sm:text-3xl font-extrabold text-white mb-4">
                Membangun Aparatur Pemasyarakatan yang Bersih & Berintegritas
            </h2>
            <p class="text-slate-300 text-xs sm:text-sm leading-relaxed max-w-2xl mx-auto mb-8">
                Inisiatif digital Kantor Wilayah Direktorat Jenderal Pemasyarakatan Sulawesi Selatan untuk mengoptimalkan sosialisasi regulasi disiplin pegawai, menekan potensi pelanggaran, dan mewujudkan tata kelola pemasyarakatan yang akuntabel.
            </p>

            <div class="flex flex-wrap items-center justify-center gap-3">
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-6 py-2.5 rounded-full bg-sky-500 hover:bg-sky-400 text-slate-950 font-bold text-xs transition-all">
                        Masuk ke Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-6 py-2.5 rounded-full bg-sky-500 hover:bg-sky-400 text-slate-950 font-bold text-xs transition-all">
                        Login Sekarang
                    </a>
                    <a href="{{ route('register') }}" class="px-6 py-2.5 rounded-full bg-white/10 hover:bg-white/20 text-white font-semibold text-xs border border-white/15 transition-all">
                        Registrasi Akun
                    </a>
                @endauth
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="py-8 text-center text-xs text-slate-500 border-t border-white/5 bg-[#04060a]">
        <div class="max-w-6xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-3">
            <span>&copy; {{ date('Y') }} STRAPSUSPAS &bull; Kanwil Ditjenpas Sulawesi Selatan</span>
            <div class="flex items-center gap-4 text-slate-400 text-[11px]">
                <span>Crafted by <span class="font-bold text-slate-200">IR & ANM</span></span>
                <span>&bull;</span>
                <a href="{{ route('login') }}" class="hover:text-white">Portal Masuk</a>
            </div>
        </div>
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>