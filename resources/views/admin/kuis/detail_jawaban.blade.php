@extends('layouts.app')

@section('title', 'Detail Jawaban – ' . $sesi->user->nama)

@section('content')

<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <a href="{{ route('admin.kuis.peserta', $materi->id) }}" class="text-text-secondary hover:text-primary flex items-center gap-1.5 font-medium transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Daftar Peserta
    </a>
</div>

{{-- Header info peserta --}}
@php
    $lulus    = $sesi->skor >= $passingGrade;
    $durasi   = $sesi->mulai_at && $sesi->selesai_at
                    ? round($sesi->mulai_at->diffInSeconds($sesi->selesai_at) / 60, 1) . ' menit'
                    : '-';
    $essaySoals = $hasilLatihans->filter(fn($h) => $h->soal && in_array($h->soal->tipe, ['essay', 'isian_singkat', 'free_text']));
@endphp

<div class="bg-white rounded-xl border border-border shadow-sm p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center shrink-0">
                <i data-lucide="user" class="w-6 h-6 text-primary"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-text-primary">{{ $sesi->user->nama ?? '-' }}</h1>
                <p class="text-text-secondary text-sm">NIP: {{ $sesi->user->nip ?? '-' }} &bull; {{ $sesi->user->jabatan->nama_jabatan ?? '-' }}</p>
                <p class="text-text-secondary text-sm mt-1">Kuis: <span class="font-semibold text-text-primary">{{ $materi->judul }}</span></p>
            </div>
        </div>
        <div class="flex items-center gap-4 shrink-0">
            <div class="text-center px-4 py-3 bg-secondary/50 rounded-xl">
                <p class="text-xs text-text-secondary font-medium">Benar / Total</p>
                <p class="text-lg font-bold text-text-primary mt-1">{{ $sesi->benar }} / {{ $sesi->total_soal }}</p>
            </div>
            <div class="text-center px-4 py-3 bg-secondary/50 rounded-xl">
                <p class="text-xs text-text-secondary font-medium">Durasi</p>
                <p class="text-lg font-bold text-text-primary mt-1">{{ $durasi }}</p>
            </div>
            <div class="text-center px-5 py-3 rounded-xl {{ $lulus ? 'bg-success/10' : 'bg-danger/10' }}">
                <p class="text-xs font-medium {{ $lulus ? 'text-success' : 'text-danger' }}">Nilai Akhir</p>
                <p class="text-3xl font-bold mt-1 {{ $lulus ? 'text-success' : 'text-danger' }}">{{ number_format($sesi->skor, 1) }}</p>
                <p class="text-xs font-bold {{ $lulus ? 'text-success' : 'text-danger' }}">{{ $lulus ? '✓ LULUS' : '✗ TIDAK LULUS' }}</p>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="bg-success/10 border border-success/20 text-success px-4 py-3 rounded-xl mb-6 flex items-center gap-3 text-sm">
        <i data-lucide="check-circle" class="w-5 h-5 shrink-0"></i>
        <p class="font-medium">{{ session('success') }}</p>
    </div>
@endif

{{-- Form penilaian manual (hanya muncul jika ada soal essay) --}}
@if($essaySoals->isNotEmpty())
<form action="{{ route('admin.kuis.nilai-manual', [$materi->id, $sesi->id]) }}" method="POST">
    @csrf
@endif

