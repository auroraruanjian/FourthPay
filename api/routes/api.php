<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});


Route::prefix('payment')->group(function () {
    // 支付
    Route::post('pay', 'PaymentController@pay');

    // 查询订单状态
    Route::post('query', 'PaymentController@query');

    // 查询订单状态
    Route::get('test', 'PaymentController@test');
});
