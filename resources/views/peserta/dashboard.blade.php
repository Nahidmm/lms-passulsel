@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('content-class', 'p-0 overflow-y-auto')

@push('styles')
<style>
/* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
   CINEMATIC SCROLL RIG â€” Full 3D Dashboard
   â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */
.cinema-scroll { position: relative; height: calc(100vh + 3600px); }

.cinema-stage {
  position: sticky; top: 0;
  height: 100vh; min-height: 560px;
  overflow: hidden; isolation: isolate;
  background: #7fb4d4;
}
.cinema-world {
  position: absolute; inset: -5vw; overflow: hidden; background: #79b7dd;
  display: flex; align-items: center; justify-content: center;
  transform: scale(1.15);
}

/* Scene images */
.scene-img {
  display: block; position: absolute;
  user-select: none; -webkit-user-drag: none;
  will-change: transform, opacity, filter; pointer-events: none;
}
.sky-img {
  inset: 0; width: 100%; height: 100%; object-fit: cover; z-index: 0;
  filter: blur(var(--blur-px,0px)) brightness(var(--back-brightness,1));
}
.back-stack {
  position: absolute; top: 0; bottom: 0; left: -3vw; right: -3vw; z-index: 1;
  opacity: var(--back-opacity,1);
  transform: translate3d(var(--back-x,0px), var(--back-y,0px), 0) scale(var(--back-scale,0.78));
  transform-origin: 50% 100%; will-change: transform, filter, opacity;
}
.back-img {
  position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover;
  filter: blur(var(--blur-px,0px)) brightness(var(--back-brightness,1));
}
.back-bazaar {
  position: absolute; top: auto; bottom: 0; left: 48%; right: auto;
  width: 112%; height: auto; object-fit: contain; z-index: 3; opacity: 1;
  filter: blur(var(--bazaar-blur-px,0px)) brightness(var(--bazaar-brightness,1)) saturate(var(--bazaar-saturation,1));
  transform: translate3d(-50%, var(--bazaar-y,20vh), 0) scale(0.86);
}
.back-four {
  position: absolute; top: auto; bottom: 0; left: 48%; right: auto;
  width: 112%; height: auto; object-fit: contain;
  z-index: 1; opacity: 0.72; mix-blend-mode: screen;
  transform: translate3d(-50%, calc(var(--four-y,10vh) - 110px), 0) scale(var(--four-scale,0.78));
}
.bridge-img {
  position: absolute; left: 50%; bottom: var(--bridge-bottom,5vh);
  width: var(--bridge-width, 67.2vw); max-width: none !important; height: auto; z-index: 4;
  transform: translate3d(var(--bridge-x,-50%), var(--bridge-y,0px), 0) scale(var(--bridge-scale,1.02));
  transform-origin: 50% 48%;
}
.splitframe-img {
  position: absolute; left: 50%; bottom: -2vh;
  width: 118vw; max-width: none !important; height: auto; pointer-events: none; z-index: 6;
}
.splitframe-left {
  transform: translate3d(var(--split-left-x,-50%), var(--split-left-y,0px), 0) scale(var(--split-left-scale,1));
  transform-origin: 21% 52%;
}
.splitframe-right {
  transform: translate3d(var(--split-right-x,-50%), var(--split-right-y,0px), 0) scale(var(--split-right-scale,1));
  transform-origin: 79% 52%;
}
.frame-two-img {
  position: absolute; left: 50%; bottom: -10vh; top: auto;
  width: 122vw; max-width: none !important; height: auto; min-height: 110vh; object-fit: cover; z-index: 5;
  filter: none !important; opacity: var(--frame2-opacity,0);
  transform: translate3d(var(--frame2-x,-50%), var(--frame2-y,0%), 0) scale(var(--frame2-scale,1.06));
  transform-origin: 50% 48%;
}
.cinema-shade {
  position: absolute; inset: 0; pointer-events: none;
  z-index: var(--shade-z,0); opacity: var(--shade-opacity,1);
  background: linear-gradient(180deg,
    rgba(74,181,224,var(--shade-top-alpha,0)) 0%,
    rgba(74,181,224,var(--shade-mid-alpha,0)) 48%,
    rgba(74,181,224,var(--shade-bottom-alpha,0)) 100%);
}
/* Deep cinematic vignette for content readability */
.cinema-vignette {
  position: absolute; inset: 0; pointer-events: none; z-index: 7;
  opacity: var(--vignette-opacity, 0);
  background:
    radial-gradient(ellipse 90% 100% at 50% 100%, rgba(6,8,14,0.97) 0%, rgba(6,8,14,0.85) 35%, rgba(6,8,14,0) 70%),
    radial-gradient(ellipse 100% 60% at 50% 0%, rgba(6,8,14,0.4) 0%, transparent 100%);
  will-change: opacity;
}

/* Remove padding to make the dashboard full bleed (no gaps) */
.app-content { padding: 0 !important; }

