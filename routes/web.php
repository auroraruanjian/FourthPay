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
    return view('index');
});

Auth::routes();


Route::get('/user/info', 'UserController@getInfo');

Route::any('/{controller}/{action}',function($controller,$action){
    $namespace = 'App\Http\Controllers\\';

    $className = $namespace . ucfirst($controller . "Controller");
    $tempObj = new $className();
    return call_user_func(array($tempObj, $action));
})->where([
    'controller'    => '^[A-Za-z]+$',
    'action'        => '^[A-Za-z]+$'
]);
