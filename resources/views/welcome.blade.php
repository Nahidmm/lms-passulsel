<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SPEKTRA - Kanwil Ditjenpas Sulsel</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Almarai for Sans, Instrument Serif for Italic Serif -->
    <link
        href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&family=Instrument+Serif:ital@1&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- GSAP for animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            background-color: #000;
            color: #E1E0CC;
            /* Primary text color requested */
            font-family: 'Almarai', -apple-system, sans-serif;
            overflow-x: hidden;
        }

        .font-instrument {
            font-family: 'Instrument Serif', serif;
        }

        /* Utility for Prisma/Spektra highlight color */
        .text-prisma {
            color: #DEDBC8;
        }

        .bg-prisma {
            background-color: #DEDBC8;
        }

        .noise-overlay {
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
            opacity: 0.7;
            mix-blend-mode: overlay;
            pointer-events: none;
            z-index: 10;
        }

        .bg-noise {
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
            opacity: 0.15;
            pointer-events: none;
            z-index: 0;
        }

        /* Animation utilities */
        .word-wrap {
            display: inline-block;
            overflow: hidden;
            vertical-align: top;
        }

        .word-inner {
            display: inline-block;
            transform: translateY(120%);
        }

        .char-wrap {
            display: inline-block;
            opacity: 0.2;
        }

        .nav-link {
            color: rgba(225, 224, 204, 0.8);
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: #E1E0CC;
        }

        /* Responsive title */
        .hero-title {
            font-size: 25vw;
        }

        @media (min-width: 640px) {
            .hero-title {
                font-size: 22vw;
            }
        }

        @media (min-width: 768px) {
            .hero-title {
                font-size: 18vw;
            }
        }

        @media (min-width: 1024px) {
            .hero-title {
                font-size: 15vw;
            }
        }

        @media (min-width: 1280px) {
            .hero-title {
                font-size: 14vw;
            }
        }

        .superscript {
            position: absolute;
            top: 0.65em;
            right: -0.3em;
            font-size: 0.31em;
        }
    </style>
</head>

