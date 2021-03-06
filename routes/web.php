<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

/**
 * Currencies routes
 */
Route::get('/pa/get-currencies', 'CurrencyController@getCurrencies');
Route::get('/pa/get-currency', 'CurrencyController@getCurrency');

/**
 * Bills methods
 */
Route::middleware(['auth'])->namespace('CRUDControllers')->group(function () {
    Route::get('/pa/bills-list', 'BillsController@getBills');

    Route::post('/pa/bills', 'BillsController@createBill');
    Route::put('/pa/bills', 'BillsController@setBill');
    Route::delete('/pa/bills', 'BillsController@deleteBill');

    Route::post('/pa/bills/transfer', 'BillsController@MoneyTransaction');
});

/**
 * Funds methods
 */
Route::middleware(['auth'])->namespace('CRUDControllers')->group(function () {
    Route::get('/pa/funds-list', 'FundsController@getFunds');

    Route::post('/pa/funds', 'FundsController@createFund');
    Route::put('/pa/funds', 'FundsController@setFund');
    Route::delete('/pa/funds', 'FundsController@deleteFund');
});


/**
 * Categories methods
 */
Route::middleware(['auth'])->namespace('CRUDControllers')->group(function () {
    Route::get('/pa/categories-list', 'CategoriesController@getCategories');

    Route::post('/pa/categories', 'CategoriesController@createCategory');
    Route::put('/pa/categories', 'CategoriesController@setCategory');
    Route::delete('/pa/categories', 'CategoriesController@deleteCategory');
});

/**
 * Event methods
 */
Route::middleware(['auth'])->namespace('CRUDControllers')->group(function () {
    Route::get('/pa/event-list', 'EventController@getEvents');

    Route::post('/pa/events', 'EventController@createEvent');
    Route::put('/pa/events', 'EventController@setEvent');
    Route::delete('/pa/events', 'EventController@deleteEvent');
});


Route::middleware(['auth'])->group(function () {
    /**
     * Personal Area routes
     */

    Route::get('/pa', 'PrivateAreaController@index')->name('privateArea');

    /**
     * web-push register
     */
    Route::post('/callback/push-on', 'CallBackController@pushOn');
    Route::post('/callback/push-off', 'CallBackController@pushOff');

    /**
     * Analytics methods
     */
    Route::get('/analytics', 'AnalyticsController@index');
    Route::get('/analitycs/dynamic-accumulate', 'AnalyticsController@getAccumulateDynamic');
    Route::post('/analytics/get-chart-data', 'AnalyticsController@getDataToChartAnalytics');

    Route::post('/analytics/create', 'AnalyticsController@createQueueToAnalyticsData');

    /**
     * QRCodeScannerController
     */
    Route::get('/qr-code-scanner', 'QrCodeScannerController@index');
    Route::post('/qr-code-scanner/send-check', 'QrCodeScannerController@sendCheck');

    Route::get('/settings', 'SettingsController@index');
    Route::get('/settings/{any}', 'SettingsController@index')->where('any', '.*');
});