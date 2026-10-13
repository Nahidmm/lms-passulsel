<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; margin: 0; }
        .header { text-align: center; margin-bottom: 18px; }
        .header h1 { font-size: 18px; margin: 0 0 4px; }
        .header .brand { font-size: 11px; color: #4b5563; margin: 0 0 2px; }
        .header .meta { font-size: 10px; color: #6b7280; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #d1d5db; padding: 7px 9px; text-align: left; font-size: 11px; }
        th { background: #f3f4f6; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        .center { text-align: center; }
        .badge-done { color: #047857; font-weight: bold; }
        .badge-active { color: #1d4ed8; font-weight: bold; }
        .badge-pause { color: #b45309; font-weight: bold; }
        .badge-not { color: #6b7280; }
        .footer { margin-top: 20px; font-size: 9px; color: #9ca3af; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <p class="brand">STRAPSUS PAS &bull; Kanwil Ditjenpas Sulawesi Selatan</p>
        <h1>Rekap Hasil Pretest Peserta</h1>
        <p class="meta">{{ $pretest ? $pretest->judul : 'Pretest' }} &bull; Dicetak: {{ now()->format('d M Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="center" style="width:30px">No</th>
                <th>Nama</th>
                <th>NIP</th>
                <th>Unit Kerja</th>
                <th class="center" style="width:90px">Nilai Pretest</th>
                <th class="center" style="width:80px">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $index => $row)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td>{{ $row['user']->nama }}</td>
                <td>{{ $row['user']->nip }}</td>
                <td>{{ $row['user']->unitKerja?->nama_unit ?? '-' }}</td>
                <td class="center">{{ $row['nilai_pretest'] !== null ? number_format($row['nilai_pretest'], 1) : '-' }}</td>
                <td class="center {{ ($row['status_ujian'] ?? '') === 'selesai' ? 'badge-done' : (($row['status_ujian'] ?? '') === 'sedang_mengerjakan' ? 'badge-active' : (($row['status_ujian'] ?? '') === 'dijeda' ? 'badge-pause' : 'badge-not')) }}">
                    @if(($row['status_ujian'] ?? '') === 'selesai')
                        Selesai
                    @elseif(($row['status_ujian'] ?? '') === 'sedang_mengerjakan')
                        Mengerjakan
                    @elseif(($row['status_ujian'] ?? '') === 'dijeda')
                        Dijeda
                    @else
                        Belum
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p class="footer">Dicetak otomatis dari sistem LMS STRAPSUS PAS &bull; {{ now()->format('d-m-Y H:i') }}</p>
</body>
</html>
