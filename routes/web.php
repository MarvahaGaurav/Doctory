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

Route::group(['prefix' => 'Admin'],function(){
	Route::match(['get','post'],'login', 'AdminController@index');
	Route::match(['get'],'logout', 'AdminController@logout');
	Route::get('dashboard','AdminController@dashboard');
	Route::get('profile','AdminController@profile');
	Route::get('approve_list','AdminController@approve_list');
	Route::get('pending_list','AdminController@pending_list');
	Route::match(['get','post'],'speciality_management','AdminController@speciality_management');
	Route::get('add_qualification','AdminController@addQualification');
	Route::get('add_language','AdminController@add_language');
	Route::get('patient_list','AdminController@patient_list');


});

