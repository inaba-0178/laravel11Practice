<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Presentation\Controllers\GetRegionList\GetRegionListController;
use App\Presentation\Controllers\PriceList\PriceListController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Region関連のルート
Route::prefix('regions')->group(function () {
    Route::get('/', GetRegionListController::class)->name('regions.list');
    Route::get('/grouped', [GetRegionListController::class, 'groupedByArea'])->name('regions.grouped');
});

// Price関連のルート
Route::prefix('Prices')->group(function () {
    Route::get('/', PriceListController::class)->name('prices.list');
});

