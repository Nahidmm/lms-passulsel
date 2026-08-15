@extends('layouts.app')

@section('title', 'Evaluasi Kompetensi')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-display font-bold text-primary">Evaluasi Kompetensi</h1>
    <p class="text-text-secondary mt-1">Uji pemahaman Anda terhadap materi yang telah dipelajari.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Active / Start Session Card -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-border p-6 md:p-8">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center text-primary">
                    <i data-lucide="clipboard-check" class="w-8 h-8"></i>
                </div>
                <div>
                    <h2 class="text-xl font-display font-bold text-text-primary">Kuis Evaluasi Jabatan</h2>
                    <p class="text-text-secondary">Eselon V - {{ Auth::user()->jabatan->nama_jabatan ?? 'Belum ada jabatan' }}</p>
                </div>
            </div>

            <div class="bg-secondary p-5 rounded-lg border border-border mb-8">
                <h3 class="font-bold text-text-primary mb-3">Informasi Kuis:</h3>
                <ul class="space-y-2 text-sm text-text-secondary">
                    <li class="flex items-center gap-2"><i data-lucide="help-circle" class="w-4 h-4 text-primary"></i> Total Soal: {{ $totalSoalTersedia }} Pilihan Ganda</li>
                    <li class="flex items-center gap-2"><i data-lucide="clock" class="w-4 h-4 text-primary"></i> Durasi: 30 Menit</li>
                    <li class="flex items-center gap-2"><i data-lucide="alert-triangle" class="w-4 h-4 text-warning"></i> Sesi bersifat <strong>Locked Mode</strong> (Tidak bisa copy-paste).</li>
                </ul>
            </div>

            @if($activeSesi)
                <div class="bg-warning/10 border border-warning/30 p-4 rounded-lg mb-6 flex items-start gap-3">
                    <i data-lucide="alert-circle" class="text-warning w-5 h-5 mt-0.5"></i>
                    <div>
                        <p class="font-bold text-warning">Sesi Evaluasi Sedang Berlangsung!</p>
                        <p class="text-sm text-warning/80 mt-1">Anda memiliki kuis yang belum diselesaikan.</p>
                    </div>
                </div>
                <a href="{{ route('peserta.evaluasi.soal', $activeSesi->id) }}" class="block w-full bg-warning hover:bg-warning/90 text-white font-bold py-3 px-4 rounded-lg text-center transition-colors shadow-md">
                    Lanjutkan Kuis
                </a>
            @else
                <form action="{{ route('peserta.evaluasi.start') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-primary hover:bg-primary-hover text-white font-bold py-3 px-4 rounded-lg transition-colors shadow-md text-lg" {{ $totalSoalTersedia == 0 ? 'disabled' : '' }}>
                        {{ $totalSoalTersedia == 0 ? 'Soal Belum Tersedia' : 'Mulai Evaluasi' }}
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- History List -->
    <div>
        <div class="bg-white rounded-xl shadow-sm border border-border overflow-hidden h-full">
            <div class="p-4 border-b border-border bg-secondary/50">
                <h3 class="font-display font-bold text-text-primary flex items-center gap-2">
                    <i data-lucide="history" class="w-5 h-5 text-primary"></i> Riwayat Evaluasi
                </h3>
            </div>
            <div class="p-4 overflow-y-auto max-h-[500px]">
                @forelse($riwayatSesi as $riwayat)
                    <a href="{{ route('peserta.evaluasi.hasil', $riwayat->id) }}" class="block border border-border rounded-lg p-4 mb-3 hover:border-primary transition-colors">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs text-text-secondary">{{ $riwayat->selesai_at->format('d M Y, H:i') }}</span>
                            <span class="text-xs font-bold px-2 py-0.5 rounded bg-success/10 text-success">Selesai</span>
                        </div>
                        <div class="flex justify-between items-end">
                            <div>
                                <p class="text-sm font-medium text-text-secondary">Skor Akhir</p>
                                <p class="text-2xl font-display font-bold {{ $riwayat->skor >= 70 ? 'text-success' : 'text-danger' }}">{{ $riwayat->skor }}</p>
                            </div>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-text-secondary"></i>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-8">
                        <p class="text-text-secondary text-sm">Belum ada riwayat evaluasi.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

@endsection
