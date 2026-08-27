<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sertifikat {{ $sertifikat->credential_id }}</title>
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            background-color: #ffffff;
            color: #333333;
        }
        
        /* Subtle Spirograph Watermark */
        .watermark {
            position: absolute;
            top: -10%; left: -10%; right: -10%; bottom: -10%;
            background-image: url('data:image/svg+xml;utf8,<svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg"><g stroke="%23F3F4F6" stroke-width="0.5" fill="none"><circle cx="100" cy="100" r="80"/><circle cx="100" cy="100" r="70"/><circle cx="100" cy="100" r="60"/><circle cx="100" cy="100" r="50"/><path d="M 20 100 Q 100 20 180 100 Q 100 180 20 100 Z" /><path d="M 100 20 Q 180 100 100 180 Q 20 100 100 20 Z" /></g></svg>');
            background-size: 800px;
            background-position: center;
            opacity: 0.8;
            z-index: 0;
        }

        /* Elegant Double Border */
        .border-outer {
            position: absolute;
            top: 20px; left: 20px; right: 20px; bottom: 20px;
            border: 1px solid #d1d5db;
            z-index: 1;
        }
        .border-inner {
            position: absolute;
            top: 24px; left: 24px; right: 24px; bottom: 24px;
            border: 3px solid #e5e7eb;
            z-index: 1;
        }

        /* Main Content Container */
        .content {
            position: absolute;
            top: 50px; left: 60px; right: 280px; bottom: 50px;
            z-index: 10;
        }

        /* Header Logo */
        .header-logo {
            height: 60px;
            margin-bottom: 20px;
        }
        
        .date {
            font-size: 11px;
            color: #6b7280;
            font-weight: bold;
            margin-bottom: 20px;
            letter-spacing: 1px;
        }

        /* Name */
        .recipient-name {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-size: 38px;
            color: #111827;
            margin-bottom: 15px;
        }

        .text-normal {
            font-size: 13px;
            color: #4b5563;
            margin-bottom: 10px;
        }

        /* Course Title */
        .course-title {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-size: 24px;
            color: #111827;
            margin-bottom: 10px;
        }
        
        .points {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 30px;
        }

        /* Table inside layout */
        .details {
            width: 90%;
            margin-bottom: 30px;
        }
        .details table {
            width: 100%;
            border-collapse: collapse;
        }
        .details th, .details td {
            padding: 6px 0;
            border-bottom: 1px solid #e5e7eb;
            font-size: 12px;
            color: #4b5563;
        }
        .details th {
            text-transform: uppercase;
            font-size: 10px;
            color: #9ca3af;
            text-align: left;
            border-bottom: 2px solid #e5e7eb;
        }
        
        /* Signature Area */
                .signature-area {
            position: absolute;
            bottom: 50px;
            left: 60px;
            z-index: 10;
        }
        .signature-img {
            height: 50px;
            display: block;
            margin-bottom: 5px;
        }
        .signature-line {
            width: 200px;
            border-bottom: 1px dotted #9ca3af;
            margin-bottom: 5px;
        }
        .signature-name {
            font-size: 11px;
            font-weight: bold;
            color: #374151;
        }
        .signature-title {
            font-size: 10px;
            color: #6b7280;
        }

        /* Right Side Ribbon */
        .ribbon-container {
            position: absolute;
            top: 24px;
            right: 80px;
            width: 180px;
            bottom: 240px;
            background-color: #f3f4f6;
            border-left: 1px solid #e5e7eb;
            border-right: 1px solid #e5e7eb;
            z-index: 5;
            text-align: center;
        }
        .ribbon-container:after {
            content: "";
            position: absolute;
            bottom: -89px;
            left: -1px;
            border-top: 90px solid #f3f4f6;
            border-left: 90px solid transparent;
            border-right: 90px solid transparent;
        }
        .ribbon-text {
            margin-top: 50px;
            font-family: 'Georgia', 'Times New Roman', serif;
            font-size: 20px;
            color: #111827;
            letter-spacing: 2px;
            line-height: 1.4;
        }

        /* Right Side Stamp/Badge */
        .badge {
            position: absolute;
            top: 250px;
            right: 90px;
            width: 160px;
            height: 160px;
            z-index: 10;
            opacity: 0.9;
            text-align: center;
        }

        /* Verify Area Bottom Right */
        .verify-area {
            position: absolute;
            bottom: 35px;
            right: 40px;
            width: 260px;
            text-align: center;
            z-index: 10;
        }
        .qr-code {
            margin-bottom: 10px;
        }
        .verify-text {
            font-size: 9px;
            color: #6b7280;
            line-height: 1.4;
        }
        .verify-url {
            color: #374151;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="watermark"></div>
    <div class="border-outer"></div>
    <div class="border-inner"></div>

    <!-- Right Ribbon -->
    <div class="ribbon-container">
        <div class="ribbon-text">SERTIFIKAT<br>KELULUSAN</div>
    </div>
    
    <!-- Badge SVG -->
        <div class="badge">
        @if(isset($setting) && $setting->logo_instansi)
            <img src="{{ public_path('storage/'.$setting->logo_instansi) }}" style="max-width: 140px; max-height: 140px;">
        @else
            <div style="width:120px; height:120px; border:3px solid #9ca3af; border-radius:60px; text-align:center; padding-top:45px; font-weight:bold; color:#9ca3af; font-family:Arial; font-size:12px; margin-top:10px;">
                SPEKTRA<br>PAS SULSEL
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <div class="content">
        @if(file_exists(public_path('logo/logo.png')))
            <img src="{{ public_path('logo/logo.png') }}" class="header-logo" style="height: 60px; margin-bottom: 10px;">
        @else
            <!-- Fallback Logo Box -->
            <div style="height:40px; margin-bottom:10px; font-family:Georgia; font-size:24px; font-weight:bold; color:#1e3a8a; letter-spacing:2px;">
                SPEKTRA
            </div>
        @endif

        <div style="font-family: 'Courier New', Courier, monospace; font-size: 10px; color: #6b7280; margin-bottom: 20px; letter-spacing: 1px;">
            ID: {{ $sertifikat->credential_id }}
        </div>

        <div class="date">{{ $sertifikat->issued_at->format('d/m/Y') }}</div>

        <div class="recipient-name">{{ $sertifikat->user->nama }}</div>

        <div class="text-normal">telah berhasil menyelesaikan pelatihan</div>

        <div class="course-title">{{ $sertifikat->pelatihan->judul }}</div>

        <div class="points">
            pelatihan bersertifikat dengan pencapaian <strong>{{ $totalPoin }} Poin XP</strong>.
        </div>

        @if(count($materiScores) > 0)
        <div class="details">
            <table>
                <thead>
                    <tr>
                        <th>Materi & Kuis Evaluasi</th>
                        <th style="text-align:right;">Nilai Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($materiScores as $ms)
                    <tr>
                        <td>{{ $ms['judul'] }}</td>
                        <td style="text-align:right; font-weight:bold;">
                            @if($ms['jenis'] !== 'quiz' && $ms['skor'] === '100.00')
                                Selesai
                            @else
                                {{ $ms['skor'] }}
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif    </div>

    <div class="signature-area">
        <div style="margin-bottom: 5px; min-height: 60px;">
            @if(!isset($setting) || in_array($setting->tipe_ttd ?? 'qr', ['qr', 'both']))
                <img src="data:image/svg+xml;base64,{!! $qrCode !!}" style="height:60px; width:60px; display:inline-block; vertical-align:middle;">
            @endif
            
            @if(isset($setting) && in_array($setting->tipe_ttd ?? 'qr', ['image', 'both']) && $setting->ttd_image)
                <img src="{{ public_path('storage/'.$setting->ttd_image) }}" style="height:60px; display:inline-block; vertical-align:middle; margin-left: 5px;">
            @endif
        </div>
        
        <div class="signature-line"></div>
        <div class="signature-name">{{ $setting->nama_penandatangan ?? 'Admin Sistem' }}</div>
        <div class="signature-title">{{ $setting->jabatan_penandatangan ?? 'Penyelenggara' }}</div>
        <div class="signature-title">{{ $setting->tempat_tanda_tangan ?? 'Makassar' }}</div>
    </div>

    <!-- Verify Area -->
    <div class="verify-area">
        @if(!isset($setting) || $setting->tipe_ttd === 'image')
            <img src="data:image/svg+xml;base64,{!! $qrCode !!}" alt="QR Code" width="60" height="60" class="qr-code">
        @endif
        <div class="verify-text">
            Verifikasi keaslian sertifikat ini di<br>
            <span class="verify-url">{{ url('/') }}/verify-certificate/{{ $sertifikat->credential_id }}</span><br><br>
            Sistem telah mengkonfirmasi identitas individu ini dan partisipasinya dalam pelatihan.
        </div>
    </div>
</body>
</html>



