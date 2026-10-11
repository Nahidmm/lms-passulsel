<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; margin: 0; }
        .header { text-align: center; margin-bottom: 16px; }
        .header h1 { font-size: 18px; margin: 0 0 4px; }
        .header .brand { font-size: 10px; color: #4b5563; margin: 0 0 2px; }
        .header .meta { font-size: 9px; color: #6b7280; margin: 0; }

        .pelatihan { margin-bottom: 14px; }
        .pelatihan > h2 { font-size: 13px; margin: 0 0 6px; color: #111827; }
        .tugas { margin-bottom: 10px; }
        .tugas > h3 { font-size: 11px; margin: 0 0 4px; color: #374151; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 5px 7px; text-align: left; font-size: 10px; }
        th { background: #f3f4f6; text-transform: uppercase; font-size: 9px; }
        .center { text-align: center; }
        .footer { margin-top: 24px; font-size: 9px; color: #9ca3af; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <p class="brand">STRAPSUS PAS &bull; Kanwil Ditjenpas Sulawesi Selatan</p>
        <h1>Arsip Berkas Peserta</h1>
        <p class="meta">Dicetak: {{ now()->format('d M Y H:i') }}</p>
    </div>

    @foreach($pelatihans as $pelatihan)
    <div class="pelatihan">
        <h2>{{ $pelatihan->judul }}</h2>
        @foreach($pelatihan->tugas as $tugas)
            @if($tugas->submissions->isNotEmpty())
            <div class="tugas">
                <h3>{{ $tugas->judul }}</h3>
                <table>
                    <thead>
                        <tr>
                            <th class="center" style="width:24px">No</th>
                            <th>Peserta</th>
                            <th>NIP</th>
                            <th>Unit Kerja</th>
                            <th>File</th>
                            <th class="center">Dikumpulkan</th>
                            <th class="center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tugas->submissions as $i => $sub)
                        <tr>
                            <td class="center">{{ $i + 1 }}</td>
                            <td>{{ $sub->user->nama ?? '-' }}</td>
                            <td>{{ $sub->user->nip ?? '-' }}</td>
                            <td>{{ $sub->user->unitKerja?->nama_unit ?? '-' }}</td>
                            <td>{{ $sub->file_nama_asli ?? '-' }}</td>
                            <td class="center">{{ $sub->created_at->format('d M Y H:i') }}</td>
                            <td class="center">
                                @if($sub->status === 'graded') Dinilai
                                @elseif($sub->status === 'need_revision') Perlu Revisi
                                @else Menunggu Review
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        @endforeach
    </div>
    @endforeach

    <p class="footer">Dicetak otomatis dari sistem LMS STRAPSUS PAS &bull; {{ now()->format('d-m-Y H:i') }}</p>
</body>
</html>
