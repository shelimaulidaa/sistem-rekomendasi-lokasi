<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\Manajer\DashboardController as ManajerDashboardController;
use App\Http\Controllers\Manajer\HasilController as ManajerHasilController;
use App\Http\Controllers\Direktur\DashboardController as DirekturDashboardController;
use App\Http\Controllers\Direktur\RekomendasiController as DirekturRekomendasiController;
use App\Http\Controllers\Direktur\ObservasiController as DirekturObservasiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function (Request $request) {
    if ($request->user()->hasRole('manajer')) {
        return redirect()->route('manajer.dashboard');
    } elseif ($request->user()->hasRole('direktur')) {
        return redirect()->route('direktur.dashboard');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Manajer Routes
Route::middleware(['auth', 'verified', 'role:manajer'])->prefix('manajer')->name('manajer.')->group(function () {
    Route::get('/dashboard', [ManajerDashboardController::class, 'index'])->name('dashboard');

    // Users CRUD
    Route::resource('users', \App\Http\Controllers\Manajer\UserController::class);
    // Kriteria Routes (Restricted: no create, store, or destroy)
    Route::resource('kriteria', \App\Http\Controllers\Manajer\KriteriaController::class)->only(['index', 'edit', 'update'])->parameters([
        'kriteria' => 'kriteria' // prevent laravel from guessing 'kriterium'
    ]);
    
    // Periode Routes
    Route::patch('periode/{periode}/activate', [\App\Http\Controllers\Manajer\PeriodeController::class, 'activate'])->name('periode.activate');
    Route::resource('periode', \App\Http\Controllers\Manajer\PeriodeController::class)->parameters([
        'periode' => 'periode'
    ]);
    // Observasi CRUD
    Route::get('observasi/create', [\App\Http\Controllers\Manajer\ObservasiController::class, 'create'])->name('observasi.create');
    Route::post('observasi/calculate', [\App\Http\Controllers\Manajer\ObservasiController::class, 'calculate'])->name('observasi.calculate');
    Route::get('observasi/{observasi}/export-pdf', [\App\Http\Controllers\Manajer\ObservasiController::class, 'exportPdf'])->name('observasi.export-pdf');
    Route::resource('observasi', \App\Http\Controllers\Manajer\ObservasiController::class)->except(['create']);
    // Penilaian & Perhitungan TOPSIS (Background)
    Route::get('/penilaian', [\App\Http\Controllers\Manajer\PenilaianController::class, 'index'])->name('penilaian.index')->middleware('permission:manage penilaian');
    Route::post('/penilaian/calculate', [\App\Http\Controllers\Manajer\PenilaianController::class, 'calculate'])->name('penilaian.calculate')->middleware('permission:process perhitungan');

    // Hasil Observasi Routes
    Route::get('/hasil', [ManajerHasilController::class, 'index'])->name('hasil.index')->middleware('permission:view hasil');
    Route::get('/hasil/export/pdf', [ManajerHasilController::class, 'exportPdf'])->name('hasil.export.pdf')->middleware('permission:view hasil');

    // Riwayat Penilaian (History)
    Route::get('/history', [\App\Http\Controllers\Manajer\HistoryController::class, 'index'])->name('history.index')->middleware('permission:view hasil');
    Route::get('/history/export/pdf', [\App\Http\Controllers\Manajer\HistoryController::class, 'exportPdf'])->name('history.export.pdf')->middleware('permission:view hasil');
    Route::get('/history/export/excel', [\App\Http\Controllers\Manajer\HistoryController::class, 'exportExcel'])->name('history.export.excel')->middleware('permission:view hasil');
});

// Direktur Routes
Route::middleware(['auth', 'verified', 'role:direktur'])->prefix('direktur')->name('direktur.')->group(function () {
    Route::get('/dashboard', [DirekturDashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('permission:view dashboard');

    Route::prefix('rekomendasi')->name('rekomendasi.')->group(function() {
        Route::get('/', [DirekturRekomendasiController::class, 'index'])->name('index');
        Route::get('/export/pdf', [DirekturRekomendasiController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/export/excel', [DirekturRekomendasiController::class, 'exportExcel'])->name('export.excel');
        Route::get('/{id}', [DirekturRekomendasiController::class, 'show'])->name('show');
    });

    Route::prefix('observasi')->name('observasi.')->group(function() {
        Route::get('/', [DirekturObservasiController::class, 'index'])->name('index');
        Route::get('/{id}', [DirekturObservasiController::class, 'show'])->name('show');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Wilayah API Routes (accessible by authenticated users)
    Route::prefix('api/wilayah')->group(function () {
        Route::get('/jabar-stats', [WilayahController::class, 'jabarStats']);
    });

    // Spatial API Route
    Route::get('/api/spatial/analyze-location', [\App\Http\Controllers\Api\SpatialController::class, 'analyzeLocation']);
});

require __DIR__.'/auth.php';
