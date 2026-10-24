<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'beranda');
// Route::livewire('/aduan', 'aduan.create');

Route::prefix('aduan')->group(function () {
    Route::livewire('/', 'aduan.create');
    Route::livewire('/lacak', 'aduan.lacak');
    Route::livewire('/detail/{id_aduan?}', 'aduan.detail');
    Route::livewire('/tiket/{kode_tiket?}', 'aduan.tiket');
});

Route::middleware(['auth'])->group(function () {
    Route::prefix('dashboard')->group(function () {
        Route::livewire('/', 'dashboard');
        Route::livewire('/aduan', 'aduan.list');
        Route::livewire('/aduan/{id_aduan?}', 'aduan.tindakan');
    });
});