<body class="antialiased">

    <!-- SECTION 1: HERO -->
    <section class="relative h-screen p-4 md:p-6 flex flex-col">
        <div class="relative w-full h-full rounded-2xl md:rounded-[2rem] overflow-hidden bg-black">
            <!-- Background Video -->
            <video
                src="https://d8j0ntlcm91z4.cloudfront.net/user_38xzZboKViGWJOttwIXH07lWA1P/hf_20260405_170732_8a9ccda6-5cff-4628-b164-059c500a2b41.mp4"
                autoplay loop muted playsinline class="absolute inset-0 w-full h-full object-cover"></video>

            <!-- Overlays -->
            <div class="noise-overlay"></div>
            <div
                class="absolute inset-0 bg-gradient-to-b from-black/30 via-transparent to-black/60 pointer-events-none z-10">
            </div>

            <!-- Navbar Pill -->
            <div class="fixed top-4 md:top-6 left-1/2 -translate-x-1/2 z-[100] transition-transform duration-300">
                <nav
                    class="bg-black/80 backdrop-blur-md rounded-full px-6 py-2.5 md:px-8 md:py-3 flex items-center gap-5 sm:gap-8 md:gap-10 shadow-2xl border border-white/10">
                    <a href="#about" class="nav-link text-[10px] sm:text-xs md:text-sm font-semibold">Tentang</a>
                    <a href="#features" class="nav-link text-[10px] sm:text-xs md:text-sm font-semibold">Fasilitas</a>
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="nav-link text-[10px] sm:text-xs md:text-sm font-semibold">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}"
                            class="nav-link text-[10px] sm:text-xs md:text-sm font-semibold">Masuk</a>
                    @endauth
                </nav>
            </div>

            <!-- Hero Content -->
            <div class="absolute bottom-0 left-0 right-0 z-20 p-6 md:p-10 lg:p-12">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-end">

                    <!-- Giant Heading -->
                    <div class="md:col-span-8 relative">
                        <h1
                            class="hero-title font-instrument font-medium leading-[0.85] tracking-[-0.07em] text-[#E1E0CC] js-pullup-hero relative inline-block">
                            SPEKTRA<span class="superscript">*</span>
                        </h1>
                    </div>

                    <!-- Description & CTA -->
                    <div class="md:col-span-4 flex flex-col gap-6 md:pb-6">
                        <p class="text-prisma/70 text-xs sm:text-sm md:text-base leading-[1.2] js-fadeup-1 opacity-0">
                            SPEKTRA adalah ekosistem pembelajaran digital interaktif untuk Petugas Pemasyarakatan
                            Kemenimipas, berfokus pada pengembangan potensi dan kompetensi tanpa dibatasi oleh tempat
                            maupun waktu.
                        </p>

                        <div class="js-fadeup-2 opacity-0">
                            @auth
                                <a href="{{ url('/dashboard') }}"
                                    class="group inline-flex items-center gap-2 bg-prisma text-black rounded-full pl-6 pr-2 py-2 font-medium text-sm sm:text-base transition-all hover:gap-3">
                                    <span>Masuk ke Dashboard</span>
                                    <div
                                        class="bg-black rounded-full w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center transition-transform group-hover:scale-110 shrink-0">
                                        <i data-lucide="arrow-right" class="text-white w-4 h-4"></i>
                                    </div>
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                    class="group inline-flex items-center gap-2 bg-prisma text-black rounded-full pl-6 pr-2 py-2 font-medium text-sm sm:text-base transition-all hover:gap-3">
                                    <span>Mulai Belajar</span>
                                    <div
                                        class="bg-black rounded-full w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center transition-transform group-hover:scale-110 shrink-0">
                                        <i data-lucide="arrow-right" class="text-white w-4 h-4"></i>
                                    </div>
                                </a>
                            @endauth
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 2: ABOUT -->
    <section id="about" class="bg-black py-24 md:py-32 px-4 md:px-6 relative">
        <div
            class="max-w-6xl mx-auto bg-[#101010] rounded-3xl p-8 md:p-16 text-center border border-white/5 relative z-10">
            <span
                class="text-prisma text-[10px] sm:text-xs font-bold uppercase tracking-widest mb-8 inline-block">E-Learning
                Passulsel</span>

            <h2
                class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl max-w-4xl mx-auto leading-[0.95] sm:leading-[0.9] js-pullup-about mb-12 text-[#E1E0CC]">
                Ini adalah SPEKTRA, <span class="font-instrument italic text-[#DEDBC8]">platform edukasi pintar.</span>
                Kami merancang pembelajaran interaktif, evaluasi adaptif, dan simulasi terarah.
            </h2>

            <div class="max-w-2xl mx-auto">
                <p class="text-prisma text-xs sm:text-sm md:text-base font-medium js-scroll-text">
                    Selama bertahun-tahun, Kanwil Ditjenpas Sulsel terus berinovasi untuk memberikan pelatihan terbaik
                    bagi petugas pemasyarakatan. Bersama SPEKTRA, kami menciptakan standar baru di mana teknologi AI,
                    kelas virtual, dan materi sinematik melebur menjadi satu pengalaman belajar yang belum pernah ada
                    sebelumnya.
                </p>
            </div>
        </div>
    </section>

    <!-- SECTION 3: FEATURES -->
    <section id="features" class="min-h-screen bg-black relative py-20 px-4 md:px-6">
        <div class="bg-noise"></div>

        <div class="max-w-7xl mx-auto relative z-10">
            <div class="mb-16 js-pullup-features">
                <h2 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-normal text-prisma">Pembelajaran terukur
                    untuk petugas pemasyarakatan.</h2>
                <h3 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-normal text-gray-500">Dirancang demi visi
                    yang jelas. Digerakkan oleh AI.</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:h-[480px]">

                <!-- Card 1: Video -->
                <div
                    class="feature-card relative rounded-2xl overflow-hidden group bg-[#212121] h-[300px] lg:h-full opacity-0">
                    <video
                        src="https://d8j0ntlcm91z4.cloudfront.net/user_38xzZboKViGWJOttwIXH07lWA1P/hf_20260406_133058_0504132a-0cf3-4450-a370-8ea3b05c95d4.mp4"
                        autoplay loop muted playsinline
                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"></video>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                    <div class="absolute bottom-6 left-6 text-[#E1E0CC] font-medium text-lg">Pembelajaran tanpa batas.</div>
                </div>

                <!-- Card 2 -->
                <div
                    class="feature-card bg-[#212121] border border-white/5 rounded-2xl p-6 flex flex-col justify-between h-full opacity-0">
                    <div>
                        <img src="https://images.higgs.ai/?default=1&output=webp&url=https%3A%2F%2Fd8j0ntlcm91z4.cloudfront.net%2Fuser_38xzZboKViGWJOttwIXH07lWA1P%2Fhf_20260405_171918_4a5edc79-d78f-4637-ac8b-53c43c220606.png&w=128&q=85"
                            class="w-10 h-10 sm:w-12 sm:h-12 rounded object-cover mb-6" alt="Icon">
                        <h4 class="text-[#E1E0CC] font-semibold text-lg mb-4 flex items-center gap-2">Materi
                            Terstruktur. <span
                                class="text-xs text-gray-500 bg-white/5 px-2 py-0.5 rounded-full font-mono">01</span>
                        </h4>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3 text-sm text-gray-400"><i data-lucide="check"
                                    class="w-4 h-4 text-prisma shrink-0 mt-0.5"></i> Modul interaktif</li>
                            <li class="flex items-start gap-3 text-sm text-gray-400"><i data-lucide="check"
                                    class="w-4 h-4 text-prisma shrink-0 mt-0.5"></i> Video sinematik eksklusif</li>
                            <li class="flex items-start gap-3 text-sm text-gray-400"><i data-lucide="check"
                                    class="w-4 h-4 text-prisma shrink-0 mt-0.5"></i> Pelacakan progres otomatis</li>
                            <li class="flex items-start gap-3 text-sm text-gray-400"><i data-lucide="check"
                                    class="w-4 h-4 text-prisma shrink-0 mt-0.5"></i> Kuis dan evaluasi harian</li>
                        </ul>
                    </div>
                    <a href="#"
                        class="inline-flex items-center gap-2 text-prisma mt-8 text-sm font-medium hover:text-white transition-colors group">
                        Pelajari lebih lanjut <i data-lucide="arrow-right"
                            class="w-4 h-4 -rotate-45 transition-transform group-hover:translate-x-1 group-hover:-translate-y-1"></i>
                    </a>
                </div>

                <!-- Card 3 -->
                <div
                    class="feature-card bg-[#212121] border border-white/5 rounded-2xl p-6 flex flex-col justify-between h-full opacity-0">
                    <div>
                        <img src="https://images.higgs.ai/?default=1&output=webp&url=https%3A%2F%2Fd8j0ntlcm91z4.cloudfront.net%2Fuser_38xzZboKViGWJOttwIXH07lWA1P%2Fhf_20260405_171741_ed9845ab-f5b2-4018-8ce7-07cc01823522.png&w=128&q=85"
                            class="w-10 h-10 sm:w-12 sm:h-12 rounded object-cover mb-6" alt="Icon">
                        <h4 class="text-[#E1E0CC] font-semibold text-lg mb-4 flex items-center gap-2">Tutor AI
                            (Kecerdasan Buatan). <span
                                class="text-xs text-gray-500 bg-white/5 px-2 py-0.5 rounded-full font-mono">02</span>
                        </h4>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3 text-sm text-gray-400"><i data-lucide="check"
                                    class="w-4 h-4 text-prisma shrink-0 mt-0.5"></i> Analisis jawaban *real-time*</li>
                            <li class="flex items-start gap-3 text-sm text-gray-400"><i data-lucide="check"
                                    class="w-4 h-4 text-prisma shrink-0 mt-0.5"></i> Catatan pembelajaran personal</li>
                            <li class="flex items-start gap-3 text-sm text-gray-400"><i data-lucide="check"
                                    class="w-4 h-4 text-prisma shrink-0 mt-0.5"></i> Simulasi penyusunan laporan</li>
                        </ul>
                    </div>
                    <a href="#"
                        class="inline-flex items-center gap-2 text-prisma mt-8 text-sm font-medium hover:text-white transition-colors group">
                        Pelajari lebih lanjut <i data-lucide="arrow-right"
                            class="w-4 h-4 -rotate-45 transition-transform group-hover:translate-x-1 group-hover:-translate-y-1"></i>
                    </a>
                </div>

                <!-- Card 4 -->
                <div
                    class="feature-card bg-[#212121] border border-white/5 rounded-2xl p-6 flex flex-col justify-between h-full opacity-0">
                    <div>
                        <img src="https://images.higgs.ai/?default=1&output=webp&url=https%3A%2F%2Fd8j0ntlcm91z4.cloudfront.net%2Fuser_38xzZboKViGWJOttwIXH07lWA1P%2Fhf_20260405_171809_f56666dc-c099-4778-ad82-9ad4f209567b.png&w=128&q=85"
                            class="w-10 h-10 sm:w-12 sm:h-12 rounded object-cover mb-6" alt="Icon">
                        <h4 class="text-[#E1E0CC] font-semibold text-lg mb-4 flex items-center gap-2">Ruang Fokus. <span
                                class="text-xs text-gray-500 bg-white/5 px-2 py-0.5 rounded-full font-mono">03</span>
                        </h4>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3 text-sm text-gray-400"><i data-lucide="check"
                                    class="w-4 h-4 text-prisma shrink-0 mt-0.5"></i> Blokir notifikasi gangguan</li>
                            <li class="flex items-start gap-3 text-sm text-gray-400"><i data-lucide="check"
                                    class="w-4 h-4 text-prisma shrink-0 mt-0.5"></i> Sertifikat yang terintegrasi</li>
                            <li class="flex items-start gap-3 text-sm text-gray-400"><i data-lucide="check"
                                    class="w-4 h-4 text-prisma shrink-0 mt-0.5"></i> Kalender akademik terpadu</li>
                        </ul>
                    </div>
                    <a href="#"
                        class="inline-flex items-center gap-2 text-prisma mt-8 text-sm font-medium hover:text-white transition-colors group">
                        Pelajari lebih lanjut <i data-lucide="arrow-right"
                            class="w-4 h-4 -rotate-45 transition-transform group-hover:translate-x-1 group-hover:-translate-y-1"></i>
                    </a>
                </div>

            </div>
        </div>

        <footer class="mt-24 pb-8 text-center text-xs text-gray-600 relative z-10">
            &copy; {{ date('Y') }} Kantor Wilayah Kemenimipas Sulawesi Selatan.
        </footer>
    </section>

    <script>
        // Init Lucide Icons
        lucide.createIcons();
        gsap.registerPlugin(ScrollTrigger);

        // Helper: recursive text node splitting to preserve HTML tags
        function wrapTextNodes(el) {
            const childNodes = Array.from(el.childNodes);
            childNodes.forEach(node => {
                if (node.nodeType === Node.TEXT_NODE) {
                    const text = node.textContent;
                    if (!text.trim()) return; // skip empty/only-whitespace nodes

                    const words = text.split(/(\s+)/); // split by whitespace but keep whitespace in array
                    const fragment = document.createDocumentFragment();
                    words.forEach(word => {
                        if (word.trim().length > 0) {
                            const wrap = document.createElement('span');
                            wrap.className = 'word-wrap'; // display: inline-block
                            const inner = document.createElement('span');
                            inner.className = 'word-inner';
                            inner.textContent = word;
                            wrap.appendChild(inner);
                            fragment.appendChild(wrap);
                        } else {
                            // preserve original whitespace for natural spacing
                            fragment.appendChild(document.createTextNode(word));
                        }
                    });
                    el.replaceChild(fragment, node);
                } else if (node.nodeType === Node.ELEMENT_NODE) {
                    if (!node.classList.contains('superscript')) {
                        wrapTextNodes(node);
                    }
                }
            });
        }

        document.querySelectorAll('.js-pullup-hero, .js-pullup-about, .js-pullup-features').forEach(el => {
            wrapTextNodes(el);
        });

        // Animations on load
        window.addEventListener('load', () => {
            // Hero Title Pullup
            gsap.to('.js-pullup-hero .word-inner', {
                y: "0%",
                duration: 1,
                ease: "power4.out",
                stagger: 0.08
            });

            // Hero Text Fade
            gsap.to('.js-fadeup-1', {
                y: 0, opacity: 1, duration: 1, delay: 0.5, ease: "power3.out", yFrom: 20
            });
            gsap.to('.js-fadeup-2', {
                y: 0, opacity: 1, duration: 1, delay: 0.7, ease: "power3.out", yFrom: 20
            });

            // Scroll Pullup for About
            gsap.to('.js-pullup-about .word-inner', {
                scrollTrigger: {
                    trigger: '#about',
                    start: "top 75%",
                },
                y: "0%",
                duration: 1,
                ease: "power4.out",
                stagger: 0.03
            });

            // Scroll Pullup for Features Text
            gsap.to('.js-pullup-features .word-inner', {
                scrollTrigger: {
                    trigger: '#features',
                    start: "top 75%",
                },
                y: "0%",
                duration: 0.8,
                ease: "power4.out",
                stagger: 0.05
            });

            // Features Cards Stagger Entrance
            gsap.fromTo('.feature-card',
                { scale: 0.95, opacity: 0, y: 30 },
                {
                    scrollTrigger: {
                        trigger: '.feature-card',
                        start: "top 85%",
                    },
                    scale: 1,
                    opacity: 1,
                    y: 0,
                    duration: 0.8,
                    ease: "power3.out",
                    stagger: 0.15
                }
            );

            // Character Scroll Opacity for About Text
            const scrollText = document.querySelector('.js-scroll-text');
            if (scrollText) {
                const text = scrollText.innerText;
                scrollText.innerHTML = '';
                text.split('').forEach(char => {
                    const span = document.createElement('span');
                    span.className = 'char-wrap';
                    span.innerHTML = char === ' ' ? '&nbsp;' : char;
                    scrollText.appendChild(span);
                });

                gsap.to('.js-scroll-text .char-wrap', {
                    scrollTrigger: {
                        trigger: '.js-scroll-text',
                        start: "top 80%",
                        end: "top 20%",
                        scrub: 1,
                    },
                    opacity: 1,
                    stagger: 0.1
                });
            }
        });
    </script>
</body>

</html>