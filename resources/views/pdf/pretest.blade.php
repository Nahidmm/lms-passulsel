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
        .badge-not { color: #b45309; font-weight: bold; }
        .footer { margin-top: 20px; font-size: 9px; color: #9ca3af; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <p class="brand">STRAPSUSPAS &bull; Kanwil Ditjenpas Sulawesi Selatan</p>
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
                <td class="center {{ $row['nilai_pretest'] !== null ? 'badge-done' : 'badge-not' }}">
                    {{ $row['nilai_pretest'] !== null ? 'Sudah' : 'Belum' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p class="footer">Dicetak otomatis dari sistem LMS STRAPSUSPAS &bull; {{ now()->format('d-m-Y H:i') }}</p>
</body>
</html>
