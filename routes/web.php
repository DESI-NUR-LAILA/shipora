<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TtdController;
use App\Http\Controllers\PortController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PergerakanController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\IogController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\TrackingController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::middleware('isLogin')->group(function(){
    // Login
    Route::get('login', [AuthController::class, 'login'])->name('login');
    Route::post('login', [AuthController::class, 'loginProses'])->name('loginProses');
});

// Logout
Route::get('logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('checkLogin')->group(function(){
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('pdf', [DashboardController::class, 'pdf'])->name('dashboardPdf');

    // Tanda Tangan
    Route::get('ttd', [TtdController::class, 'index'])->name('ttd');  
    Route::get('ttd/arsip', [TtdController::class, 'arsip'])->name('ttdArsip');
    Route::get('ttd/create', [TtdController::class, 'create'])->name('ttdCreate');  
    Route::post('ttd/store', [TtdController::class, 'store'])->name('ttdStore');
    Route::put('ttd/arsip/{id}', [TtdController::class, 'arsipkan'])->name('ttdArsipkan');
    Route::put('ttd/{id}/unarsip', [TTDController::class, 'unarsip'])->name('ttdUnarsip');
    Route::get('ttd/excel', [TtdController::class, 'excel'])->name('ttdExcel');
    Route::get('ttd/pdf', [TtdController::class, 'pdf'])->name('ttdPdf');

    // Data Pergerakan
    Route::get('pergerakan', [PergerakanController::class, 'index'])->name('pergerakan');
    Route::get('pergerakan/create', [PergerakanController::class, 'create'])->name('pergerakanCreate');
    Route::post('pergerakan/store', [PergerakanController::class, 'store'])->name('pergerakanStore');
    Route::get('pergerakan/edit/{id}', [PergerakanController::class, 'edit'])->name('pergerakanEdit');
    Route::post('pergerakan/update/{id}', [PergerakanController::class, 'update'])->name('pergerakanUpdate');
    Route::get('pergerakan/detail', [PergerakanController::class, 'detail'])->name('pergerakanDetail');
    Route::get('pergerakan/excel', [PergerakanController::class, 'excel'])->name('pergerakanExcel');
    Route::get('pergerakan/pdf', [PergerakanController::class, 'pdf'])->name('pergerakanPdf');
    Route::get('pergerakan/kedatangan', [PergerakanController::class, 'kedatangan'])->name('pergerakanKedatangan');

    // Data Pegawai
    Route::get('pegawai', [PegawaiController::class, 'index'])->name('pegawai');
    Route::get('pegawai/create', [PegawaiController::class, 'create'])->name('pegawaiCreate');
    Route::post('pegawai/store', [PegawaiController::class, 'store'])->name('pegawaiStore');
    Route::get('pegawai/edit/{id}', [PegawaiController::class, 'edit'])->name('pegawaiEdit');
    Route::post('pegawai/update/{id}', [PegawaiController::class, 'update'])->name('pegawaiUpdate');
    Route::get('pegawai/detail/{id}', [PegawaiController::class, 'detail'])->name('pegawaiDetail');
    Route::get('pegawai/arsip', [PegawaiController::class, 'arsip'])->name('pegawaiArsip');
    Route::put('pegawai/arsip/{id}', [PegawaiController::class, 'arsipkan'])->name('pegawaiArsipkan');
    Route::put('pegawai/{id}/unarsip', [PegawaiController::class, 'unarsip'])->name('pegawaiUnarsip');
    Route::get('pegawai/excel', [PegawaiController::class, 'excel'])->name('pegawaiExcel');
    Route::get('pegawai/pdf', [PegawaiController::class, 'pdf'])->name('pegawaiPdf');
    
    // Surat Cuti
    Route::get('cuti', [CutiController::class, 'index'])->name('cuti');
    Route::get('cuti/create', [CutiController::class, 'create'])->name('cutiCreate');
    Route::post('cuti/store', [CutiController::class, 'store'])->name('cutiStore');
    Route::get('cuti/detail/{id}', [CutiController::class, 'detail'])->name('cutiDetail');
    Route::get('cuti/edit/{id}', [CutiController::class, 'edit'])->name('cutiEdit');
    Route::post('cuti/update/{id}', [CutiController::class, 'update'])->name('cutiUpdate');
    Route::get('cuti/pdf/{id}', [CutiController::class, 'pdf'])->name('cutiPdf');
    Route::post('cuti/{id}/mengetahui', [CutiController::class, 'mengetahui'])->name('cutiMengetahui');
    Route::post('cuti/{id}/approve', [CutiController::class, 'approve'])->name('cutiApprove');
    Route::put('/cuti/{id}/tolak', [CutiController::class, 'tolak'])->name('cutiTolak');

    // Ijin Olah Gerak
    Route::get('iog', [IogController::class, 'index'])->name('iog');
    Route::get('iog/create', [IogController::class, 'create'])->name('iogCreate');
    Route::post('iog/store', [IogController::class, 'store'])->name('iogStore');
    Route::get('iog/detail/{id}', [IogController::class, 'detail'])->name('iogDetail');
    Route::get('iog/edit/{id}', [IogController::class, 'edit'])->name('iogEdit');
    Route::post('iog/update/{id}', [IogController::class, 'update'])->name('iogUpdate');
    Route::get('iog/pdf/{id}', [IogController::class, 'pdf'])->name('iogPdf');

    // Laporan
    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan');
    Route::get('laporan/create/{pergerakan_id?}', [LaporanController::class, 'create'])->name('laporanCreate');
    Route::post('laporan/store', [LaporanController::class, 'store'])->name('laporanStore');
    Route::get('laporan/jenis-file/{pergerakan_id}', [LaporanController::class, 'getJenisFile'])->name('laporan.getJenisFile');
    Route::get('laporan/edit/{id}', [LaporanController::class, 'edit'])->name('laporanEdit');
    Route::get('laporan/edit-jenis-file/{id}', [LaporanController::class, 'getJenisFileEdit']);
    Route::post('laporan/update/{id}', [LaporanController::class, 'update'])->name('laporanUpdate');
    Route::get('laporan/detail/{id}', [LaporanController::class, 'detail'])->name('laporanDetail');
    Route::post('laporan/komentar/{id}', [LaporanController::class, 'komentar'])->name('laporanKomentar');
    Route::patch('/laporan/verifikasi-komentar/{id}', [LaporanController::class, 'verifikasiKomentar'])->name('laporanVerifikasiKomentar');
    Route::post('laporan/update-status/{id}', [LaporanController::class, 'updateStatus'])->name('laporanUpdateStatus');
    Route::post('/laporan/revisi/{pergerakan_id}', [LaporanController::class, 'revisi'])->name('laporanRevisi');
    Route::get('laporan/combine/{id}', [LaporanController::class, 'combine'])->name('laporanCombine');
    Route::get('laporan/excel', [LaporanController::class, 'excel'])->name('laporanExcel');
    Route::get('laporan/pdf', [LaporanController::class, 'pdf'])->name('laporanPdf');

    Route::middleware('isSekretaris')->group(function(){
        // Data Lokasi Port
        Route::get('port', [PortController::class, 'index'])->name('port');
        Route::get('port/create', [PortController::class, 'create'])->name('portCreate');
        Route::post('port/store', [PortController::class, 'store'])->name('portStore');
        Route::get('port/edit/{id}', [PortController::class, 'edit'])->name('portEdit');
        Route::post('port/update/{id}', [PortController::class, 'update'])->name('portUpdate');
        Route::get('port/excel', [PortController::class, 'excel'])->name('portExcel');
        Route::get('port/pdf', [PortController::class, 'pdf'])->name('portPdf');

        // Data User
        Route::get('user', [UserController::class, 'index'])->name('user');
        Route::get('user/create', [UserController::class, 'create'])->name('userCreate');
        Route::post('user/store', [UserController::class, 'store'])->name('userStore');
        Route::get('user/edit/{id}', [UserController::class, 'edit'])->name('userEdit');
        Route::post('user/update/{id}', [UserController::class, 'update'])->name('userUpdate');
        Route::get('user/excel', [UserController::class, 'excel'])->name('userExcel');
        Route::get('user/pdf/{role}', [UserController::class, 'pdf'])->name('userPdf');
    });
});