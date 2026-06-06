<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Filament\MstUploadController;
use App\Presentation\Controllers\Estimate\EstimateDownloadController;

Route::get('/', function () {
    return view('welcome');
});


Route::middleware(['web'])
    ->prefix('admin')
    ->group(function () {
        Route::post('/mst-upload/upload', [MstUploadController::class, 'upload']);
    });

Route::middleware(['web', 'auth'])->group(function () {
Route::get('/estimate/{estimate}/download', EstimateDownloadController::class)
    ->name('estimate.download');
});