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
	Route::get('getAllStaticData','CommonController@getAllStaticData');
	Route::post('settings','CommonController@settings');
	Route::post('completeProfile','CommonController@completeProfileOrEditProfile');
	Route::post('change_password','CommonController@change_password');
	Route::post('logout','CommonController@logout');
	Route::post('get_all_event_dates','CommonController@get_all_event_dates');
	Route::post('sendFirebaseId','CommonController@sendFirebaseId');
	Route::post('sendAttachment','CommonController@sendAttachment');
	Route::post('getSettingsData','CommonController@getSettingsData');
	
	Route::post('sendOtp','CommonController@sendOtp');

////////////////////////////////////////////////
////// Doctor Api's
////////////////////////////////////////////////
	Route::get('doctorList','DoctorController@getList');
	Route::post('save_doctor_timing_for_availability','DoctorController@save_doctor_timing_for_availability');
	Route::post('getReviewList','DoctorController@getReviewList');
	Route::post('acceptReview','DoctorController@acceptReview');
	Route::post('getDoctorBySpecialityId','DoctorController@getDoctorBySpecialityId_FOR_PATIENT_SEARCH');

	Route::post('get_all_appointment_of_doctor','DoctorController@get_all_appointment_of_doctor');

	Route::post('get_all_appointment_of_doctor_by_date','DoctorController@get_all_appointment_of_doctor_by_date');
	Route::post('accept_or_reject_appointment','DoctorController@accept_or_reject_appointment');
	Route::post('reschedule_appointment_by_doctor','DoctorController@reschedule_appointment_by_doctor');
	Route::post('get_doctor_availability','DoctorController@get_doctor_availability');
	Route::post('get_doctor_available_time_slots','DoctorController@get_doctor_available_time_slots');
	Route::post('get_notification_list_for_doctor','DoctorController@get_notification_list');

	Route::post('updateRescheduledAppointmentByDoctor','DoctorController@accept_or_reject_appointment_by_doctor_rescheduled_by_patient');
	Route::post('cancel_appointment_by_doctor','DoctorController@cancel_appointment_by_doctor');
	Route::post('completeAppointmentByDoctor','DoctorController@completeAppointmentByDoctor');
	Route::post('transferAppointmentByDoctor','DoctorController@transferAppointmentByDoctor');
	Route::post('send_otp_at_email','DoctorController@send_otp_at_email');
	Route::post('verify_email_by_otp','DoctorController@verify_email_by_otp');
	Route::post('getDoctorRevenue','DoctorController@getDoctorRevenue');
	
	


////////////////////////////////////////////////
////// Doctor Api's END
////////////////////////////////////////////////



////////////////////////////////////////////////
////// PATIENT's Api's
////////////////////////////////////////////////
	Route::post('bookmark_UnBookMark_Doctor','PatientController@bookmark_UnBookMark_Doctor');
	Route::post('get_patient_bookmarks_doctors','PatientController@get_patient_bookmarks_doctors');
	Route::post('schedule_appointment_with_doctor','PatientController@schedule_appointment_with_doctor');

	Route::post('get_all_appointment_of_patient_by_date','PatientController@get_all_appointment_of_patient_by_date');

	Route::post('updateRescheduledAppointmentByPatient','PatientController@accept_or_reject_appointment_by_patient_rescheduled_by_doctor');
	Route::post('search_doctor_by_patient','PatientController@search_doctor');
	Route::post('get_notification_list_for_patient','PatientController@get_notification_list');
	Route::post('reschedule_appointment_by_patient','PatientController@reschedule_appointment_by_patient');
	Route::post('cancel_appointment_by_patient','PatientController@cancel_appointment_by_patient');
	Route::post('giveReviewToDoctor','PatientController@giveReviewToDoctor');


////////////////////////////////////////////////
////// PATIENT's Api's END
////////////////////////////////////////////////



Route::post('test',function(Request $request){
	$test=2;
	if ($test>=1) {
	  trigger_error("Value must be 1 or below");
	}
	/*$favcolor = "red";

	switch ($favcolor) {
	    case "red":
	        echo "Your favorite color is red!";
	        break;
	    case "blue":
	        echo "Your favorite color is blue!";
	        break;
	    case "green":
	        echo "Your favorite color is green!";
	        break;
	    default:
	        echo "Your favorite color is neither red, blue, nor green!";
	}*/
	dd();
	$password = $request->password;
 	// dd(Hash::make($password));
	$db = '$2y$10$Rs7AaHoYaL5sAIsXSXEZOuhVXuqLzrra2WboYZdHPTRbdjla13r/6'; 
 	dd(Hash::check($password,$db));
});

Route::post('testCase1','GauravController@testCase1');