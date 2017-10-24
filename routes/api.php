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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('signUp','CommonController@signUp');
Route::post('otpVerify','CommonController@otpVerify');
Route::post('login','CommonController@login');







Route::post('test',function(Request $request){
	$password = $request->password;
	// dd($password);
 	// dd(Hash::make($password));
	$db = '$2y$10$Rs7AaHoYaL5sAIsXSXEZOuhVXuqLzrra2WboYZdHPTRbdjla13r/6'; // gaurav
	// dd($db);
 	dd(Hash::check($password,$db));
});