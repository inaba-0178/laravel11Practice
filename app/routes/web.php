<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Filament\MstUploadController;

Route::get('/', function () {
    return view('welcome');
});


Route::middleware(['web'])
    ->prefix('admin')
    ->group(function () {
        Route::post('/mst-upload/upload', [MstUploadController::class, 'upload']);
    });