<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use DB;
use Mail;
use Illuminate\Validation\Rule;
use Log;
use Response;
use Session;
use \Carbon\Carbon;
use App\User;
use App\Otp;
use App\UserDetail;
use App\Category;
use App\TimeSlot;
use App\Day;
use App\MotherLanguage;
use App\PatientBookmark;
use App\DoctorAvailability;
use App\Qualification;
use App\DoctorQualification;
use App\Appointment;
use Hash;
use Auth;
use Exception;

class PatientController extends Controller
{
 	public function bookmark_UnBookMark_Doctor(Request $request){
		Log::info('----------------------PatientController--------------------------bookmark_UnBookMark_Doctor'.print_r($request->all(),True));
 		
		$accessToken = $request->header('accessToken');
		$patient_id = $request->patient_id;
		$doctor_id = $request->doctor_id;
		$key = $request->key; // 1 for bookmark or 0 for un bookmark

    	if( !empty( $accessToken ) ) {
    		$UserDetail = User::where(['remember_token'=>$accessToken])->first();
    		if(count($UserDetail)){
    			$validations = [
					'patient_id' => 'required|numeric',
					'doctor_id' => 'required|numeric',
					'key' => 'required|numeric'
		    	];
		    	$validator = Validator::make($request->all(),$validations);
		    	if($validator->fails()){
		    		$response = [
						'message' => $validator->errors($validator)->first()
					];
					return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
		    	}else{
		    		$patient_detail = User::where(['id'=>$patient_id,'user_type'=>2])->first();
		    		$doctor_detail = User::where(['id'=>$doctor_id,'user_type'=>1])->first();
		    		if($doctor_detail && $patient_detail ){
		    			$alreadyBookMarked = PatientBookmark::where(['doctor_id' => $doctor_id , 'patient_id' => $patient_id])->first();
		    			if($key == 1 ){
		    				if(!$alreadyBookMarked){
		    					$PatientBookmark = new PatientBookmark;
				    			$PatientBookmark->patient_id = $patient_id;
				    			$PatientBookmark->doctor_id = $doctor_id;
				    			$PatientBookmark->save();
		    				}
		    			}
		    			if($key == 0 && count($alreadyBookMarked)){
		    				PatientBookmark::where(['doctor_id' => $doctor_id , 'patient_id' => $patient_id])->delete();
		    			}
		    			$Response = [
							'message'  => trans('messages.success.success'),
						];
				      return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
		    		}else {
				    	$Response = [
							'message'  => trans('messages.invalid.request'),
						];
				      return Response::json( $Response , __('messages.statusCode.SHOW_ERROR_MESSAGE') );
				   }
		    	}
    		}else{
    			$Response = [
    			  'message'  => trans('messages.invalid.detail'),
    			];
        		return Response::json( $Response , trans('messages.statusCode.INVALID_ACCESS_TOKEN') );
    		}
    	}else {
	    	$Response = [
				'message'  => trans('messages.required.accessToken'),
			];
	      return Response::json( $Response , __('messages.statusCode.SHOW_ERROR_MESSAGE') );
	   }
 	}

 	public function get_patient_bookmarks_doctors(Request $request){
 		Log::info('------------------PatientController------------get_patient_bookmarks_doctors');
 		$accessToken = $request->header('accessToken');
 		$result = [];
		if( !empty( $accessToken ) ) {
			$UserDetail = User::where(['remember_token'=>$accessToken])->first();
    		if(count($UserDetail)){
				Log::info('UserDetail'.print_r($UserDetail,True));
    			if($UserDetail->user_type == 2){
    				$PatientBookmark = PatientBookmark::where(['patient_id' => $UserDetail->id])->get();
    				$User = new User;
    				foreach ($PatientBookmark as $key => $value) {
    					// dd($value->doctor_id);
				    	$result[] = $this->getUserDetail($User->getUserDetail($value->doctor_id));
    				}
    				$Response = [
						'message'  => trans('messages.success.success'),
						'response' => $result
					];
			      return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
    			}else{
			    	$Response = [
						'message'  => trans('messages.invalid.request'),
					];
			      return Response::json( $Response , __('messages.statusCode.SHOW_ERROR_MESSAGE'));
    			}
			}else{
    			$Response = [
    			  'message'  => trans('messages.invalid.detail'),
    			];
        		return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE') );
    		}
		}else {
	    	$Response = [
				'message'  => trans('messages.required.accessToken'),
			];
	      return Response::json( $Response , __('messages.statusCode.SHOW_ERROR_MESSAGE') );
		}
 	}

