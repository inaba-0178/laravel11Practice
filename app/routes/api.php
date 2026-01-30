<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Presentation\Controllers\GetRegionList\GetRegionListController;
use App\Presentation\Controllers\PriceList\PriceListController;
use App\Presentation\Controllers\MileageList\MileageListController;
use App\Presentation\Controllers\DisplacementList\DisplacementListController;
use App\Presentation\Controllers\RidingCapacityList\RidingCapacityListController;
use App\Presentation\Controllers\FeaturedBrandList\FeaturedBrandListController;
use App\Presentation\Controllers\FeaturedBodyTypeList\FeaturedBodyTypeListController;

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

// Mileage関連のルート
Route::prefix('Mileages')->group(function () {
    Route::get('/', MileageListController::class)->name('Mileages.list');
});

// Displacement関連のルート
Route::prefix('Displacements')->group(function () {
    Route::get('/', DisplacementListController::class)->name('Displacements.list');
});

// RidingCapacity関連のルート
Route::prefix('RidingCapacities')->group(function () {
    Route::get('/', RidingCapacityListController::class)->name('RidingCapacities.list');
});

// FeaturedBrand関連のルート
Route::prefix('FeaturedBrands')->group(function () {
    Route::get('/', FeaturedBrandListController::class)->name('FeaturedBrands.list');
});

// FeaturedBodyTypes関連のルート
Route::prefix('FeaturedBodyTypes')->group(function () {
    Route::get('/', FeaturedBodyTypeListController::class)->name('FeaturedBodyTypes.list');
});