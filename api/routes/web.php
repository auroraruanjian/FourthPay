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

Route::prefix('payment')->group(function () {
    // 支付
    Route::get('pay', 'PaymentController@pay');

    // 查询订单状态
    Route::post('query', 'PaymentController@query');

    // 查询订单状态
    Route::get('test', 'PaymentController@test');
});
