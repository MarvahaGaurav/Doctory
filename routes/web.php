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
	Route::get('doctor_profile/{doctor_id}','AdminController@doctor_profile');
	Route::match(['get','post'],'login', 'AdminController@index');
	Route::match(['get'],'logout', 'AdminController@logout');
	Route::get('dashboard','AdminController@dashboard');
	Route::get('profile','AdminController@profile');
	Route::get('approve_list','AdminController@approved_list');
	Route::get('pending_list','AdminController@pending_list');

	Route::match(['get','post'],'speciality_management','AdminController@speciality_management');
	Route::get('delete_speciality/{speciality_id}','AdminController@delete_speciality');

	Route::match(['get','post'],'speciality/edit/{speciality_id}','AdminController@edit_speciality_management');

	Route::match(['get','post'],'add_qualification','AdminController@addQualification');
	Route::match(['get','post'],'qualification_edit/{qualification_id}','AdminController@qualification_edit');
	Route::get('qualification_delete/{qualification_id}','AdminController@qualification_delete');

	Route::match(['get','post'],'add_mother_language','AdminController@add_mother_language');
	Route::get('mother_language_delete/{mother_language_id}','AdminController@mother_language_delete');

	Route::get('patient_list','AdminController@patient_list');
	Route::get('approve_doctor/{doctor_id}','AdminController@approve_doctor');
	Route::get('block_patient/{patient_id}/{status}','AdminController@block_patient');

});

