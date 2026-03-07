<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Presentation\Controllers\RegionList\RegionListController;
use App\Presentation\Controllers\PriceList\PriceListController;
use App\Presentation\Controllers\MileageList\MileageListController;
use App\Presentation\Controllers\DisplacementList\DisplacementListController;
use App\Presentation\Controllers\RidingCapacityList\RidingCapacityListController;
use App\Presentation\Controllers\FeaturedBrandList\FeaturedBrandListController;
use App\Presentation\Controllers\FeaturedBodyTypeList\FeaturedBodyTypeListController;
use App\Presentation\Controllers\SelectManufacturerList\SelectManufacturerListController;
use App\Presentation\Controllers\SelectBodyTypeList\SelectBodyTypeListController;
use App\Presentation\Controllers\ManufacturerList\ManufacturerListController;
use App\Presentation\Controllers\BodyTypeInfo\BodyTypeInfoController;
use App\Presentation\Controllers\CarList\CarListController;
use App\Presentation\Controllers\AreaCarList\AreaCarListController;
use App\Presentation\Controllers\SelectAreaCarList\SelectAreaCarListController;
use App\Presentation\Controllers\SelectCarData\SelectCarDataController;
use App\Presentation\Controllers\Room\RoomController;
use App\Presentation\Controllers\Message\MessageController;

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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/rooms', [RoomController::class, 'index']);
    Route::post('/rooms', [RoomController::class, 'store']);
    Route::get('/rooms/{roomId}', [RoomController::class, 'show']);

    Route::get('/rooms/{roomId}/messages', [MessageController::class, 'index']);
    Route::post('/rooms/{roomId}/messages', [MessageController::class, 'store']);
    Route::post('/rooms/{roomId}/messages/read', [MessageController::class, 'read']);
    Route::post('/rooms/{roomId}/typing', [MessageController::class, 'typing']);
});

// Region関連のルート
Route::prefix('regions')->group(function () {
    Route::get('/', RegionListController::class)->name('regions.list');
    Route::get('/grouped', [RegionListController::class, 'groupedByArea'])->name('regions.grouped');
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

// SelectManufacturerList関連のルート
Route::prefix('SelectManufacturers')->group(function () {
    Route::get('/', SelectManufacturerListController::class)->name('SelectManufacturers.list');
});

// SelectBodyTypeList関連のルート
Route::prefix('SelectBodyTypeLists')->group(function () {
    Route::get('/', SelectBodyTypeListController::class)->name('SelectBodyTypeLists.list');
});

// Manufacturer関連のルート
Route::prefix('Manufacturers')->group(function () {
    Route::get('/', ManufacturerListController::class)->name('Manufacturers.list');
});

// BodyTypeInfo関連のルート
Route::prefix('BodyTypeInfo')->group(function () {
    Route::get('/', BodyTypeInfoController::class)->name('BodyTypeInfo.list');
});

// AreaCarList関連のルート
Route::prefix('CarList')->group(function () {
    Route::get('/', CarListController::class)->name('CarList.list');
});

// AreaCarList関連のルート
Route::prefix('AreaCarList')->group(function () {
    Route::get('/', AreaCarListController::class)->name('AreaCarList.list');
});

// SelectAreaCarList関連のルート
Route::prefix('SelectAreaCarList')->group(function () {
    Route::get('/', SelectAreaCarListController::class)->name('SelectAreaCarList.list');
});

// SelectCarData関連のルート
Route::prefix('SelectCarData')->group(function () {
    Route::get('/', SelectCarDataController::class)->name('SelectCarData.list');
});


Route::post('/login', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    if (!\Illuminate\Support\Facades\Auth::attempt($request->only('email', 'password'))) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $user  = $request->user();
    $token = $user->createToken('chat-token')->plainTextToken;

    return response()->json([
        'token' => $token,
        'user'  => $user,
    ]);
});