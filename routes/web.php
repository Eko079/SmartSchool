<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\FeeCategoryController;
use App\Http\Controllers\Admin\BillingController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Portal\AuthController as PortalAuthController;
use App\Http\Controllers\Portal\DashboardController as PortalDashboardController;
use App\Http\Controllers\Portal\BillingController as PortalBillingController;
use App\Http\Controllers\Portal\SupportController as PortalSupportController;
use App\Http\Controllers\Portal\AccountController as PortalAccountController;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');


Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Siswa
    Route::get('/siswa', [StudentController::class, 'index'])->name('siswa');
    Route::post('/siswa', [StudentController::class, 'store'])->name('siswa.store');
    Route::post('/siswa/import', [StudentController::class, 'import'])->name('siswa.import');
    Route::get('/siswa/{student}/bills', [StudentController::class, 'bills'])->name('siswa.bills');
    Route::get('/siswa/{student}/edit', [StudentController::class, 'edit'])->name('siswa.edit');
    Route::put('/siswa/{student}', [StudentController::class, 'update'])->name('siswa.update');
    Route::delete('/siswa/{student}', [StudentController::class, 'destroy'])->name('siswa.destroy');

    // Kategori Tagihan
    Route::get('/kategori-tagihan', [FeeCategoryController::class, 'index'])->name('kategori-tagihan');
    Route::post('/kategori-tagihan', [FeeCategoryController::class, 'store'])->name('kategori-tagihan.store');
    Route::put('/kategori-tagihan/{category}', [FeeCategoryController::class, 'update'])->name('kategori-tagihan.update');
    Route::post('/kategori-tagihan/{category}/toggle', [FeeCategoryController::class, 'toggle'])->name('kategori-tagihan.toggle');

    // Billing Generator
    Route::get('/billing', [BillingController::class, 'index'])->name('billing');
    Route::post('/billing/generate', [BillingController::class, 'generate'])->name('billing.generate');

    // Laporan
    Route::get('/laporan', [ReportController::class, 'index'])->name('laporan');

    // Pengaturan
    Route::get('/pengaturan', [SettingController::class, 'index'])->name('pengaturan');
    Route::post('/pengaturan', [SettingController::class, 'update'])->name('pengaturan.update');

    // Bantuan (kirim settings agar kontak ikut data Pengaturan; HP => mobile otomatis)
    Route::get('/bantuan', function (\Illuminate\Http\Request $request) {
        if (\App\Support\Device::isPhone($request)) {
            return app(\App\Http\Controllers\Admin\MobileController::class)->bantuan();
        }
        $settings = \App\Models\SchoolSetting::pluck('value', 'key')->toArray();
        return view('admin.bantuan', compact('settings'));
    })->name('bantuan');
});

// ---------- Portal Siswa / Wali (terisolasi dari admin) ----------
Route::prefix('portal')->name('portal.')->group(function () {
    Route::get('/login', [PortalAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [PortalAuthController::class, 'login'])->name('login.attempt');
    Route::post('/otp', [PortalAuthController::class, 'requestOtp'])->name('otp');
    Route::post('/reset-password', [PortalAuthController::class, 'resetPassword'])->name('reset');
    Route::post('/midtrans/callback', [PortalBillingController::class, 'callback'])->name('midtrans.callback');

    Route::middleware(['wali'])->group(function () {
        Route::post('/logout', [PortalAuthController::class, 'logout'])->name('logout');
        Route::get('/', [PortalDashboardController::class, 'index'])->name('dashboard');
        Route::get('/tagihan', [PortalBillingController::class, 'index'])->name('tagihan');
        Route::get('/tagihan/{bill}', [PortalBillingController::class, 'show'])->name('tagihan.show');
        Route::post('/tagihan/{bill}/bayar', [PortalBillingController::class, 'pay'])->name('tagihan.pay');
        Route::get('/riwayat', [PortalBillingController::class, 'history'])->name('riwayat');
        Route::get('/kuitansi/{payment}', [PortalBillingController::class, 'receipt'])->name('kuitansi');
        Route::get('/profil', [PortalAccountController::class, 'profile'])->name('profil');
        Route::get('/bantuan', [PortalSupportController::class, 'index'])->name('bantuan');
        Route::post('/bantuan', [PortalSupportController::class, 'store'])->name('bantuan.store');
        Route::post('/bantuan/{ticket}/balas', [PortalSupportController::class, 'reply'])->name('bantuan.reply');
        Route::get('/pengaturan', [PortalAccountController::class, 'show'])->name('pengaturan');
        Route::put('/pengaturan', [PortalAccountController::class, 'update'])->name('pengaturan.update');
        Route::put('/pengaturan/sandi', [PortalAccountController::class, 'password'])->name('pengaturan.sandi');
    });
});