{{-- Detail per soal --}}
<div class="space-y-4">
    @foreach($hasilLatihans as $idx => $hasil)
        @php
            $soal        = $hasil->soal;
            $isEssay     = $soal && in_array($soal->tipe, ['essay', 'isian_singkat', 'free_text']);
            $isBenar     = $hasil->is_correct;
            $tipeLabels  = [
                'pilihan_ganda' => ['label' => 'Pilihan Ganda', 'color' => 'bg-blue-100 text-blue-700'],
                'multi_select'  => ['label' => 'Multi Select',  'color' => 'bg-violet-100 text-violet-700'],
                'essay'         => ['label' => 'Essay',          'color' => 'bg-green-100 text-green-700'],
                'isian_singkat' => ['label' => 'Isian Singkat',  'color' => 'bg-yellow-100 text-yellow-700'],
                'menjodohkan'   => ['label' => 'Menjodohkan',    'color' => 'bg-orange-100 text-orange-700'],
            ];
            $tipeInfo = $tipeLabels[$soal->tipe ?? ''] ?? ['label' => ucfirst($soal->tipe ?? '-'), 'color' => 'bg-gray-100 text-gray-700'];
        @endphp
        <div class="bg-white rounded-xl border {{ $isEssay ? 'border-border' : ($isBenar ? 'border-success/30' : 'border-danger/30') }} shadow-sm overflow-hidden">
            {{-- Soal header --}}
            <div class="px-5 py-4 border-b border-border flex items-start justify-between gap-4 {{ $isEssay ? 'bg-secondary/20' : ($isBenar ? 'bg-success/5' : 'bg-danger/5') }}">
                <div class="flex items-start gap-3 flex-1 min-w-0">
                    <span class="w-7 h-7 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center shrink-0 mt-0.5">{{ $idx + 1 }}</span>
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $tipeInfo['color'] }}">{{ $tipeInfo['label'] }}</span>
                            <span class="text-xs text-text-secondary">Bobot: {{ $soal->bobot ?? 1 }} poin</span>
                        </div>
                        <p class="text-sm font-semibold text-text-primary leading-relaxed">{{ $soal->pertanyaan ?? '-' }}</p>
                    </div>
                </div>
                <div class="shrink-0 text-right">
                    @if(!$isEssay)
                        <div class="text-2xl font-bold {{ $isBenar ? 'text-success' : 'text-danger' }}">
                            {{ $isBenar ? '+' : '0' }}{{ $hasil->skor }}
                        </div>
                        <div class="text-xs {{ $isBenar ? 'text-success' : 'text-danger' }} font-bold mt-0.5">
                            {{ $isBenar ? '✓ Benar' : '✗ Salah' }}
                        </div>
                    @else
                        <div class="text-lg font-bold text-accent">{{ $hasil->skor }} / {{ $soal->bobot }}</div>
                        <div class="text-xs text-text-secondary mt-0.5">Poin didapat</div>
                    @endif
                </div>
            </div>

            {{-- Body: pilihan jawaban atau jawaban teks --}}
            <div class="px-5 py-4">
                @if($soal->tipe === 'pilihan_ganda' || $soal->tipe === 'multi_select')
                    <div class="space-y-2">
                        @foreach($soal->pilihanJawaban as $pilihan)
                            @php
                                $dipilih   = $hasil->pilihan_id == $pilihan->id;
                                $benarPilihan = $pilihan->is_correct;
                            @endphp
                            <div class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm
                                {{ $benarPilihan ? 'bg-success/10 border border-success/30' : '' }}
                                {{ $dipilih && !$benarPilihan ? 'bg-danger/10 border border-danger/30' : '' }}
                                {{ !$dipilih && !$benarPilihan ? 'bg-secondary/30 border border-border' : '' }}">
                                <span class="w-6 h-6 rounded-full text-xs font-bold flex items-center justify-center shrink-0
                                    {{ $benarPilihan ? 'bg-success text-white' : ($dipilih ? 'bg-danger text-white' : 'bg-secondary text-text-secondary') }}">
                                    {{ $pilihan->label ?? chr(64 + $loop->iteration) }}
                                </span>
                                <span class="{{ $benarPilihan ? 'text-success font-semibold' : ($dipilih ? 'text-danger' : 'text-text-secondary') }}">
                                    {{ $pilihan->teks }}
                                </span>
                                @if($dipilih)
                                    <span class="ml-auto text-xs font-bold {{ $benarPilihan ? 'text-success' : 'text-danger' }}">
                                        {{ $benarPilihan ? '✓ Dipilih (Benar)' : '✗ Dipilih (Salah)' }}
                                    </span>
                                @elseif($benarPilihan)
                                    <span class="ml-auto text-xs font-bold text-success">✓ Jawaban Benar</span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                @elseif($soal->tipe === 'menjodohkan')
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-xs font-bold text-text-secondary mb-2 uppercase tracking-wider">Kolom Kiri</p>
                            @foreach($soal->pilihanJawaban as $p)
                                @php $parts = explode('|||', $p->teks); @endphp
                                <div class="px-3 py-2 bg-secondary/50 rounded-lg text-sm text-text-primary mb-1.5">{{ $parts[0] ?? '-' }}</div>
                            @endforeach
                        </div>
                        <div>
                            <p class="text-xs font-bold text-text-secondary mb-2 uppercase tracking-wider">Kolom Kanan (Kunci)</p>
                            @foreach($soal->pilihanJawaban as $p)
                                @php $parts = explode('|||', $p->teks); @endphp
                                <div class="px-3 py-2 bg-success/10 border border-success/20 rounded-lg text-sm text-success font-medium mb-1.5">{{ $parts[1] ?? '-' }}</div>
                            @endforeach
                        </div>
                    </div>
                    @if($hasil->jawaban_esai)
                        <div class="mt-3 p-3 bg-secondary/30 rounded-lg">
                            <p class="text-xs font-bold text-text-secondary mb-1">Jawaban Peserta:</p>
                            <p class="text-sm text-text-primary">{{ $hasil->jawaban_esai }}</p>
                        </div>
                    @endif

                @elseif($isEssay)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-bold text-text-secondary uppercase tracking-wider mb-2">Jawaban Peserta</p>
                            <div class="p-3 bg-secondary/30 rounded-lg min-h-[80px] text-sm text-text-primary">
                                {{ $hasil->jawaban_esai ?: '(Tidak menjawab)' }}
                            </div>
                        </div>
                        @if($soal->pilihanJawaban->isNotEmpty())
                        <div>
                            <p class="text-xs font-bold text-text-secondary uppercase tracking-wider mb-2">Kunci / Contoh Jawaban</p>
                            <div class="p-3 bg-success/10 border border-success/20 rounded-lg min-h-[80px] text-sm text-success">
                                {{ $soal->pilihanJawaban->first()->teks ?? '-' }}
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Input nilai manual admin --}}
                    <div class="mt-4 p-4 bg-accent/5 border border-accent/20 rounded-xl">
                        <p class="text-sm font-bold text-accent mb-3 flex items-center gap-2">
                            <i data-lucide="pencil" class="w-4 h-4"></i> Penilaian Manual Admin
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-text-primary mb-1.5">Nilai (0 – {{ $soal->bobot }})</label>
                                <input type="number" name="penilaian[{{ $hasil->id }}][skor]"
                                    value="{{ $hasil->skor }}" min="0" max="{{ $soal->bobot }}" step="0.5"
                                    class="w-full px-4 py-2.5 border border-border rounded-lg bg-white focus:ring-2 focus:ring-accent focus:border-accent outline-none text-center text-lg font-bold">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-text-primary mb-1.5">Catatan Admin (opsional)</label>
                                <input type="text" name="penilaian[{{ $hasil->id }}][catatan]"
                                    value="{{ $hasil->catatan_admin }}" placeholder="Misal: Jawaban kurang lengkap"
                                    class="w-full px-4 py-2.5 border border-border rounded-lg bg-white focus:ring-2 focus:ring-accent focus:border-accent outline-none text-sm">
                            </div>
                        </div>
                        @if($hasil->catatan_admin)
                            <p class="text-xs text-text-secondary mt-2">Catatan sebelumnya: <em>{{ $hasil->catatan_admin }}</em></p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>

@if($essaySoals->isNotEmpty())
    <div class="mt-6 flex justify-end">
        <button type="submit" class="bg-accent hover:bg-accent-hover text-white font-bold py-3 px-8 rounded-xl transition-all shadow-sm hover:shadow-md flex items-center gap-2">
            <i data-lucide="save" class="w-5 h-5"></i> Simpan Penilaian Manual
        </button>
    </div>
</form>
@endif

@endsection