/* â”€â”€ Hero Content â”€â”€ */
.cinema-hero-content {
  position: absolute; left: 50%;
  top: clamp(120px, 25vh, 280px); /* Lowered to center */
  width: min(94vw, 1780px);
  transform: translate3d(-50%, var(--title-y,0px), 0) scale(var(--title-scale,1));
  opacity: var(--title-opacity,1);
  will-change: transform, opacity;
  z-index: 15; /* Increased to stay above vignettes */
  display: flex; flex-direction: column; align-items: center;
  pointer-events: none;
}
.cinema-hero-title {
  margin: 0; color: #fdf4e3;
  font-family: 'Ogg Medium', 'Fraunces', serif;
  font-size: clamp(3.5rem, 10vw, 12rem);
  font-weight: 500; line-height: 0.82;
  text-align: center; letter-spacing: -0.02em;
  text-shadow: 0 12px 60px rgba(0,0,0,0.7), 0 4px 16px rgba(0,0,0,0.5);
}
@keyframes floatY {
  0%, 100% { transform: translate(-50%, 0); }
  50% { transform: translate(-50%, 10px); }
}
.cinema-tagline {
  margin-top: 1.5rem; text-align: center;
  width: 90%; max-width: 900px;
  font-size: 0.75rem; font-weight: 800; letter-spacing: 0.12em; text-transform: uppercase;
  color: #ffffff; font-family: 'Plus Jakarta Sans', sans-serif;
  line-height: 1.7;
  text-shadow: 0 2px 10px rgba(0,0,0,0.9), 0 4px 20px rgba(0,0,0,0.7);
}
.cinema-cta {
  margin-top: 2.25rem; display: flex; gap: 1rem;
  pointer-events: auto;
}
.btn-cinema {
  display: flex; align-items: center; gap: 0.5rem;
  padding: 0.75rem 1.75rem; border-radius: 99px;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 0.75rem; font-weight: 800;
  text-transform: uppercase; letter-spacing: 0.1em;
  text-decoration: none; transition: all 0.3s cubic-bezier(0.22,1,0.36,1);
}
.btn-cinema.primary {
  background: rgba(200, 137, 26, 0.95); color: #0b0e13;
  box-shadow: 0 4px 15px rgba(200, 137, 26, 0.4);
}
.btn-cinema.primary:hover {
  background: #f0b429; transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(200, 137, 26, 0.6);
}
.btn-cinema.secondary {
  background: rgba(6, 8, 14, 0.65); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
  color: #fdf4e3; border: 1px solid rgba(255, 255, 255, 0.25);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
}
.btn-cinema.secondary:hover {
  background: rgba(6, 8, 14, 0.85); border-color: rgba(255, 255, 255, 0.5);
  transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.5);
}

/* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
   GLASS 3D CARD SYSTEM
   â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */
.glass-panel {
  background: rgba(6,8,14,0.76);
  backdrop-filter: blur(32px) saturate(1.4);
  -webkit-backdrop-filter: blur(32px) saturate(1.4);
  border: 1px solid rgba(255,255,255,0.09);
  border-radius: 1.25rem;
  box-shadow:
    0 40px 80px rgba(0,0,0,0.65),
    0 8px 24px rgba(0,0,0,0.4),
    inset 0 1px 0 rgba(255,255,255,0.1),
    inset 0 -1px 0 rgba(0,0,0,0.3);
  transform-style: preserve-3d;
  will-change: transform;
  transition: transform 0.4s cubic-bezier(0.22,1,0.36,1), box-shadow 0.4s ease;
  cursor: default;
}
.glass-panel:hover {
  box-shadow:
    0 60px 100px rgba(0,0,0,0.7),
    0 0 60px rgba(200,137,26,0.1),
    inset 0 1px 0 rgba(255,255,255,0.14),
    inset 0 -1px 0 rgba(0,0,0,0.3);
  border-color: rgba(200,137,26,0.2);
}
/* Stat card variant */
.stat-glass-card {
  background: rgba(6,8,14,0.72);
  backdrop-filter: blur(28px) saturate(1.3);
  -webkit-backdrop-filter: blur(28px) saturate(1.3);
  border-radius: 1.125rem; padding: 1.125rem 1.125rem 1rem;
  border: 1px solid rgba(255,255,255,0.09);
  box-shadow:
    0 28px 56px rgba(0,0,0,0.6),
    0 4px 12px rgba(0,0,0,0.35),
    inset 0 1px 0 rgba(255,255,255,0.1),
    inset 0 -1px 0 rgba(0,0,0,0.25);
  transform-style: preserve-3d;
  will-change: transform;
  transition: transform 0.38s cubic-bezier(0.22,1,0.36,1), box-shadow 0.38s ease, border-color 0.3s;
  position: relative; overflow: hidden;
}
.stat-glass-card::after {
  content: '';
  position: absolute; inset: 0; border-radius: inherit;
  background: linear-gradient(135deg, rgba(255,255,255,0.04) 0%, transparent 55%);
  pointer-events: none;
}
.stat-glass-card:hover {
  box-shadow:
    0 44px 72px rgba(0,0,0,0.65),
    0 0 50px rgba(200,137,26,0.1),
    inset 0 1px 0 rgba(255,255,255,0.14);
}

/* Accent top border per card */
.sgc-violet  { border-top: 2px solid rgba(139,92,246,0.75); }
.sgc-amber   { border-top: 2px solid rgba(200,137,26,0.75); }
.sgc-rose    { border-top: 2px solid rgba(244,63,94,0.75); }
.sgc-emerald { border-top: 2px solid rgba(16,185,129,0.75); }

/* Ambient glow dot on each card top */
.sgc-violet::before  { background: radial-gradient(circle, rgba(139,92,246,0.18) 0%, transparent 70%); }
.sgc-amber::before   { background: radial-gradient(circle, rgba(200,137,26,0.18) 0%, transparent 70%); }
.sgc-rose::before    { background: radial-gradient(circle, rgba(244,63,94,0.18) 0%, transparent 70%); }
.sgc-emerald::before { background: radial-gradient(circle, rgba(16,185,129,0.18) 0%, transparent 70%); }
.stat-glass-card::before {
  content: ''; position: absolute;
  top: -40px; left: -40px; width: 120px; height: 120px;
  border-radius: 50%; pointer-events: none;
}