 	public function schedule_appointment_with_doctor(Request $request){
		Log::info('----------------------PatientController--------------------------bookmark_UnBookMark_Doctor'.print_r($request->all(),True));
 		$accessToken = $request->header('accessToken');
 		$patient_id = $request->patient_id;
 		$patient_age = $request->patient_age;
 		$patient_gender = $request->patient_gender;
 		$question = $request->question;
 		$previous_illness_desc = $request->previous_illness_desc;
		$doctor_id = $request->doctor_id;
		$time_slot_id = $request->time_slot_id;
		$day_id = $request->day_id;

 		if( !empty( $accessToken ) ) {
 			$UserDetail = User::where(['remember_token'=>$accessToken])->first();
 			if(count($UserDetail)){
 				if($UserDetail->user_type == 2){ // for Patient Only
 					$validations = [
						'patient_id' => 'required|numeric',
						'doctor_id' => 'required|numeric',
						'time_slot_id' => 'required|numeric',
						'day_id' => 'required|numeric',
			    	];
			    	$validator = Validator::make($request->all(),$validations);
			    	if($validator->fails()){
			    		$response = [
							'message' => $validator->errors($validator)->first()
						];
						return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
		    		}else{
		    			$AppointmentData =[
		    				'patient_id' => $patient_id,
		    				'patient_age' => $patient_age,
		    				'patient_gender' => $patient_gender,
		    				'question' => $question,
		    				'previous_illness_desc' => $previous_illness_desc,
		    				'doctor_id' => $doctor_id,
		    				'time_slot_id' => $time_slot_id,
		    				'day_id' => $day_id,
		    			]; 

		    			$Already_Busy_Time_Slot_With_Other_Patient = Appointment::where(['doctor_id' => $doctor_id,'time_slot_id' => $time_slot_id,'day_id' => $day_id])
		    			->where('patient_id','<>',$patient_id)
		    			->first();
		    			// dd($Already_Busy_Time_Slot_With_Other_Patient);
		    			if(!$Already_Busy_Time_Slot_With_Other_Patient){
		    				$already_booked = Appointment::where([
			    				'patient_id' => $patient_id,
			    				'doctor_id' => $doctor_id,
			    				'time_slot_id' => $time_slot_id,
			    				'day_id' => $day_id
			    				])->first();
		    				if(!$already_booked){
			    				$appontmentId = Appointment::insertGetId($AppointmentData);
			    				$result = Appointment::find($appontmentId);

				    			$Response = [
									'message'  => trans('messages.success.appointment_scheduled'),
									'response' => $result
								];
						      return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
			    			}else{
			    				$Response = [
									'message'  => trans('messages.Patient_Already_Booked_appointment'),
								];
						      return Response::json( $Response , __('messages.statusCode.ALREADY_EXIST') );
			    			}
		    			}else{
		    				$Response = [
								'message'  => trans('messages.Already_Busy_Time_Slot_With_Other_Patient')
							];
					      return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
		    			}
		    		}
 				}else{
 					$response=[
						'message' => trans('messages.invalid.request'),
		      	];
		     		return Response::json($response,__('messages.statusCode.SHOW_ERROR_MESSAGE'));
 				}
 			}else{
    			$Response = [
    			  'message'  => trans('messages.invalid.detail'),
    			];
        		return Response::json( $Response , trans('messages.statusCode.INVALID_ACCESS_TOKEN') );
    		}
 		}else {
	    	$Response = [
				'message'  => trans('messages.required.accessToken'),
			];
	      return Response::json( $Response , __('messages.statusCode.SHOW_ERROR_MESSAGE') );
	   }
 	}

 	public function get_all_appointment_of_patient_by_date(Request $request){
 		$accessToken = $request->header('accessToken');
 		$date = date('Y-m-d',strtotime($request->date));
	   $page_number = $request->page_number;
 		// dd($date);
 		if( !empty( $accessToken ) ) {
 			$UserDetail = User::where(['remember_token'=>$accessToken])->first();
 			if(count($UserDetail)){
 				if($UserDetail->user_type == 2){
	 				$validations = [
						'date' => 'required',
						'page_number' => 'required|numeric'
			    	];
			    	$validator = Validator::make($request->all(),$validations);
			    	if($validator->fails()){
			    		$response = [
							'message' => $validator->errors($validator)->first()
						];
						return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
		    		}else{
		    			$result = Appointment::get_all_appointment_of_patient_by_date($date,$UserDetail->id,$page_number);
		    			$Response = [
							'message'  => trans('messages.success.success'),
							'response' => $result
						];
				      return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
		    		}
		    	}else{
		    		$Response = [
						'message'  => trans('messages.invalid.request'),
					];
			      return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
		    	}
 			}else{
    			$Response = [
    			  'message'  => trans('messages.invalid.detail'),
    			];
        		return Response::json( $Response , trans('messages.statusCode.INVALID_ACCESS_TOKEN') );
    		}
		}else {
	    	$Response = [
				'message'  => trans('messages.required.accessToken'),
			];
	      return Response::json( $Response , __('messages.statusCode.SHOW_ERROR_MESSAGE') );
	   }
 	}

 	public function accept_or_reject_appointment_by_patient_rescheduled_by_doctor(Request $request){
 		$accessToken = $request->header('accessToken');
 		$appointment_id = $request->appointment_id;
 		$doctor_id = $request->doctor_id;
 		$time_slot_id = $request->time_slot_id;
		$day_id = $request->day_id;
 		if( !empty( $accessToken ) ) {
 			$UserDetail = User::where(['remember_token'=>$accessToken])->first();
 			if(count($UserDetail)){
 				if($UserDetail->user_type == 2){ // for Patient Only
 					$validations = [
						'appointment_id' => 'required|numeric',
						'doctor_id' => 'required|numeric',
						'time_slot_id' => 'required|numeric',
						'day_id' => 'required|numeric',
			    	];
			    	$validator = Validator::make($request->all(),$validations);
			    	if($validator->fails()){
			    		$response = [
							'message' => $validator->errors($validator)->first()
						];
						return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
		    		}else{
		    			$today = Carbon::now();
		    			$dayNumber = $today->dayOfWeek+1;
		    			dd($today->hour);
		    			dd($today->minute);
		    		}
		    	}else{
 					$response=[
						'message' => trans('messages.invalid.request'),
		      	];
		     		return Response::json($response,__('messages.statusCode.SHOW_ERROR_MESSAGE'));
 				}
		   }else{
    			$Response = [
    			  'message'  => trans('messages.invalid.detail'),
    			];
        		return Response::json( $Response , trans('messages.statusCode.INVALID_ACCESS_TOKEN') );
    		}
		}else {
	    	$Response = [
				'message'  => trans('messages.required.accessToken'),
			];
	      return Response::json( $Response , __('messages.statusCode.SHOW_ERROR_MESSAGE') );
	   }
 	}
}
