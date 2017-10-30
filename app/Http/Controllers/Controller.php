<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use DB;
use Mail;
use Log;
use Response;
use Session;
use \Carbon\Carbon;
use App\Category;
use App\Day;
use App\DoctorAvailability;
use App\DoctorMotherlanguage;
use App\DoctorQualification;
use App\Faq;
use App\MotherLanguage;
use App\Otp;
use App\PatientBookmark;
use App\Qualification;
use App\TimeSlot;
use App\User;
use App\UserDetail;
use Hash;
use Auth;
use Exception;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

   public function getUserDetail($data){
   	// dd($data);

   	$result = [];
   	$qualification = [];
   	$DoctorMotherlanguage = [];
   	
   	if($data->user_type == 1){ // doctor
   		if(isset(($data['qualification']))) {
	   		foreach ($data['qualification'] as $key => $value) {
	   			$QualificationDetail = Qualification::Where(['id' => $value->qualification_id])->first();
	   			$qualification[]=[
	   				'id' => $value->id,
	   				'user_id' => $value->user_id,
	   				'qualification_id' => $value->qualification_id,
	   				'qualification_name' => $QualificationDetail['name']
	   			];
	   		}
	   	}
	   	if(isset(($data['mother_language']))) {
	   		foreach ($data['mother_language'] as $key => $value) {
	   			$DoctorMotherlanguageDetail = MotherLanguage::Where(['id' => $value->mother_language_id])->first();
	   			$DoctorMotherlanguage[]=[
	   				'id' => $value->id,
	   				'user_id' => $value->user_id,
	   				'mother_language_id' => $value->mother_language_id,
	   				'qualification_name' => $DoctorMotherlanguageDetail['name']
	   			];
	   		}
	   	}
   		$doctor_availabilities = DoctorAvailability::Where(['doctor_id' => $data->id])->get();
	   	$result = [
	   		'UserIdentificationType' => "Doctor",
	   		'id' => $data['id'],
	   		'name' => $data['name'],
	   		'email' => $data['email'],
	   		'country_code' => $data['country_code'],
	   		'mobile' => $data['mobile'],
	   		'profile_image' => $data['profile_image'],
	   		'speciality_id' => $data['speciality_id'],
	   		'experience' => $data['experience'],
	   		'working_place' => $data['working_place'],
	   		'latitude' => $data['latitude'],
	   		'longitude' => $data['longitude'],
	   		'about_me' => $data['about_me'],
	   		'remember_token' => $data['remember_token'],
	   		'device_token' => $data['device_token'],
	   		'device_type' => $data['device_type'],
	   		'user_type' => $data['user_type'],
	   		'medical_licence_number' => $data['medical_licence_number'],
	   		'issuing_country' => $data['issuing_country'],
	   		
	   		'status' => $data['status'],
	   		'profile_status' => $data['profile_status'],
	   		'notification' => $data['notification'],
	   		'language' => $data['language'],
	   		// 'created_at' => $data['created_at'],
	   		// 'updated_at' => $data['updated_at'],
	   		'speciality' => $data['speciality'],
	   		'otp_detail' => $data['Otp_detail'],
	   		'qualification' => $qualification,
	   		'mother_language' => $DoctorMotherlanguage,
	   		'doctor_availabilities' => $doctor_availabilities
	   	];
	   	return $result;
   	}

   	if($data->user_type == 2){ // patient
   		$result = [
   			'UserIdentificationType' => "Patient",
	   		'id' => $data['id'],
	   		'name' => $data['name'],
	   		'email' => $data['email'],
	   		'country_code' => $data['country_code'],
	   		'mobile' => $data['mobile'],
	   		'profile_image' => $data['profile_image'],
	   		// 'speciality_id' => $data['speciality_id'],
	   		'remember_token' => $data['remember_token'],
	   		'device_token' => $data['device_token'],
	   		'device_type' => $data['device_type'],
	   		'user_type' => $data['user_type'],
	   		
	   		'status' => $data['status'],
	   		'profile_status' => $data['profile_status'],
	   		'notification' => $data['notification'],
	   		'language' => $data['language'],
	   		// 'created_at' => $data['created_at'],
	   		// 'updated_at' => $data['updated_at'],
	   		// 'speciality' => $data['speciality'],
	   		'otp_detail' => $data['Otp_detail'],
	   	];
	   	return $result;
   	}
   	
   }
}
