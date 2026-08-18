<!DOCTYPE html>
<html>
<head>
    <title>Akun LMS Pas Sulsel Disetujui</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h2 style="color: #2563eb; text-align: center;">Selamat! Akun Anda Telah Disetujui</h2>
        
        <p>Halo, <strong>{{ $user->nama }}</strong>,</p>
        
        <p>Pendaftaran akun Anda di <strong>LMS Pas Sulsel</strong> telah diverifikasi dan disetujui oleh Administrator.</p>
        
        <p>Anda sekarang dapat login ke dalam sistem dan mulai mengakses katalog pelatihan yang tersedia.</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/login') }}" style="background-color: #2563eb; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;">Login ke LMS</a>
        </div>
        
        <p style="color: #64748b; font-size: 14px;">Jika Anda memiliki pertanyaan, silakan hubungi tim dukungan kami.</p>
        
        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;">
        <p style="text-align: center; color: #94a3b8; font-size: 12px;">&copy; {{ date('Y') }} LMS Pas Sulsel. All rights reserved.</p>
    </div>
</body>
</html>
