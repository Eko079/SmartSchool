<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\FeeCategoryController;
use App\Http\Controllers\Admin\BillingController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');


Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
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

    // Bantuan (kirim settings agar kontak ikut data Pengaturan)
    Route::get('/bantuan', function () {
        $settings = \App\Models\SchoolSetting::pluck('value', 'key')->toArray();
        return view('admin.bantuan', compact('settings'));
    })->name('bantuan');
});


