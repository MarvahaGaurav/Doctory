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
Route::post('resendOtp','CommonController@resendOtp');
Route::post('login','CommonController@login');
Route::post('forgetPassword','CommonController@forgetPassword');
Route::post('resetPassword','CommonController@resetPassword');
Route::post('changeMobileNumber','CommonController@changeMobileNumber');
Route::get('getSpeciality','CategoryController@getCategoryList');
Route::get('getQualification','QualificationController@getQualificationList');
// Route::post('getSubCategoryUnderCat','CategoryController@getSubCategory');
// Route::get('getSubCatAndCat','CategoryController@getSubCatAndCat');
Route::get('getAllStaticData','CommonController@getAllStaticData');

Route::post('settings','CommonController@settings');

Route::post('completeProfile','CommonController@completeProfileOrEditProfile');

Route::post('logout','CommonController@logout');









////////////////////////////////////////////////
////// Doctor Api's
////////////////////////////////////////////////
	Route::get('doctorList','DoctorController@getList');

	Route::post('save_doctor_timing_for_availability','DoctorController@save_doctor_timing_for_availability');

	Route::post('get_review_rating_at_doctor_app','DoctorController@get_review_rating_at_doctor_app');
	
	Route::post('change_status_of_reviews_from_doctor_app','DoctorController@change_status_of_reviews_from_doctor_app');


	Route::post('getDoctorBySpecialityId','DoctorController@getDoctorBySpecialityId_FOR_PATIENT_SEARCH');



////////////////////////////////////////////////
////// Doctor Api's END
////////////////////////////////////////////////







////////////////////////////////////////////////
////// PATIENT's Api's
////////////////////////////////////////////////

	Route::post('bookmark_UnBookMark_Doctor','PatientController@bookmark_UnBookMark_Doctor');

	Route::post('get_patient_bookmarks_doctors','PatientController@get_patient_bookmarks_doctors');
	
	Route::post('schedule_appointment_with_doctor','PatientController@schedule_appointment_with_doctor');


	
////////////////////////////////////////////////
////// PATIENT's Api's END
////////////////////////////////////////////////



Route::post('test',function(Request $request){
	$password = $request->password;
	// dd($password);
 	// dd(Hash::make($password));
	$db = '$2y$10$Rs7AaHoYaL5sAIsXSXEZOuhVXuqLzrra2WboYZdHPTRbdjla13r/6'; // gaurav
	// dd($db);
 	dd(Hash::check($password,$db));
});