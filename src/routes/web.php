<?php

use Illuminate\Support\Facades\Route;
use admin\pages\Controllers\PageManagerController;


Route::name('admin.')->middleware(['web','auth:admin'])->group(function () {  
    Route::middleware('auth:admin')->group(function () {
        Route::resource('pages', PageManagerController::class);
        Route::post('pages/updateStatus', [PageManagerController::class, 'updateStatus'])->name('pages.updateStatus');
    });
});