.sgc-icon {
  width: 32px; height: 32px; border-radius: 0.625rem;
  display: flex; align-items: center; justify-content: center;
  margin-bottom: 0.75rem; position: relative; z-index: 1;
}
.sgc-val {
  font-family: 'Fraunces', 'Ogg Medium', serif;
  font-size: 1.875rem; font-weight: 600; color: #fdf4e3;
  line-height: 1; margin-bottom: 0.2rem; position: relative; z-index: 1;
}
.sgc-label {
  font-size: 0.65rem; font-weight: 700; letter-spacing: 0.1em;
  text-transform: uppercase; color: rgba(253,244,227,0.45);
  font-family: 'Plus Jakarta Sans', sans-serif; position: relative; z-index: 1;
}
.sgc-trend {
  display: inline-flex; align-items: center; gap: 0.25rem;
  margin-top: 0.625rem; font-size: 0.65rem; font-weight: 700;
  font-family: 'Plus Jakarta Sans', sans-serif;
  padding: 0.2rem 0.55rem; border-radius: 999px;
  position: relative; z-index: 1;
}
.sgc-trend.up    { color: #6ee7b7; background: rgba(16,185,129,0.18); border: 1px solid rgba(16,185,129,0.25); }
.sgc-trend.down  { color: #fda4af; background: rgba(244,63,94,0.18); border: 1px solid rgba(244,63,94,0.25); }
.sgc-trend.flat  { color: rgba(253,244,227,0.45); background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.09); }

/* Section label */
.cinema-section-label {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 0.6rem; font-weight: 700;
  letter-spacing: 0.2em; text-transform: uppercase;
  color: #f0b429; margin-bottom: 0.875rem;
  display: flex; align-items: center; gap: 0.5rem;
}
.cinema-section-label::after {
  content: ''; flex: 1; height: 1px;
  background: linear-gradient(90deg, rgba(240,180,41,0.4), transparent);
}

/* â”€â”€â”€ Data overlays (Stats & Analytics) â”€â”€â”€ */
.cinema-data-overlay {
  position: absolute;
  bottom: 3vh; left: 0; right: 0;
  z-index: 13; padding: 0 1.5rem 2.5rem;
  display: flex; flex-direction: column; align-items: center; justify-content: flex-end;
  pointer-events: none;
}
.cinema-data-container {
  width: 100%; max-width: 1180px;
  display: flex; flex-direction: column; gap: 1.5rem;
}

#cinema-analytics {
  opacity: var(--analytics-opacity, 0);
  transform: translateY(var(--analytics-y, 90px));
  will-change: transform, opacity;
}
#cinema-analytics.ready { pointer-events: auto; }

#cinema-stats {
  opacity: var(--stats-opacity, 0);
  transform: translateY(var(--stats-y, 70px));
  will-change: transform, opacity;
}
#cinema-stats.ready { pointer-events: auto; }

.stats-4-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 0.625rem;
}
@media (min-width: 560px) { .stats-4-grid { grid-template-columns: repeat(4, 1fr); gap: 0.75rem; } }

.cinema-analytics-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 0.75rem;
}
@media (min-width: 640px) {
  .cinema-analytics-grid { grid-template-columns: 1fr 320px; gap: 1rem; }
}

/* Chart header */
.glass-panel-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 1rem 1.25rem 0.875rem;
  border-bottom: 1px solid rgba(255,255,255,0.06);
}
.glass-panel-header h3 {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 0.825rem; font-weight: 700;
  color: rgba(253,244,227,0.8); margin: 0;
  display: flex; align-items: center; gap: 0.5rem;
}

/* Activity items */
.activity-item {
  display: flex; align-items: start; gap: 0.75rem;
  padding: 0.75rem 1.125rem;
  border-bottom: 1px solid rgba(255,255,255,0.04);
  transition: background 0.15s ease;
}
.activity-item:last-child { border-bottom: none; }
.activity-item:hover { background: rgba(255,255,255,0.025); }

/* Native Mobile Redesign (No Parallax) */
@media (max-width: 768px) {
  /* Disable 3600px scroll trick */
  .cinema-scroll { height: auto !important; min-height: 100vh; overflow-x: hidden; padding-bottom: 6rem; }
  
  /* Make the stage a simple background container */
  .cinema-stage { 
    position: relative !important; 
    height: auto !important; 
    overflow: visible !important; 
    clip-path: none !important; 
    display: flex; flex-direction: column; 
    align-items: center; 
  }
  
  /* Hide the 3D complex layered world */
  .cinema-world { display: none !important; }
  
  /* Mobile background */
  .cinema-stage::before {
    content: ''; position: fixed; inset: 0;
    background: linear-gradient(to bottom, rgba(11,14,19,0.5) 0%, #0b0e13 100%), url('https://images.unsplash.com/photo-1517400508447-f8dd518b86e3?q=80&w=2070') center/cover;
    z-index: -1;
  }
  
  /* Make all overlays static, stacked natively */
  .cinema-hero-content {
    position: relative !important;
    top: 0 !important; left: 0 !important; transform: none !important;
    padding: 3rem 1rem 2rem; margin-top: 5vh; width: 100%;
  }
  
  .cinema-data-overlay {
    position: relative !important;
    bottom: auto !important; left: auto !important; right: auto !important;
    width: 100% !important; padding: 0 1rem !important; margin-bottom: 2rem;
    display: flex; flex-direction: column; gap: 2rem;
  }
  
  #cinema-stats, #cinema-analytics, .cinema-stats-overlay, .cinema-analytics-overlay {
    position: relative !important;
    left: auto !important; top: auto !important; bottom: auto !important;
    transform: none !important;
    opacity: 1 !important;
    pointer-events: auto !important;
    width: 100% !important;
    padding: 0; margin-bottom: 0;
  }
  
  .mobile-hidden { display: none !important; }

  .cinema-hero-title { font-size: clamp(2.5rem, 12vw, 4rem); letter-spacing: 0.05em; }
  .cinema-tagline { font-size: 0.8rem; letter-spacing: 0.05em; line-height: 1.5; }
  .cinema-cta { flex-direction: column; gap: 1rem; align-items: center; width: 100%; padding: 0 1.5rem; }
  .btn-cinema { width: 100%; justify-content: center; }
  
  .stats-4-grid { grid-template-columns: 1fr; gap: 1rem; }
  .glass-panel { padding: 1.25rem !important; border-radius: 1rem; }
  
  /* Hide the scroll indicator since mobile is obvious */
  .scroll-indicator { display: none !important; }
}
@media (prefers-reduced-motion: reduce) {
  .scene-img, .back-stack, .cinema-hero-title, .cinema-tagline,
  #cinema-stats, #cinema-analytics { transition: none !important; }
}
</style>
@endpush

