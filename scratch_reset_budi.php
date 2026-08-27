<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('nama', 'like', '%budi%')->orWhere('email', 'like', '%budi%')->first();

if ($user) {
    echo "Ditemukan user: " . $user->nama . " (" . $user->email . ")\n";
    $user->has_taken_pretest = false;
    // Reset password to 12345678 for testing
    $user->password = bcrypt('12345678');
    $user->save();

    // Hapus data sesi evaluasi & hasil pretest agar benar-benar dari 0
    $sesiIds = \App\Models\SesiEvaluasi::where('user_id', $user->id)->pluck('id');
    \App\Models\HasilLatihan::whereIn('sesi_evaluasi_id', $sesiIds)->delete();
    \App\Models\SesiEvaluasi::where('user_id', $user->id)->delete();
    \App\Models\HasilPretestTopik::where('user_id', $user->id)->delete();
    \App\Models\ProgresMateri::where('user_id', $user->id)->delete();

    echo "Status pretest untuk " . $user->nama . " telah di-reset!\nPassword juga diset menjadi: 12345678";
} else {
    echo "User dengan nama/email 'budi' tidak ditemukan. Saya akan mereset akun peserta pertama yang ada...\n";
    $user = \App\Models\User::where('role', 'peserta')->first();
    if ($user) {
        $user->has_taken_pretest = false;
        $user->password = bcrypt('12345678');
        $user->save();
        
        $sesiIds = \App\Models\SesiEvaluasi::where('user_id', $user->id)->pluck('id');
        \App\Models\HasilLatihan::whereIn('sesi_evaluasi_id', $sesiIds)->delete();
        \App\Models\SesiEvaluasi::where('user_id', $user->id)->delete();
        \App\Models\HasilPretestTopik::where('user_id', $user->id)->delete();
        \App\Models\ProgresMateri::where('user_id', $user->id)->delete();
        
        echo "Telah mereset user: " . $user->nama . " (" . $user->email . ")\nPassword: 12345678";
    } else {
        echo "Tidak ada user peserta di database.";
    }
}
