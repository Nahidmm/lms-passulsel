<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Lengkap Buku Nilai - {{ $isAll ? 'Seluruh Pelatihan' : $pelatihan->judul }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background-color: #f1f5f9;
            color: #0f172a;
            padding: 24px;
            font-size: 11pt;
            line-height: 1.4;
        }

        .no-print-bar {
            max-width: 1050px;
            margin: 0 auto 20px auto;
            background: #ffffff;
            padding: 12px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: #2563eb;
            color: #ffffff;
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
        }

        .btn-secondary {
            background-color: #e2e8f0;
            color: #334155;
        }

        .btn-secondary:hover {
            background-color: #cbd5e1;
        }

        .sheet {
            max-width: 1050px;
            margin: 0 auto;
            background: #ffffff;
            padding: 32px 36px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            border-radius: 4px;
        }

        /* Kop Surat */
        .kop {
            display: flex;
            align-items: center;
            gap: 20px;
            padding-bottom: 14px;
            border-bottom: 3px double #000000;
            margin-bottom: 20px;
        }

        .kop-logo {
            width: 85px;
            height: 85px;
            object-fit: contain;
        }

        .kop-text {
            flex: 1;
            text-align: center;
        }

        .kop-text h2 {
            font-size: 14pt;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #000;
        }

        .kop-text h3 {
            font-size: 12pt;
            font-weight: 700;
            text-transform: uppercase;
            margin-top: 2px;
            color: #111;
        }

        .kop-text p {
            font-size: 9pt;
            margin-top: 4px;
            color: #333;
        }

        /* Title */
        .doc-title {
            text-align: center;
            margin-bottom: 20px;
        }

        .doc-title h1 {
            font-size: 13pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: underline;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 16px;
            font-size: 9.5pt;
        }

        .meta-table td {
            padding: 2px 4px;
        }

        /* Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 20px;
        }

        .data-table th, .data-table td {
            border: 1px solid #334155;
            padding: 6px 8px;
        }

        .data-table th {
            background-color: #f1f5f9;
            font-weight: 700;
            text-align: center;
            text-transform: uppercase;
            font-size: 8.5pt;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: 700; }

        /* Stats Box */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 24px;
            padding: 12px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-label {
            font-size: 8pt;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 600;
        }

        .stat-value {
            font-size: 13pt;
            font-weight: 800;
            color: #0f172a;
            margin-top: 2px;
        }

        /* Signature */
        .ttd-container {
            display: flex;
            justify-content: space-between;
            margin-top: 36px;
            font-size: 10pt;
            page-break-inside: avoid;
        }

        .ttd-box {
            width: 320px;
            text-align: center;
        }

        .ttd-space {
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ttd-space img {
            max-height: 65px;
            object-fit: contain;
        }

        /* Print Specifics */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }

            .no-print-bar {
                display: none !important;
            }

            .sheet {
                box-shadow: none;
                padding: 0;
                margin: 0;
                max-width: 100%;
            }

            .data-table th {
                background-color: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    {{-- Top Action Bar (Hidden on print) --}}
    <div class="no-print-bar">
        <div>
            <strong style="font-size: 14px;">Preview Cetak Buku Nilai (Laporan Resmi)</strong>
            <p style="font-size: 11px; color: #64748b;">Gunakan dialog print untuk mencetak langsung ke printer atau simpan sebagai PDF.</p>
        </div>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" class="btn btn-primary">
                🖨️ Cetak Dokumen / Simpan PDF
            </button>
            <button onclick="window.close()" class="btn btn-secondary">
                ✕ Tutup
            </button>
        </div>
    </div>

    {{-- Printable Paper --}}
    <div class="sheet">
        {{-- Kop Surat Resmi --}}
        <div class="kop">
            @if($setting && $setting->logo_instansi)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($setting->logo_instansi) }}" alt="Logo" class="kop-logo">
            @else
                <img src="{{ asset('img/logo-kemenkumham.png') }}" onerror="this.src='{{ asset('img/logo.png') }}'; this.onerror=null;" alt="Logo" class="kop-logo">
            @endif
            <div class="kop-text">
                <h2>Kementerian Hukum dan Hak Asasi Manusia RI</h2>
                <h3>Kantor Wilayah Sulawesi Selatan</h3>
                <p>Lembaga Manajemen Pelatihan &amp; Kompetensi Pegawai Pemasyarakatan (LMS STRAPSUSPAS)</p>
                <p>Jl. Sultan Alauddin No. 102, Kota Makassar, Sulawesi Selatan &bull; Portal: passulsel.kemenkumham.go.id</p>
            </div>
        </div>

        {{-- Judul Dokumen --}}
        <div class="doc-title">
            <h1>Laporan Lengkap Buku Nilai &amp; Rekapitulasi Prestasi Peserta</h1>
        </div>

        {{-- Informasi Metadata --}}
        <table class="meta-table">
            <tr>
                <td style="width: 180px; font-weight: bold;">Program Pelatihan</td>
                <td style="width: 10px;">:</td>
                <td><strong>{{ $isAll ? 'Seluruh Pelatihan (Komposit Program LMS)' : $pelatihan->judul }}</strong></td>
                <td style="width: 140px; font-weight: bold;">Tanggal Cetak</td>
                <td style="width: 10px;">:</td>
                <td>{{ now()->translatedFormat('d F Y, H:i') }} WITA</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Unit Kerja / Satker</td>
                <td>:</td>
                <td>{{ $unitKerjaFilter ? $unitKerjaFilter->nama : 'Semua Unit Kerja' }}</td>
                <td style="font-weight: bold;">Dicetak Oleh</td>
                <td>:</td>
                <td>{{ auth()->user()->nama ?? 'Administrator LMS' }}</td>
            </tr>
        </table>

        @if($isAll)
            {{-- MODE SELURUH PELATIHAN --}}
            @php
                $totalPeserta = count($reportData);
                $totalCerts = collect($reportData)->sum('totalCerts');
                $avgScores = collect($reportData)->pluck('avgScore')->filter(fn($v) => $v > 0);
                $rataGlobal = $avgScores->isNotEmpty() ? $avgScores->avg() : 0;
                $maxNilai = $avgScores->isNotEmpty() ? $avgScores->max() : 0;
            @endphp

            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-label">Total Peserta</div>
                    <div class="stat-value">{{ $totalPeserta }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Total Program Pelatihan</div>
                    <div class="stat-value">{{ $allPelatihans->count() }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Rata-rata Nilai Komposit</div>
                    <div class="stat-value">{{ $rataGlobal > 0 ? number_format($rataGlobal, 1) : '-' }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Total Sertifikat Terbit</div>
                    <div class="stat-value">{{ $totalCerts }}</div>
                </div>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 35px;">No</th>
                        <th>Nama Peserta</th>
                        <th style="width: 120px;">NIP</th>
                        <th>Unit Kerja</th>
                        <th style="width: 90px;">Pelatihan Selesai</th>
                        <th style="width: 80px;">Rata-rata Nilai</th>
                        <th style="width: 90px;">Predikat</th>
                        <th style="width: 80px;">Sertifikat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $idx => $item)
                        @php
                            $u = $item['user'];
                            $avg = $item['avgScore'];
                            $predikat = match(true) {
                                $avg >= 85 => 'Sangat Baik',
                                $avg >= 70 => 'Baik',
                                $avg >= 55 => 'Cukup',
                                $avg > 0   => 'Kurang',
                                default    => 'Belum Ada',
                            };
                        @endphp
                        <tr>
                            <td class="text-center">{{ $idx + 1 }}</td>
                            <td><strong>{{ $u->nama }}</strong></td>
                            <td class="text-center">{{ $u->nip }}</td>
                            <td>{{ $u->unitKerja->nama ?? '-' }}</td>
                            <td class="text-center font-bold">{{ $item['totalCompleted'] }} / {{ $item['totalEnrolled'] }}</td>
                            <td class="text-center font-bold">{{ $avg > 0 ? number_format($avg, 1) : '-' }}</td>
                            <td class="text-center">{{ $predikat }}</td>
                            <td class="text-center">{{ $item['totalCerts'] > 0 ? $item['totalCerts'] . ' Terbit' : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center" style="padding: 20px;">Tidak ada data peserta ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        @else
            {{-- MODE SATU PELATIHAN LENGKAP --}}
            @php
                $totalPeserta = count($gradebookData);
                $lulusCount = collect($gradebookData)->filter(fn($i) => ($i['rekap']->status_kelulusan ?? '') === 'lulus')->count();
                $allFinalScores = collect($gradebookData)->map(fn($i) => $i['rekap']->nilai_akhir ?? 0)->filter(fn($v) => $v > 0);
                $rataNilai = $allFinalScores->isNotEmpty() ? $allFinalScores->avg() : 0;
                $maxNilai = $allFinalScores->isNotEmpty() ? $allFinalScores->max() : 0;
                $minNilai = $allFinalScores->isNotEmpty() ? $allFinalScores->min() : 0;
            @endphp

            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-label">Total Peserta</div>
                    <div class="stat-value">{{ $totalPeserta }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Jumlah Lulus (PG {{ $bobot->passing_grade }})</div>
                    <div class="stat-value">{{ $lulusCount }} <span style="font-size: 9pt; font-weight: normal;">({{ $totalPeserta > 0 ? round(($lulusCount / $totalPeserta) * 100) : 0 }}%)</span></div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Rata-rata Nilai Akhir</div>
                    <div class="stat-value">{{ $rataNilai > 0 ? number_format($rataNilai, 1) : '-' }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Nilai Tertinggi / Terendah</div>
                    <div class="stat-value">{{ number_format($maxNilai, 1) }} / {{ number_format($minNilai, 1) }}</div>
                </div>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th>Nama Peserta</th>
                        <th style="width: 110px;">NIP</th>
                        <th>Unit Kerja</th>
                        <th style="width: 55px;">Pretest<br>({{ $bobot->bobot_pretest }}%)</th>
                        <th style="width: 55px;">Kuis<br>({{ $bobot->bobot_quiz }}%)</th>
                        <th style="width: 55px;">Tugas<br>({{ $bobot->bobot_tugas }}%)</th>
                        <th style="width: 55px;">Posttest<br>({{ $bobot->bobot_posttest }}%)</th>
                        <th style="width: 55px;">Tim</th>
                        <th style="width: 65px;">Nilai Akhir</th>
                        <th style="width: 75px;">Predikat</th>
                        <th style="width: 70px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gradebookData as $idx => $item)
                        @php
                            $u = $item['user'];
                            $r = $item['rekap'];
                            $isLulus = ($r->status_kelulusan ?? '') === 'lulus';
                        @endphp
                        <tr>
                            <td class="text-center">{{ $idx + 1 }}</td>
                            <td><strong>{{ $u->nama }}</strong></td>
                            <td class="text-center font-mono">{{ $u->nip }}</td>
                            <td>{{ $u->unitKerja->nama ?? '-' }}</td>
                            <td class="text-center">{{ $r && $r->nilai_pretest !== null ? number_format($r->nilai_pretest, 1) : '-' }}</td>
                            <td class="text-center">{{ $r && $r->nilai_quiz !== null ? number_format($r->nilai_quiz, 1) : '-' }}</td>
                            <td class="text-center">{{ $r && $r->nilai_tugas !== null ? number_format($r->nilai_tugas, 1) : '-' }}</td>
                            <td class="text-center">{{ $r && $r->nilai_posttest !== null ? number_format($r->nilai_posttest, 1) : '-' }}</td>
                            <td class="text-center">{{ $r && $r->nilai_tim !== null ? number_format($r->nilai_tim, 1) : '-' }}</td>
                            <td class="text-center font-bold" style="background-color: #f8fafc;">
                                {{ $r && $r->nilai_akhir !== null ? number_format($r->nilai_akhir, 1) : '-' }}
                            </td>
                            <td class="text-center">{{ $r->predikat ?? '-' }}</td>
                            <td class="text-center font-bold" style="color: {{ $isLulus ? '#166534' : '#991b1b' }};">
                                {{ $isLulus ? 'LULUS' : 'BELUM' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center" style="padding: 20px;">Tidak ada data peserta untuk kursus ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif

        {{-- Lembar Tanda Tangan Pengesahan --}}
        <div class="ttd-container">
            <div class="ttd-box">
                <p>Mengetahui,</p>
                <p><strong>{{ $setting->jabatan_penandatangan ?? 'Kepala Kantor Wilayah' }}</strong></p>
                <div class="ttd-space">
                    @if($setting && $setting->ttd_image)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($setting->ttd_image) }}" alt="TTD">
                    @endif
                </div>
                <p style="text-decoration: underline; font-weight: bold;">{{ $setting->nama_penandatangan ?? 'Dr. H. Fulan, S.H., M.H.' }}</p>
                <p style="font-size: 9pt; color: #475569;">NIP. 19750817 200003 1 001</p>
            </div>

            <div class="ttd-box">
                <p>{{ $setting->tempat_tanda_tangan ?? 'Makassar' }}, {{ now()->translatedFormat('d F Y') }}</p>
                <p><strong>Pengelola &amp; Verifikator LMS</strong></p>
                <div class="ttd-space"></div>
                <p style="text-decoration: underline; font-weight: bold;">{{ auth()->user()->nama ?? 'Administrator LMS' }}</p>
                <p style="font-size: 9pt; color: #475569;">NIP. {{ auth()->user()->nip ?? '-' }}</p>
            </div>
        </div>
    </div>

</body>
</html>

