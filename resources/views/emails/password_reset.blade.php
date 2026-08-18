<!DOCTYPE html>
<html>
<head>
    <title>Reset Password LMS Pas Sulsel</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h2 style="color: #f59e0b; text-align: center;">Password Anda Telah Direset</h2>
        
        <p>Halo, <strong>{{ $user->nama }}</strong>,</p>
        
        <p>Sesuai permintaan Anda atau kebijakan Administrator, password akun Anda di <strong>LMS Pas Sulsel</strong> telah direset.</p>
        
        <p>Berikut adalah password sementara Anda:</p>
        
        <div style="background-color: #fef3c7; border: 1px dashed #f59e0b; padding: 15px; text-align: center; margin: 20px 0; border-radius: 5px;">
            <span style="font-size: 24px; font-weight: bold; letter-spacing: 2px; color: #b45309;">{{ $tempPassword }}</span>
        </div>
        
        <p>Silakan login menggunakan password di atas. <strong>Sangat disarankan untuk segera mengganti password Anda</strong> melalui menu Profil setelah berhasil login.</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/login') }}" style="background-color: #2563eb; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;">Login ke LMS</a>
        </div>
        
        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;">
        <p style="text-align: center; color: #94a3b8; font-size: 12px;">&copy; {{ date('Y') }} LMS Pas Sulsel. All rights reserved.</p>
    </div>
</body>
</html>
