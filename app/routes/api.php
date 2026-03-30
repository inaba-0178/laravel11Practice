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
use App\Presentation\Controllers\Auth\AuthController;
use App\Presentation\Controllers\Password\PasswordResetController;
use App\Presentation\Controllers\Member\MemberRegistrationController;
use App\Presentation\Controllers\EditMember\EditMemberProfileController;
use App\Presentation\Controllers\MemberAuth\MemberAuthController;
use App\Presentation\Controllers\EmailChange\EmailChangeController;
use App\Presentation\Controllers\ChangePassword\ChangePasswordController;
use App\Presentation\Controllers\SeriesStkCount\SeriesStkCountController;
use App\Presentation\Controllers\ColorOption\ColorOptionController;
use App\Presentation\Controllers\BasicOption\BasicOptionController;
use App\Presentation\Controllers\DetailOption\DetailOptionController;
use App\Presentation\Controllers\EquipmentBasic\EquipmentBasicController;
use App\Presentation\Controllers\EquipmentSafety\EquipmentSafetyController;
use App\Presentation\Controllers\EquipmentEnv\EquipmentEnvController;
use App\Presentation\Controllers\EquipmentDressup\EquipmentDressupController;
use App\Presentation\Controllers\SeatOption\SeatOptionController;
use App\Presentation\Controllers\LoanMonthlyOption\LoanMonthlyOptionController;
use App\Presentation\Controllers\LoanDownOption\LoanDownOptionController;
use App\Presentation\Controllers\CarTypeOption\CarTypeOptionController;
use App\Presentation\Controllers\SelectDealerData\SelectDealerDataController;
use App\Presentation\Controllers\SelectDealerReview\SelectDealerReviewController;
use App\Presentation\Controllers\SelectVehicleSpec\SelectVehicleSpecController;
use App\Presentation\Controllers\SelectReservationType\SelectReservationTypeController;
use App\Presentation\Controllers\SelectDealerSchedule\SelectDealerScheduleController;
use App\Presentation\Controllers\CreateReservation\CreateReservationController;
use App\Presentation\Controllers\CarImage\UploadCarImageController;

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
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/rooms', [RoomController::class, 'index']);
    Route::post('/rooms', [RoomController::class, 'store']);
    Route::get('/rooms/{roomId}', [RoomController::class, 'show']);

    Route::get('/rooms/{roomId}/messages', [MessageController::class, 'index']);
    Route::post('/rooms/{roomId}/messages', [MessageController::class, 'store']);
    Route::post('/rooms/{roomId}/messages/read', [MessageController::class, 'read']);
    Route::post('/rooms/{roomId}/typing', [MessageController::class, 'typing']);

    Route::prefix('EditMembers')->group(function () {
        Route::get('/Profile',  [EditMemberProfileController::class, 'show']);
        Route::put('/Profile',  [EditMemberProfileController::class, 'update']);
    });

    // メールアドレス変更
    Route::post('/EmailChange', [EmailChangeController::class, 'update']);

    Route::post('/ChangePassword', ChangePasswordController::class);
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

Route::post('/login', [AuthController::class, 'login']);

Route::prefix('Password')->group(function () {
    Route::post('/forgot', [PasswordResetController::class, 'forgot']);
    Route::post('/reset',  [PasswordResetController::class, 'reset']);
});

//　会員仮登録、本登録API
Route::prefix('Members')->group(function () {
    Route::post('/provisional', [MemberRegistrationController::class, 'provisional']);
    Route::post('/register',    [MemberRegistrationController::class, 'register']);
});

//　サイトのユーザーログイン
Route::prefix('MemberAuth')->group(function () {
    Route::post('/login',  [MemberAuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [MemberAuthController::class, 'logout']);
    });
});

// 車両データ数
Route::get('/SeriesStkCount', SeriesStkCountController::class);

// 検索条件取得
Route::prefix('SearchOptions')->group(function () {
    // 検索条件の色の取得
    Route::get('ColorOptions', [ColorOptionController::class, 'index']);
    // 検索条件の基本オプションの取得
    Route::get('BasicOptions', [BasicOptionController::class, 'index']);
    // 検索条件の詳細オプションの取得
    Route::get('DetailOptions', [DetailOptionController::class, 'index']);
    // 検索条件の基本装備の取得
    Route::get('EquipmentBasic', [EquipmentBasicController::class, 'index']);
    // 検索条件の安全装備の取得
    Route::get('EquipmentSafety', [EquipmentSafetyController::class, 'index']);
    // 検索条件の環境装備の取得
    Route::get('EquipmentEnv', [EquipmentEnvController::class, 'index']);
    // 検索条件のドレスアップ装備の取得
    Route::get('EquipmentDressup', [EquipmentDressupController::class, 'index']);
    // 検索条件のシートオプションの取得
    Route::get('SeatOption', [SeatOptionController::class, 'index']);
    // 検索条件のローン月々支払いの取得
    Route::get('LoanMonthlyOption', [LoanMonthlyOptionController::class, 'index']);
    //  検索条件のローン頭金の取得
    Route::get('LoanDownOption', [LoanDownOptionController::class, 'index']);
    //  検索条件の車のタイプの取得
    Route::get('CarTypeOption', [CarTypeOptionController::class, 'index']);
});
 
Route::get('SelectDealerData', SelectDealerDataController::class);
Route::get('SelectDealerReview', SelectDealerReviewController::class);
Route::get('SelectVehicleSpec', SelectVehicleSpecController::class);

Route::get('ReservationTypes', SelectReservationTypeController::class);
Route::get('DealerSchedules', SelectDealerScheduleController::class);
Route::post('Reservations', CreateReservationController::class);

// 認証済みディーラーのみアクセス可能
//Route::middleware('auth:sanctum')->group(function () {
    Route::post('/CarImages/upload', UploadCarImageController::class);
//});