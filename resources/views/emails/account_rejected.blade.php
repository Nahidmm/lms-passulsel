<!DOCTYPE html>
<html>
<head>
    <title>Status Pendaftaran Akun STRAPSUSPAS</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h2 style="color: #ef4444; text-align: center;">Status Pendaftaran Akun</h2>
        
        <p>Halo, <strong>{{ $user->nama }}</strong>,</p>
        
        <p>Mohon maaf, pendaftaran akun Anda di <strong>STRAPSUSPAS</strong> tidak dapat disetujui oleh Administrator saat ini.</p>
        
        <div style="background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 15px; margin: 20px 0;">
            <p style="margin: 0; color: #991b1b;"><strong>Alasan Penolakan:</strong><br>{{ $reason }}</p>
        </div>
        
        <p>Anda dapat mencoba mendaftar kembali dengan memperbaiki informasi sesuai dengan alasan penolakan di atas.</p>
        
        <p style="color: #64748b; font-size: 14px; mt-4">Jika Anda merasa ini adalah kesalahan, silakan hubungi tim dukungan kami.</p>
        
        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;">
        <p style="text-align: center; color: #94a3b8; font-size: 12px;">&copy; {{ date('Y') }} STRAPSUSPAS. All rights reserved.</p>
    </div>
</body>
</html>

