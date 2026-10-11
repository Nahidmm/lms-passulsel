<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; margin: 0; }
        .header { text-align: center; margin-bottom: 16px; }
        .header h1 { font-size: 18px; margin: 0 0 4px; }
        .header .brand { font-size: 11px; color: #4b5563; margin: 0 0 2px; }
        .header .meta { font-size: 10px; color: #6b7280; margin: 0; }

        .summary { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .summary td { border: 1px solid #d1d5db; padding: 8px 10px; text-align: center; font-size: 11px; }
        .summary .label { background: #f3f4f6; font-weight: bold; font-size: 9px; text-transform: uppercase; color: #4b5563; }
        .summary .value { font-size: 16px; font-weight: bold; }

        .question { margin-bottom: 18px; padding-bottom: 14px; border-bottom: 1px solid #e5e7eb; }
        .q-head { margin-bottom: 6px; }
        .q-no { font-weight: bold; font-size: 11px; color: #374151; }
        .badge { font-size: 9px; font-weight: bold; padding: 2px 8px; border-radius: 10px; }
        .badge-benar { background: #d1fae5; color: #047857; }
        .badge-salah { background: #ffe4e6; color: #be123c; }
        .q-text { font-weight: bold; margin: 6px 0 8px; }

        .choice { padding: 6px 9px; margin-bottom: 4px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 11px; }
        .choice.correct { background: #d1fae5; border-color: #6ee7b7; }
        .choice.wrong-user { background: #ffe4e6; border-color: #fda4af; }
        .tag { font-size: 9px; font-weight: bold; padding: 1px 7px; border-radius: 8px; margin-left: 6px; }
        .tag-kunci { background: #059669; color: #fff; }
        .tag-user { background: #e11d48; color: #fff; }

        .catatan { background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 6px; padding: 8px 10px; margin-top: 6px; font-size: 11px; }
        .catatan .lbl { font-weight: bold; color: #4338ca; font-size: 9px; text-transform: uppercase; }
        .footer { margin-top: 24px; font-size: 9px; color: #9ca3af; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <p class="brand">STRAPSUS PAS &bull; Kanwil Ditjenpas Sulawesi Selatan</p>
        <h1>Detail Hasil Pretest</h1>
        <p class="meta">{{ $user->nama }} &bull; NIP: {{ $user->nip }} &bull; {{ $user->unitKerja?->nama_unit ?? '-' }} &bull; Dicetak: {{ now()->format('d M Y H:i') }}</p>
    </div>

    @if(!$sesi)
        <p style="text-align:center; color:#b45309;">Peserta belum mengerjakan pretest.</p>
    @else
        <table class="summary">
            <tr>
                <td class="label">Nilai Pretest</td>
                <td class="label">Jawaban Benar</td>
                <td class="label">Selesai</td>
                <td class="label">Status</td>
            </tr>
            <tr>
                <td class="value">{{ number_format((float)$sesi->skor, 1) }}</td>
                <td class="value">{{ $sesi->benar }}/{{ $sesi->total_soal }}</td>
                <td class="value" style="font-size:11px;">{{ $sesi->selesai_at ? $sesi->selesai_at->format('d M Y, H:i') : '-' }}</td>
                <td class="value" style="font-size:12px;">{{ $sesi->skor >= ($pretest->passing_grade ?? 70) ? 'Lulus' : 'Belum Lulus' }}</td>
            </tr>
        </table>

        @foreach($hasilLatihans as $index => $hasil)
        <div class="question">
            <div class="q-head">
                <span class="q-no">Soal No. {{ $index + 1 }}</span>
                <span class="badge {{ $hasil->is_correct ? 'badge-benar' : 'badge-salah' }}">{{ $hasil->is_correct ? 'BENAR' : 'SALAH' }}</span>
                <span style="font-size:9px;color:#6b7280;">&nbsp;({{ strtoupper(str_replace('_', ' ', $hasil->soal->tipe)) }})</span>
            </div>
            <div class="q-text">{{ $hasil->soal->pertanyaan }}</div>

            @if(in_array($hasil->soal->tipe, ['pilihan_ganda', 'multi_select', 'jenis_lainnya']))
                @foreach($hasil->soal->pilihanJawaban as $pilihan)
                    @php
                        $isUserChoice    = $hasil->pilihan_id == $pilihan->id;
                        $isCorrectChoice = $pilihan->is_correct;
                        $cls = $isCorrectChoice ? 'correct' : ($isUserChoice ? 'wrong-user' : '');
                    @endphp
                    <div class="choice {{ $cls }}">
                        <strong>{{ $pilihan->huruf }}.</strong> {{ $pilihan->teks }}
                        @if($isCorrectChoice)<span class="tag tag-kunci">Kunci</span>@endif
                        @if($isUserChoice && !$isCorrectChoice)<span class="tag tag-user">Jawaban Peserta</span>@endif
                        @if($isUserChoice && $isCorrectChoice)<span class="tag tag-kunci">Jawaban Peserta (Benar)</span>@endif
                    </div>
                @endforeach
            @elseif(in_array($hasil->soal->tipe, ['isian_singkat', 'essay']))
                <div class="choice">
                    <strong>Jawaban Peserta:</strong> {{ $hasil->jawaban_esai ?: '(Tidak menjawab)' }}
                </div>
            @endif

            @if($hasil->catatan)
                <div class="catatan">
                    <span class="lbl">Catatan / Alasan Peserta:</span> {{ $hasil->catatan }}
                </div>
            @endif
        </div>
        @endforeach
    @endif

    <p class="footer">Dicetak otomatis dari sistem LMS STRAPSUS PAS &bull; {{ now()->format('d-m-Y H:i') }}</p>
</body>
</html>
