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
    return redirect()->route('portal.login');
});

// Auth admin di bawah /admin agar root milik portal.
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('logout');
Route::view('/admin/forgot-password', 'auth.forgot-password')->name('password.request');


Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Siswa
    Route::get('/siswa', [StudentController::class, 'index'])->name('siswa');
    Route::get('/siswa/template', function () {
        $path = public_path('templates/template_import_siswa.xlsx');
        abort_if(! file_exists($path), 404, 'Template belum tersedia.');

        return response()->download($path, 'template_import_siswa.xlsx');
    })->name('siswa.template');
    Route::post('/siswa', [StudentController::class, 'store'])->name('siswa.store');
    Route::post('/siswa/import', [StudentController::class, 'import'])->name('siswa.import');
    Route::post('/siswa/import/confirm', [StudentController::class, 'importConfirm'])->name('siswa.import.confirm');
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
    Route::post('/billing/generate-package', [BillingController::class, 'generatePackage'])->name('billing.package');

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
        $tickets = \App\Models\HelpTicket::with(['user.student', 'replies'])->latest()->take(20)->get();
        $ticketStats = [
            'open' => \App\Models\HelpTicket::where('status', 'open')->count(),
            'answered' => \App\Models\HelpTicket::where('status', 'answered')->count(),
            'closed' => \App\Models\HelpTicket::where('status', 'closed')->count(),
        ];
        return view('admin.bantuan', compact('settings', 'tickets', 'ticketStats'));
    })->name('bantuan');

    // Tiket bantuan wali
    Route::get('/tiket', [\App\Http\Controllers\Admin\TicketController::class, 'index'])->name('tiket');
    Route::post('/tiket/{ticket}/balas', [\App\Http\Controllers\Admin\TicketController::class, 'reply'])->name('tiket.reply');
    Route::post('/tiket/{ticket}/tutup', [\App\Http\Controllers\Admin\TicketController::class, 'close'])->name('tiket.close');
    Route::post('/tiket/{ticket}/buka', [\App\Http\Controllers\Admin\TicketController::class, 'reopen'])->name('tiket.reopen');
});

// ---------- Portal Siswa / Wali di root (main link milik portal) ----------
// Auth portal di root agar domain utama langsung portal.
Route::get('/login', [PortalAuthController::class, 'showLoginForm'])->name('portal.login');
Route::post('/login', [PortalAuthController::class, 'login'])->name('portal.login.attempt');
Route::post('/otp', [PortalAuthController::class, 'requestOtp'])->name('portal.otp');
Route::post('/reset-password', [PortalAuthController::class, 'resetPassword'])->name('portal.reset');
Route::post('/midtrans/callback', [PortalBillingController::class, 'callback'])->name('portal.midtrans.callback');

Route::middleware(['wali'])->name('portal.')->group(function () {
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

// Kompatibilitas link lama /portal/* -> root.
Route::prefix('portal')->group(function () {
    Route::redirect('/login', '/login', 301);
    Route::redirect('/', '/', 301);
    Route::redirect('/tagihan', '/tagihan', 301);
    Route::redirect('/riwayat', '/riwayat', 301);
    Route::redirect('/profil', '/profil', 301);
    Route::redirect('/bantuan', '/bantuan', 301);
    Route::redirect('/pengaturan', '/pengaturan', 301);
});


