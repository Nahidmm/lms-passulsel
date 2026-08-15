<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\EvaluasiController;
use App\Http\Controllers\StatistikController;
use App\Http\Controllers\AiAssistantController;
use App\Http\Controllers\KalenderController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ManajemenAkunController;
use App\Http\Controllers\SoalController;

// Public / Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Authenticated Routes (Any Role)
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Force change password (if needed)
    Route::get('/auth/change-password', [AuthController::class, 'showChangePassword'])->name('auth.change-password');
    Route::post('/auth/change-password', [AuthController::class, 'changePassword'])->name('auth.change-password.post');

    // Waiting approval page
    Route::get('/auth/waiting-approval', [AuthController::class, 'showWaitingApproval'])->name('auth.waiting');

    // Normal access (Account approved & password changed)
    Route::middleware(['account.approved', 'force.password'])->group(function () {
        
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Profil
        Route::get('/profil', [AccountController::class, 'index'])->name('profil.index');
        Route::post('/profil/update', [AccountController::class, 'update'])->name('profil.update');
        Route::post('/profil/password', [AccountController::class, 'updatePassword'])->name('profil.password');

        // AI Assistant (Blocked during active evaluasi)
        Route::middleware(['evaluasi.mode'])->group(function () {
            Route::get('/ai-assistant', [AiAssistantController::class, 'index'])->name('ai.index');
            Route::post('/ai-assistant/chat', [AiAssistantController::class, 'chat'])->name('ai.chat');
        });

        // ==========================================
        // ROLE: PESERTA
        // ==========================================
        Route::middleware(['role:peserta'])->prefix('peserta')->name('peserta.')->group(function () {
            
            // Pembelajaran (Pelatihan)
            Route::get('/pelatihan', [\App\Http\Controllers\PelatihanController::class, 'indexPeserta'])->name('pelatihan.index');
            Route::get('/pelatihan/{pelatihan}', [\App\Http\Controllers\PelatihanController::class, 'showPeserta'])->name('pelatihan.show');
            Route::get('/pembelajaran/materi/{materi}', [\App\Http\Controllers\MateriController::class, 'showPeserta'])->name('pembelajaran.materi.show');
            Route::post('/pembelajaran/materi/{materi}/progress', [\App\Http\Controllers\MateriController::class, 'updateProgress'])->name('pembelajaran.materi.progress');

            // Evaluasi (Kuis)
            Route::get('/evaluasi', [EvaluasiController::class, 'index'])->name('evaluasi.index');
            Route::post('/evaluasi/start', [EvaluasiController::class, 'start'])->name('evaluasi.start');
            Route::get('/evaluasi/soal/{sesi}', [EvaluasiController::class, 'soal'])->name('evaluasi.soal');
            Route::post('/evaluasi/submit/{sesi}', [EvaluasiController::class, 'submit'])->name('evaluasi.submit');
            Route::get('/evaluasi/hasil/{sesi}', [EvaluasiController::class, 'hasil'])->name('evaluasi.hasil');

            // Statistik / Riwayat
            Route::get('/statistik', [StatistikController::class, 'indexPeserta'])->name('statistik.index');
        });

        // ==========================================
        // ROLE: ADMIN & SUPERADMIN
        // ==========================================
        Route::middleware(['role:admin,superadmin'])->prefix('admin')->name('admin.')->group(function () {
            
            // Kelola Pelatihan, Materi, dan Soal (Nested)
            Route::resource('pelatihan', \App\Http\Controllers\PelatihanController::class);
            Route::resource('pelatihan.materi', \App\Http\Controllers\MateriController::class)->shallow();
            Route::resource('materi.soal', \App\Http\Controllers\SoalController::class)->shallow();

            // Kuis: Nilai & Review Peserta
            Route::get('kuis/{materi}/peserta', [\App\Http\Controllers\Admin\KuisReviewController::class, 'indexPeserta'])->name('kuis.peserta');
            Route::get('kuis/{materi}/peserta/{sesi}', [\App\Http\Controllers\Admin\KuisReviewController::class, 'showJawaban'])->name('kuis.jawaban');
            Route::post('kuis/{materi}/peserta/{sesi}/nilai', [\App\Http\Controllers\Admin\KuisReviewController::class, 'nilaiManual'])->name('kuis.nilai-manual');
            Route::get('kuis/{materi}/export', [\App\Http\Controllers\Admin\KuisReviewController::class, 'exportNilai'])->name('kuis.export');

            // Import Soal dari CSV
            Route::get('materi/{materi}/import-soal', [\App\Http\Controllers\Admin\ImportSoalController::class, 'create'])->name('soal.import');
            Route::post('materi/{materi}/import-soal', [\App\Http\Controllers\Admin\ImportSoalController::class, 'store'])->name('soal.import.store');
            Route::get('materi/{materi}/template-soal', [\App\Http\Controllers\Admin\ImportSoalController::class, 'template'])->name('soal.template');

            // API endpoints for Drag-and-Drop builder
            Route::post('pelatihan/{pelatihan}/reorder', [\App\Http\Controllers\PelatihanController::class, 'reorder'])->name('pelatihan.reorder');
            
            // Note: VideoController is deprecated since video is now a type of Materi

            // Statistik / Rekap Peserta
            Route::get('/statistik', [StatistikController::class, 'indexAdmin'])->name('statistik.index');

            // Manajemen Akun
            Route::get('/akun', [ManajemenAkunController::class, 'index'])->name('akun.index');
            Route::post('/akun/approve/{id}', [ManajemenAkunController::class, 'approve'])->name('akun.approve');
            Route::post('/akun/reject/{id}', [ManajemenAkunController::class, 'reject'])->name('akun.reject');
            Route::post('/akun/reset-password/{id}', [ManajemenAkunController::class, 'resetPassword'])->name('akun.reset-password');
            Route::delete('/akun/delete/{id}', [ManajemenAkunController::class, 'destroy'])->name('akun.destroy');

            // Kalender Akademik
            Route::resource('kalender', KalenderController::class);
        });
    });
});
