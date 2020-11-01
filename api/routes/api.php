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
    Route::get('pay', 'PaymentController@pay');

    // 用户支付异步返回
    Route::match(['get', 'post'], 'callback/{channel_detail_id}', 'PaymentController@callback');

    // 用户支付同步返回
    Route::get('callback_view/{channel_detail_id}', 'PaymentController@callback_view');

    // 查询订单状态
    Route::post('query', 'PaymentController@query');

    // 查询订单状态
    Route::get('test', 'PaymentController@test');
});