@section('content')

<section class="cinema-scroll" id="cinema-dash" aria-label="Dashboard">

  <div class="cinema-stage" id="cinema-stage">
    <div class="cinema-world">

      {{-- Sky --}}
      <img class="scene-img sky-img" alt=""
        src="https://raft-blast-61784561.figma.site/_assets/v11/16b5007d9c93971e26ffe4e0e3e37946f6bd538c.png">

      {{-- Back stack --}}
      <div class="back-stack">
        <img class="scene-img back-four" alt=""
          src="https://raft-blast-61784561.figma.site/_assets/v11/8a7f8af50e0ce92ec2e228e7b0b4112178c51cf1.png">
        <img class="scene-img back-bazaar" alt=""
          src="https://raft-blast-61784561.figma.site/_assets/v11/864afe00e41e2fa20a5aa546e15cb807e0f81384.png">
      </div>

      {{-- Splitframes --}}
      <img class="scene-img splitframe-img splitframe-left" alt=""
        src="https://raft-blast-61784561.figma.site/_assets/v11/7536d7b60a1fce482cf6edf3f0bffd3bad5d0f8a.png">
      <img class="scene-img splitframe-img splitframe-right" alt=""
        src="https://raft-blast-61784561.figma.site/_assets/v11/392db6a6a6b98e868bd7f8d3f55bb719d51e5028.png">

      {{-- Bridge --}}
      <img class="scene-img bridge-img" alt=""
        src="https://raft-blast-61784561.figma.site/_assets/v11/c6a6d8ef49bca43f708aa852692942c45ec950d4.png">

      {{-- Frame two (river) --}}
      <img class="scene-img frame-two-img" alt=""
        src="https://raft-blast-61784561.figma.site/_assets/v11/ba75252bab2b1c510987b74837770f7bc8a6b2d4.png">

      <div class="cinema-shade"></div>
      <div class="cinema-vignette"></div>
    </div>

    {{-- â”€â”€ HERO CONTENT â”€â”€ --}}
    <div class="cinema-hero-content">
      <h1 class="cinema-hero-title">SPEKTRA</h1>
      <p class="cinema-tagline">
        Selamat datang, {{ explode(' ', Auth::user()->nama)[0] }}!
        <br>
        <span style="font-size: 0.85em; opacity: 0.85; margin-top: 0.5rem; display: block; font-weight: 700;">
          Quest berikutnya sudah menunggumu. Siap maju?
        </span>
      </p>
      
      <div class="cinema-cta">
        <a href="javascript:void(0)" class="btn-cinema primary" onclick="const y=1000; const m=document.getElementById('app-main'); const c=document.getElementById('app-content'); if(m && m.scrollHeight>m.clientHeight+50) m.scrollTo({top:y, behavior:'smooth'}); else if(c && c.scrollHeight>c.clientHeight+50) c.scrollTo({top:y, behavior:'smooth'}); else window.scrollTo({top:y, behavior:'smooth'});">
          <i data-lucide="sword" style="width:16px;height:16px;"></i> Lihat Quest & Profil
        </a>
        <a href="javascript:void(0)" class="btn-cinema secondary" onclick="const y=2400; const m=document.getElementById('app-main'); const c=document.getElementById('app-content'); if(m && m.scrollHeight>m.clientHeight+50) m.scrollTo({top:y, behavior:'smooth'}); else if(c && c.scrollHeight>c.clientHeight+50) c.scrollTo({top:y, behavior:'smooth'}); else window.scrollTo({top:y, behavior:'smooth'});">
          <i data-lucide="award" style="width:16px;height:16px;"></i> Hall of Fame
        </a>
      </div>
      
      {{-- Scroll Indicator --}}
      <div class="mobile-hidden" style="position: absolute; bottom: -12vh; left: 50%; transform: translateX(-50%); display: flex; flex-direction: column; align-items: center; gap: 0.5rem; opacity: 0.6; pointer-events: none; animation: floatY 2.5s ease-in-out infinite;">
        <span style="font-size: 0.65rem; font-weight: 800; letter-spacing: 0.15em; text-transform: uppercase; color: rgba(255,255,255,0.8); text-shadow: 0 2px 4px rgba(0,0,0,0.5);">Scroll ke bawah</span>
        <i data-lucide="chevron-down" style="width: 24px; height: 24px; color: rgba(255,255,255,0.9); filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5));"></i>
      </div>
    </div>

    {{-- â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
         DATA OVERLAY (Analytics & Stats)
         â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
    <div class="cinema-data-overlay">
      <div class="cinema-data-container">

        {{-- ANALYTICS PANEL (Appears second, ~scroll 1700-2600) --}}
        <div id="cinema-analytics">
          <div class="cinema-section-label">
            <i data-lucide="gamepad-2" style="width:11px;height:11px;"></i>
            Misi & Peringkat
          </div>
          <div class="cinema-analytics-grid">
            {{-- Left column --}}
            <div style="display:flex; flex-direction:column; gap:0.75rem;">
                {{-- Profile Glass Panel --}}
                <div class="glass-panel js-3d-card" style="padding:1.25rem;">
                    <div style="display:flex; align-items:center; gap:1rem;">
                        <img src="{{ Auth::user()->avatar_url }}" style="width:3.5rem;height:3.5rem;border-radius:1rem;object-fit:cover;border:1px solid rgba(139,92,246,0.3);" alt="">
                        <div>
                            <span style="font-size:0.65rem;font-weight:800;color:rgba(139,92,246,0.9);text-transform:uppercase;letter-spacing:0.1em;display:flex;align-items:center;gap:0.25rem;"><i data-lucide="swords" style="width:10px;height:10px;"></i> {{ Auth::user()->jabatan->nama_jabatan ?? 'Peserta' }}</span>
                            <div style="font-family:'Ogg Medium',serif;font-size:1.25rem;color:#fff;margin-top:0.1rem;">{{ explode(' ', Auth::user()->nama)[0] }}</div>
                            <div style="font-size:0.75rem;color:rgba(255,255,255,0.6);margin-top:0.1rem;font-weight:600;"><span style="color:#f0b429;font-weight:800;">{{ number_format(Auth::user()->getTotalPoin()) }}</span> XP Total</div>
                        </div>
                    </div>
                </div>

                {{-- Katalog Pelatihan --}}
                <a href="{{ route('peserta.pelatihan.index') }}" class="glass-panel js-3d-card" style="padding:1.25rem;text-decoration:none;display:flex;align-items:center;gap:1rem;transition:all 0.3s;cursor:pointer;" onmouseenter="this.style.background='rgba(0,0,0,0.5)'" onmouseleave="this.style.background='rgba(0,0,0,0.4)'">
                    <div style="width:2.5rem;height:2.5rem;border-radius:0.75rem;background:rgba(139,92,246,0.15);border:1px solid rgba(139,92,246,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i data-lucide="map" style="width:18px;height:18px;color:#c4b5fd;"></i>
                    </div>
                    <div style="flex:1;">
                        <div style="font-weight:700;color:#fff;font-size:0.95rem;">Katalog Pelatihan</div>
                        <div style="font-size:0.7rem;color:rgba(255,255,255,0.6);margin-top:0.1rem;">Pilih pelatihan dan selesaikan misi.</div>
                    </div>
                    <i data-lucide="chevron-right" style="width:16px;height:16px;color:rgba(255,255,255,0.4);"></i>
                </a>

                {{-- AI Tutor --}}
                @if(!Auth::user()->hasActiveSesiEvaluasi())
                <a href="{{ route('ai.index') }}" class="glass-panel js-3d-card" style="padding:1.25rem;text-decoration:none;display:flex;align-items:center;gap:1rem;transition:all 0.3s;cursor:pointer;" onmouseenter="this.style.background='rgba(0,0,0,0.5)'" onmouseleave="this.style.background='rgba(0,0,0,0.4)'">
                    <div style="width:2.5rem;height:2.5rem;border-radius:0.75rem;background:rgba(6,182,212,0.15);border:1px solid rgba(6,182,212,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i data-lucide="bot" style="width:18px;height:18px;color:#67e8f9;"></i>
                    </div>
                    <div style="flex:1;">
                        <div style="font-weight:700;color:#fff;font-size:0.95rem;">AI Tutor</div>
                        <div style="font-size:0.7rem;color:rgba(255,255,255,0.6);margin-top:0.1rem;">Asisten AI pribadimu.</div>
                    </div>
                    <i data-lucide="chevron-right" style="width:16px;height:16px;color:rgba(255,255,255,0.4);"></i>
                </a>
                @endif
            </div>

            {{-- Leaderboard feed glass panel --}}
            <div class="glass-panel js-3d-card" id="activity-panel" style="display:flex;flex-direction:column;overflow:hidden;">
              <div class="glass-panel-header" style="flex-shrink:0;">
                <h3>
                  <i data-lucide="trophy" style="width:14px;height:14px;color:#f0b429;"></i>
                  Hall of Fame (Top 5)
                </h3>
              </div>
              <div class="activity-feed" style="flex:1;overflow-y:auto;padding:0.75rem;display:flex;flex-direction:column;gap:0.35rem;">
                @php $rankColors = ['color:#f0b429', 'color:#cbd5e1', 'color:#d97706']; @endphp
                @forelse($leaderboard ?? [] as $i => $u)
                  @php $isMe = $u->id === Auth::id(); @endphp
                  <div class="eval-item" style="display:flex;align-items:center;gap:0.75rem;padding:0.6rem 0.75rem;border-radius:0.75rem;{{ $isMe ? 'background:rgba(139,92,246,0.1);border-color:rgba(139,92,246,0.2);' : 'background:rgba(255,255,255,0.02);' }}">
                    <div style="width:20px;text-align:center;flex-shrink:0;">
                        @if($i < 3)
                            <i data-lucide="award" style="width:16px;height:16px;{{ $rankColors[$i] }};margin:0 auto;"></i>
                        @else
                            <span style="font-size:0.75rem;font-weight:800;color:rgba(255,255,255,0.5);">#{{ $i+1 }}</span>
                        @endif
                    </div>
                    <img src="{{ $u->avatar_url }}" style="width:32px;height:32px;border-radius:0.5rem;object-fit:cover;{{ $isMe ? 'border:1px solid rgba(139,92,246,0.5)' : '' }}" alt="">
                    <div style="flex:1;min-width:0;">
                      <div style="font-size:0.85rem;font-weight:700;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ explode(' ', $u->nama)[0] }}
                        @if($isMe) <span style="font-size:0.55rem;background:rgba(139,92,246,0.8);color:#fff;padding:0.15rem 0.4rem;border-radius:0.25rem;margin-left:0.35rem;text-transform:uppercase;letter-spacing:0.05em;font-weight:900;">Kamu</span> @endif
                      </div>
                      <div style="font-size:0.7rem;color:rgba(255,255,255,0.5);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:0.1rem;">{{ $u->jabatan->nama_jabatan ?? 'Peserta' }}</div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:0.9rem;font-weight:800;color:#f0b429;">{{ number_format($u->total_poin) }}</div>
                        <div style="font-size:0.6rem;font-weight:800;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:0.1em;margin-top:0.1rem;">XP</div>
                    </div>
                  </div>
                @empty
                  <div style="padding:2rem;text-align:center;">
                    <i data-lucide="ghost" style="width:24px;height:24px;color:rgba(255,255,255,0.2);margin:0 auto 0.5rem;"></i>
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.4);">Belum ada peringkat</div>
                  </div>
                @endforelse
              </div>
            </div>
          </div>
        </div>

        {{-- PRETEST ANALYSIS SECTION (Appears below analytics) --}}
        @if(isset($pretestResults) && $pretestResults->count() > 0)
        <div id="cinema-pretest" style="margin-top:2rem;">
          <div class="cinema-section-label">
            <i data-lucide="brain" style="width:11px;height:11px;"></i>
            Analisis Pemahaman (Pretest)
          </div>
          <div class="cinema-analytics-grid">
              {{-- Radar Chart Card --}}
              <div class="glass-panel js-3d-card" style="padding:1.5rem; display:flex; flex-direction:column; align-items:center;">
                  <h4 style="color:#fff; font-family:'Plus Jakarta Sans',sans-serif; font-weight:700; font-size:0.9rem; margin-bottom:1rem; align-self:flex-start;">Pemetaan Kompetensi</h4>
                  <div style="width: 100%; max-width: 320px; aspect-ratio: 1/1; position: relative; display:flex; justify-content:center; align-items:center;">
                      <canvas id="pretestRadarChart"></canvas>
                  </div>
              </div>

              {{-- Rekomendasi Pelatihan Card --}}
              <div class="glass-panel js-3d-card" style="padding:1.5rem;">
                  <h4 style="color:#fff; font-family:'Plus Jakarta Sans',sans-serif; font-weight:700; font-size:0.9rem; margin-bottom:1rem; display:flex; align-items:center; gap:0.5rem;">
                      <i data-lucide="lightbulb" style="color:#f0b429; width:18px; height:18px;"></i>
                      Rekomendasi Pelatihan
                  </h4>
                  @if(isset($rekomendasi) && $rekomendasi->count() > 0)
                      <div style="display:flex; flex-direction:column; gap:0.75rem;">
                          @foreach($rekomendasi as $rek)
                              <a href="{{ route('peserta.pelatihan.show', $rek->pelatihan->id) }}" class="activity-item" style="display:block; text-decoration:none; background:rgba(255,255,255,0.03); border-radius:0.75rem; padding:1rem;">
                                  <div style="font-weight:700; color:#c4b5fd; font-size:0.85rem; margin-bottom:0.25rem;">{{ $rek->pelatihan->judul }}</div>
                                  <div style="font-size:0.7rem; color:rgba(255,255,255,0.6); line-height:1.4;">{{ $rek->alasan }}</div>
                              </a>
                          @endforeach
                      </div>
                  @else
                      <div style="padding:1.5rem; text-align:center; background:rgba(16,185,129,0.1); border-radius:0.75rem; border:1px solid rgba(16,185,129,0.2); margin-top:1rem;">
                          <i data-lucide="check-circle" style="color:#6ee7b7; width:32px; height:32px; margin:0 auto 0.75rem;"></i>
                          <div style="color:#6ee7b7; font-weight:700; font-size:0.9rem;">Luar Biasa!</div>
                          <div style="color:rgba(255,255,255,0.6); font-size:0.75rem; margin-top:0.35rem;">Skor Anda sudah memenuhi standar di semua topik. Anda dapat memilih pelatihan manapun untuk pengembangan lanjutan.</div>
                      </div>
                  @endif
              </div>
          </div>
        </div>
        @endif

        {{-- STATS PANEL (Appears first, ~scroll 800-1400) --}}
        <div id="cinema-stats">
          <div class="cinema-section-label">
            <i data-lucide="target" style="width:11px;height:11px;"></i>
            Ringkasan Pelatihan
          </div>
          <div class="stats-4-grid">

            <div class="stat-glass-card sgc-violet js-3d-card">
              <div class="sgc-icon" style="background:rgba(139,92,246,0.15);border:1px solid rgba(139,92,246,0.25);">
                <i data-lucide="check-circle-2" style="width:16px;height:16px;color:#c4b5fd;"></i>
              </div>
              <div class="sgc-val">{{ $materiSelesai ?? 0 }}<span style="font-size:0.5em;color:rgba(255,255,255,0.5);">/{{ $totalMateri ?? 0 }}</span></div>
              <div class="sgc-label">MATERI SELESAI</div>
              <div class="sgc-sub">
                <span style="color:rgba(255,255,255,0.5);">Progress materi wajib</span>
              </div>
            </div>

            <div class="stat-glass-card sgc-cyan js-3d-card">
              <div class="sgc-icon" style="background:rgba(6,182,212,0.15);border:1px solid rgba(6,182,212,0.25);">
                <i data-lucide="crosshair" style="width:16px;height:16px;color:#67e8f9;"></i>
              </div>
              <div class="sgc-val">{{ $rataNilai ?? 0 }}</div>
              <div class="sgc-label">RATA-RATA NILAI</div>
              <div class="sgc-sub">
                <span style="color:rgba(255,255,255,0.5);">Dari seluruh evaluasi</span>
              </div>
            </div>

            <div class="stat-glass-card sgc-amber js-3d-card">
              <div class="sgc-icon" style="background:rgba(245,158,11,0.15);border:1px solid rgba(245,158,11,0.25);">
                <i data-lucide="crown" style="width:16px;height:16px;color:#fcd34d;"></i>
              </div>
              <div class="sgc-val">#{{ $userRank ?? '-' }}</div>
              <div class="sgc-label">PERINGKAT GLOBAL</div>
              <div class="sgc-sub">
                <span style="color:rgba(255,255,255,0.5);">Dari total peserta</span>
              </div>
            </div>

            <div class="stat-glass-card sgc-emerald js-3d-card">
              <div class="sgc-icon" style="background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.25);">
                <i data-lucide="trending-up" style="width:16px;height:16px;color:#6ee7b7;"></i>
              </div>
              <div class="sgc-val" style="color:#6ee7b7;">{{ $progres ?? 0 }}%</div>
              <div class="sgc-label">PROGRESS TOTAL</div>
              <div class="sgc-sub">
                <div style="width:100%;height:3px;background:rgba(255,255,255,0.1);border-radius:2px;margin-top:4px;overflow:hidden;">
                  <div style="width:{{ $progres ?? 0 }}%;height:100%;background:#6ee7b7;border-radius:2px;"></div>
                </div>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>{{-- /cinema-stats-overlay --}}

  </div>{{-- /cinema-stage --}}
</section>{{-- /cinema-scroll --}}

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
/* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
   CINEMATIC SCROLL ENGINE
   Scroll 0â€“450:    Title fades
   Scroll 350â€“1350: Bridge + splitframe animation
   Scroll 800â€“1400: Stats overlay appears (bottom)
   Scroll 1700â€“2600:Analytics panel appears (center)
   â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */
(function() {
    const section      = document.getElementById('cinema-dash');
    const stage        = document.getElementById('cinema-stage');
    const statsEl      = document.getElementById('cinema-stats');
    const analyticsEl  = document.getElementById('cinema-analytics');
    if (!section || !stage) return;

    const rm = window.matchMedia('(prefers-reduced-motion: reduce)');
    let txMX = 0, txMY = 0, mX = 0, mY = 0;
    let tScroll = 0, sScroll = 0, init = false, raf = false;

    function clamp(v, lo=0, hi=1) { return Math.min(hi, Math.max(lo, v)); }
    function ss(e0, e1, v) { const x = clamp((v-e0)/(e1-e0)); return x*x*(3-2*x); }
    function lerp(a, b, t) { return a + (b-a)*t; }
    function segIO(s, a, b, c, d) {
        const en = ss(a,b,s), ex = ss(c,d,s);
        return { enter:en, exit:ex, active: en*(1-ex) };
    }
    function getDist() {
        return clamp(-section.getBoundingClientRect().top, 0, section.offsetHeight - window.innerHeight);
    }

    function update() {
        raf = false;
        tScroll = getDist();
        if (!init || rm.matches) { sScroll = tScroll; init = true; }
        else { sScroll = lerp(sScroll, tScroll, 0.14); }
        if (Math.abs(sScroll - tScroll) < 0.08) sScroll = tScroll;

        mX = lerp(mX, txMX, 0.12);
        mY = lerp(mY, txMY, 0.12);

        const s = stage.style;
        const f2 = segIO(sScroll, 350, 820, 1100, 1420);
        const f3 = segIO(sScroll, 1600, 2000, 2100, 2300);
        const prog = clamp(sScroll / 3600);
        const titleExit  = ss(60, 450, sScroll);
        const statsEnter = ss(800, 1420, sScroll);
        const analyEnter = ss(1700, 2500, sScroll);
        const blurAct    = clamp(f2.active + f3.active * 0.5);
        const splitDrift = Math.pow(f2.enter, 1.5);
        const backScale  = 0.78 + prog * 0.22 + f2.enter * 0.16 + f3.enter * 0.1;
        const heroY      = prog * -70;
        const heroScale  = prog * 0.2;
        const vigOpacity = clamp(statsEnter + analyEnter * 0.5);

        // Back stack
        s.setProperty('--back-opacity',    (1 - f2.active * 0.06).toFixed(4));
        s.setProperty('--back-x',          rm.matches ? '0px' : `${mX * -12}px`);
        s.setProperty('--back-y',          rm.matches ? '0px' : `${mY * -4}px`);
        s.setProperty('--back-scale',      backScale.toFixed(4));
        s.setProperty('--four-y',          `${10 + prog * 10}vh`);
        s.setProperty('--four-scale',      (0.78 + prog * 0.14).toFixed(4));
        s.setProperty('--bazaar-y',        `${20 - prog * 8}vh`);

        // Blur / brightness
        s.setProperty('--blur-px',          `${blurAct * 14}px`);
        s.setProperty('--back-brightness',  (1 - blurAct * 0.25).toFixed(4));
        s.setProperty('--bazaar-blur-px',   `${f2.active * 14}px`);
        s.setProperty('--bazaar-brightness',(1 - f2.active * 0.25 - f3.active * 0.05).toFixed(4));
        s.setProperty('--bazaar-saturation',(1 + f3.active * 0.15).toFixed(4));

        // Shade
        s.setProperty('--shade-opacity',      '1');
        s.setProperty('--shade-z',            f2.active > 0.02 ? '2' : '0');
        s.setProperty('--shade-top-alpha',    (blurAct * 0.45).toFixed(4));
        s.setProperty('--shade-mid-alpha',    (blurAct * 0.40).toFixed(4));
        s.setProperty('--shade-bottom-alpha', (blurAct * 0.50).toFixed(4));
        s.setProperty('--vignette-opacity',   vigOpacity.toFixed(4));

        // Title
        s.setProperty('--title-y',       `${titleExit * -200}px`);
        s.setProperty('--title-scale',   (1 - titleExit * 0.08).toFixed(4));
        s.setProperty('--title-opacity', (1 - titleExit).toFixed(4));

        // Bridge
        const bx = rm.matches ? '-50%' : `calc(-50% + ${mX * 16}px)`;
        s.setProperty('--bridge-x',      bx);
        s.setProperty('--bridge-y',      `${mY * 8 + heroY - f2.exit * 740}px`);
        s.setProperty('--bridge-bottom', `${5 - f2.enter * 13}vh`);
        s.setProperty('--bridge-width',  `${67.2 + f2.enter * 37}vw`);
        s.setProperty('--bridge-scale',  (1.02 + heroScale + f2.exit * 0.45).toFixed(4));

        // Splitframes
        const sx = rm.matches ? 0 : mX * 20;
        s.setProperty('--split-left-x',    `calc(-50% + ${-splitDrift*44}vw + ${sx}px)`);
        s.setProperty('--split-left-y',    `${mY*9 + heroY - splitDrift*165}px`);
        s.setProperty('--split-left-scale',(1 + heroScale + f2.enter*0.7).toFixed(4));
        s.setProperty('--split-right-x',   `calc(-50% + ${ splitDrift*44}vw + ${sx}px)`);
        s.setProperty('--split-right-y',   `${mY*9 + heroY - splitDrift*165}px`);
        s.setProperty('--split-right-scale',(1 + heroScale + f2.enter*0.7).toFixed(4));

        // Frame two
        s.setProperty('--frame2-opacity', (f2.active*(1-f3.enter)).toFixed(4));
        s.setProperty('--frame2-x',       `calc(-50% + ${mX*9}px)`);
        s.setProperty('--frame2-y',       `calc(${(1-f2.active)*10}% + ${mY*7 - f2.exit*140}px)`);
        s.setProperty('--frame2-scale',   (1.06 + f2.enter*0.07 + f2.exit*0.07).toFixed(4));

        // Stats overlay
        if (statsEl) {
            statsEl.style.setProperty('--stats-opacity', statsEnter.toFixed(4));
            statsEl.style.setProperty('--stats-y',       `${(1-statsEnter)*70}px`);
            statsEl.classList.toggle('ready', statsEnter > 0.5);
        }

        // Analytics overlay
        if (analyticsEl) {
            analyticsEl.style.setProperty('--analytics-opacity', analyEnter.toFixed(4));
            analyticsEl.style.setProperty('--analytics-y',       `${(1-analyEnter)*90}px`);
            analyticsEl.classList.toggle('ready', analyEnter > 0.5);
        }

        // Continue animating
        if (Math.abs(sScroll-tScroll) > 0.08
            || (!rm.matches && (Math.abs(mX-txMX) > 0.001 || Math.abs(mY-txMY) > 0.001)))
            tick();
    }

    function tick() { if (!raf) { raf = true; requestAnimationFrame(update); } }

    // Bind to all possible scroll containers to be bulletproof on all devices
    ['scroll', 'touchmove'].forEach(evt => {
        window.addEventListener(evt, tick, { passive: true });
        const m = document.getElementById('app-main');
        const c = document.getElementById('app-content');
        if(m) m.addEventListener(evt, tick, { passive: true });
        if(c) c.addEventListener(evt, tick, { passive: true });
    });
    
    window.addEventListener('resize', tick);
    window.addEventListener('pointermove', e => {
        txMX = e.clientX/window.innerWidth - 0.5;
        txMY = e.clientY/window.innerHeight - 0.5;
        tick();
    }, { passive: true });
    tick();
})();

/* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
   3D TILT on glass cards
   â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */
(function() {
    const cards = document.querySelectorAll('.js-3d-card');
    cards.forEach(card => {
        card.addEventListener('mousemove', e => {
            const r   = card.getBoundingClientRect();
            const cx  = (e.clientX - r.left - r.width / 2)  / (r.width / 2);
            const cy  = (e.clientY - r.top  - r.height / 2) / (r.height / 2);
            const rx  = -cy * 7;
            const ry  =  cx * 9;
            card.style.transform = `perspective(700px) rotateX(${rx}deg) rotateY(${ry}deg) translateZ(18px) scale(1.015)`;
            const amber = `rgba(200,137,26,${Math.abs(cx)*0.18 + 0.04})`;
            card.style.boxShadow = `
                ${-cx*28}px ${cy*28}px 60px rgba(0,0,0,0.65),
                0 0 50px ${amber},
                inset 0 1px 0 rgba(255,255,255,0.14),
                inset 0 -1px 0 rgba(0,0,0,0.3)`;
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(700px) rotateX(0deg) rotateY(0deg) translateZ(0) scale(1)';
            card.style.boxShadow = '';
        });
    });
})();

@if(isset($pretestResults) && $pretestResults->count() > 0)
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('pretestRadarChart');
    if (!ctx) return;
    
    const labels = {!! json_encode($pretestResults->pluck('topik.nama_topik')) !!};
    const data = {!! json_encode($pretestResults->pluck('skor')) !!};
    
    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pemahaman (%)',
                data: data,
                backgroundColor: 'rgba(139, 92, 246, 0.2)',
                borderColor: 'rgba(139, 92, 246, 1)',
                pointBackgroundColor: 'rgba(200, 137, 26, 1)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(200, 137, 26, 1)',
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                r: {
                    angleLines: { color: 'rgba(255, 255, 255, 0.1)' },
                    grid: { color: 'rgba(255, 255, 255, 0.1)' },
                    pointLabels: {
                        color: 'rgba(255, 255, 255, 0.7)',
                        font: { size: 10, family: "'Plus Jakarta Sans', sans-serif" }
                    },
                    ticks: {
                        display: false, // hide numbers on the axes
                        min: 0,
                        max: 100,
                        stepSize: 20
                    }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
});
@endif
</script>
@endpush

@endsection
