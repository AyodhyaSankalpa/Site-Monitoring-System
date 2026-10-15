<?php

use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

// Redirect home to sites dashboard
Route::get('/', function () {
    return redirect()->route('sites.index');
});

// Sites Resource Routes & Monitoring Actions
Route::prefix('sites')->name('sites.')->group(function () {
    Route::get('/', [SiteController::class, 'index'])->name('index');
    Route::get('/create', [SiteController::class, 'create'])->name('create');
    Route::post('/', [SiteController::class, 'store'])->name('store');
    Route::post('/check-all', [SiteController::class, 'checkAll'])->name('checkAll');
    Route::get('/{site}', [SiteController::class, 'show'])->name('show');
    Route::get('/{site}/edit', [SiteController::class, 'edit'])->name('edit');
    Route::put('/{site}', [SiteController::class, 'update'])->name('update');
    Route::delete('/{site}', [SiteController::class, 'destroy'])->name('destroy');
    Route::post('/{site}/check', [SiteController::class, 'check'])->name('check');
});

// User Guide / Documentation Route
Route::view('/guide', 'guide')->name('guide');
