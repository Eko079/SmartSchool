<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/login', 'auth.login')->name('login');
Route::view('/lupa-password-otp', 'auth.otp')->name('auth.otp');

// Halaman admin (branch review: halaman-admin)
Route::view('/beranda', 'admin.beranda')->name('admin.beranda');
Route::view('/siswa', 'admin.siswa')->name('admin.siswa');
Route::view('/siswa/detail', 'admin.siswa-detail')->name('admin.siswa.detail');
Route::view('/kategori-tagihan', 'admin.kategori-tagihan')->name('admin.kategori');
Route::view('/billing-generator', 'admin.billing-generator')->name('admin.billing');
Route::view('/billing-generator/konfirmasi', 'admin.konfirmasi-generate')->name('admin.billing.konfirmasi');
Route::view('/invoice/detail', 'admin.invoice-detail')->name('admin.invoice.detail');
Route::view('/laporan', 'admin.laporan')->name('admin.laporan');
Route::view('/riwayat-generate', 'admin.riwayat-generate')->name('admin.riwayat');
Route::view('/import/preview', 'admin.preview-import')->name('admin.import.preview');
Route::view('/audit-log', 'admin.audit-log')->name('admin.audit');
Route::view('/tiket', 'admin.tiket')->name('admin.tiket');
Route::view('/bantuan', 'admin.bantuan')->name('admin.bantuan');
Route::view('/pengaturan', 'admin.pengaturan')->name('admin.pengaturan');
Route::view('/log-notifikasi', 'admin.log-notifikasi')->name('admin.notif.log');
Route::view('/demo-state', 'admin.demo-state')->name('admin.demo.state');
Route::view('/beranda-dark', 'admin.beranda-dark')->name('admin.beranda.dark');
