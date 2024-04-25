<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CouponAddController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\flight\AirlineController;
use App\Http\Controllers\flight\AirlineNumberController;
use App\Http\Controllers\flight\AirlineSeatController;
use App\Http\Controllers\flight\AirportController;
use App\Http\Controllers\flight\FlightCategoryController;
use App\Http\Controllers\flight\FlightClassController;
use App\Http\Controllers\flight\FlightTicketController;
use App\Http\Controllers\flight\FlightTicketPriceController;
use App\Http\Controllers\flight\FlightTransactionController;
use App\Http\Controllers\flight\FlightTripController;
use App\Http\Controllers\flight\MealController;
use App\Http\Controllers\flight\PassengerTypeController;
use App\Http\Controllers\flight\StatusController;
use App\Http\Controllers\flight\SystemController;
use App\Http\Controllers\flight\TicketStatusController;
use App\Http\Controllers\flight\TripStatusController;
use App\Http\Controllers\flight\WeightController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\LevelDiscountController;
use App\Http\Controllers\PaymentTypeController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentFormController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
    Route::apiResource('users', UserController::class);
    Route::post('logout', [UserController::class, "logout"]);
    Route::post('changepassword', [UserController::class, "changePassword"]);
    Route::get('get-user-detail', [UserController::class, "getUser"]);
    Route::post('user-change-language', [UserController::class, 'changeLanguage']);
    Route::apiResource('levels', LevelController::class);
    Route::apiResource('countries', CountryController::class);
    Route::apiResource('currencies', CurrencyController::class);
    Route::apiResource('cities', CityController::class);
    Route::apiResource('level-discount', LevelDiscountController::class);
    Route::get('user-level-discount', [LevelDiscountController::class, "getUserLevelDiscount"]);
    Route::apiResource('rating', RatingController::class);
    Route::apiResource('payment-type', PaymentTypeController::class);
    Route::apiResource('coupon', CouponController::class);
    Route::post('coupon-add', [CouponAddController::class, 'addCoupon']);
    Route::get('user-coupon', [CouponController::class, 'userCoupon']);
    Route::apiResource('payment-form',PaymentFormController::class);
    Route::apiResource('languages', LanguageController::class);


    Route::prefix('flight')->name('flight.')->group(function () {
        Route::apiResource('trip-status', TripStatusController::class);
        Route::apiResource('system', SystemController::class);
        Route::apiResource('airport', AirportController::class);
        Route::apiResource('flight-category', FlightCategoryController::class);
        Route::apiResource('status', StatusController::class);
        Route::apiResource('flight-trip', FlightTripController::class);
        Route::apiResource('flight-ticket-price', FlightTicketPriceController::class);
        Route::apiResource('airline', AirlineController::class);
        Route::apiResource('flight-class', FlightClassController::class);
        Route::apiResource('airline-number', AirlineNumberController::class);
        Route::apiResource('airline-seat', AirlineSeatController::class);
        Route::apiResource('flight-ticket', FlightTicketController::class);
        Route::apiResource('ticket-status', TicketStatusController::class);
        Route::apiResource('meal', MealController::class);
        Route::apiResource('weight', WeightController::class);
        Route::get('flight-ticket-count', [FlightTicketController::class, 'ticketCount']);
        Route::apiResource('passenger-type', PassengerTypeController::class);
        Route::apiResource('flight-transaction', FlightTransactionController::class);
        Route::get('search-flight-ticket', [FlightTransactionController::class, 'searchFlightTicket']);
        Route::get('search-flight-seat', [FlightTransactionController::class, 'searchFlightSeat']);
        Route::get('check-coupon', [FlightTransactionController::class, 'checkCoupon']);
        Route::get('user-flight-transaction', [FlightTransactionController::class, 'getUserFlightTransaction']);
        Route::get('user-ticket-calendar', [CalendarController::class, 'getUserTicketCalendar']);
    });
});

Route::post('login', [UserController::class, "login"]);
Route::post('register', [UserController::class, "register"]);
