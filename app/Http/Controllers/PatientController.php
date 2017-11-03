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
 		Log::info('------------------PatientController------------get_patient_bookmarks_doctors'.print_r($request->all(),True));
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
		$appointment_date = $request->appointment_date;
 		if( !empty( $accessToken ) ) {
 			$UserDetail = User::where(['remember_token'=>$accessToken])->first();
 			if(count($UserDetail)){
 				if($UserDetail->user_type == 2){ // for Patient Only
 					$validations = [
						'patient_id' => 'required|numeric',
						'doctor_id' => 'required|numeric',
						'time_slot_id' => 'required|numeric',
						'day_id' => 'required|numeric',
						'appointment_date' => 'required|date_format:"Y-m-d"'
			    	];
			    	$validator = Validator::make($request->all(),$validations);
			    	if($validator->fails()){
			    		$response = [
							'message' => $validator->errors($validator)->first()
						];
						return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
		    		}else{
		    			if($UserDetail->id == $patient_id){
			    			$appointment_date_from_user = Carbon::parse($appointment_date);
			    			if(!$appointment_date_from_user->isYesterday()){
				    			$AppointmentData =[
				    				'patient_id' => $patient_id,
				    				'patient_age' => $patient_age,
				    				'patient_gender' => $patient_gender,
				    				'question' => $question,
				    				'previous_illness_desc' => $previous_illness_desc,
				    				'doctor_id' => $doctor_id,
				    				'time_slot_id' => $time_slot_id,
				    				'day_id' => $day_id,
				    				'appointment_date' => $appointment_date
				    			]; 

				    			$check_doctor_availability = DoctorAvailability::where(['doctor_id' => $doctor_id,'day_id' => $day_id ,'time_slot_id' => $time_slot_id])->first();
				    			if($check_doctor_availability){
					    			$Already_Busy_Time_Slot_With_Other_Patient = Appointment::where(['doctor_id' => $doctor_id,'time_slot_id' => $time_slot_id,'day_id' => $day_id])
					    			->where('patient_id','<>',$patient_id)
					    			->where('appointment_date','=',$appointment_date_from_user)
					    			->first();
					    			if(!$Already_Busy_Time_Slot_With_Other_Patient){
					    				$already_booked = Appointment::where([
						    				'patient_id' => $patient_id,
						    				'doctor_id' => $doctor_id,
						    				'time_slot_id' => $time_slot_id,
						    				'day_id' => $day_id,
						    				'appointment_date' => $appointment_date_from_user
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
					    		}else{
					    			$response = [
										'message' => __('messages.invalid.doctor_not_available_at_this_time_slot')
									];
									return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
					    		}
				    		}else{
				    			$response = [
									'message' => __('messages.invalid.appointment_date')
								];
								return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
				    		}
				    	}else{
			    			$Response = [
			    			  'message'  => trans('messages.invalid.request'),
			    			];
			        		return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE') );
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
 		Log::info('----------------------PatientController--------------------------get_all_appointment_of_patient_by_date'.print_r($request->all(),True));
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
 		Log::info('----------------------PatientController--------------------------accept_or_reject_appointment_by_patient_rescheduled_by_doctor'.print_r($request->all(),True));
 		$accessToken = $request->header('accessToken');
 		$appointment_id = $request->appointment_id;
 		$doctor_id = $request->doctor_id;
 		$time_slot_id = $request->time_slot_id;
		$day_id = $request->day_id;
		$accept_or_reject = $request->accept_or_reject;

 		if( !empty( $accessToken ) ) {
 			$UserDetail = User::where(['remember_token'=>$accessToken])->first();
 			if(count($UserDetail)){
 				if($UserDetail->user_type == 2){ // for Patient Only
 					$validations = [
						'appointment_id' => 'required|numeric',
						'doctor_id' => 'required|numeric',
						'time_slot_id' => 'required|numeric',
						'day_id' => 'required|numeric',
						'accept_or_reject' => 'required|alpha',
			    	];
			    	$validator = Validator::make($request->all(),$validations);
			    	if($validator->fails()){
			    		$response = [
							'message' => $validator->errors($validator)->first()
						];
						return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
		    		}else{
		    			$AppointmentDetail = Appointment::find($appointment_id);
		    			// dd($AppointmentDetail);
		    			if($AppointmentDetail && $AppointmentDetail->doctor_id == $doctor_id && $AppointmentDetail->rescheduled_by_doctor == 1)
		    			{
		    				$appointmentDateInDb = Carbon::parse($AppointmentDetail->appointment_date)->format('Y-m-d');
		    				if($appointmentDateInDb >= Carbon::now()->format('Y-m-d')){
			    				$Time_slot_detail = TimeSlot::find($AppointmentDetail->time_slot_id);
								$Appointment_TimeSlot_StartTime = $Time_slot_detail->start_time;
								$Appointment_TimeSlot_EndTime = $Time_slot_detail->end_time;
								if( Carbon::parse(strtoupper(($Appointment_TimeSlot_StartTime)))->format('g:i A') > Carbon::now()->format('g:i A') )
								{
                                    $AppointmentDetail->status_of_appointment = $accept_or_reject;
                                    $AppointmentDetail->save();
                                    if($accept_or_reject == 'Accepted'){
                                        $Response = [
                                            'message'  => trans('messages.success.appointment_accepted'),
                                        ];
                                        return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
                                    }
                                    if($accept_or_reject == 'Rejected'){
                                        $Response = [
                                            'message'  => trans('messages.success.appointment_rejected'),
                                        ];
                                        return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
                                    }
                                }else{
                                    $AppointmentDetail->status_of_appointment = "Expired";
                                    $AppointmentDetail->save();
                                    $response = [
                                        'message' => __('messages.invalid.appointment_expired')
                                    ];
                                    return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
                                }
			    			}else{
								$response = [
								   'message' => __('messages.invalid.appointment_expired')
								];
								return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
							}	
		    				
			    		}else{
			    			$response = [
								'message' => __('messages.success.NO_DATA_FOUND')
							];
							return response()->json($response,trans('messages.statusCode.NO_DATA_FOUND'));
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

 	public function search_doctor(Request $request){
 		Log::info('------------------PatientController------------search_doctor');
 		$accessToken = $request->header('accessToken');
 		$name = $request->name;
 		if( !empty( $accessToken ) ) {
			$UserDetail = User::where(['remember_token'=>$accessToken])->first();
    		if(count($UserDetail)){
				Log::info('UserDetail'.print_r($UserDetail,True));
    			if($UserDetail->user_type == 2){
    				$validations = [
						'name' => 'required',
			    	];
			    	$validator = Validator::make($request->all(),$validations);
			    	if($validator->fails()){
			    		$response = [
							'message' => $validator->errors($validator)->first()
						];
						return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
		    		}else{
		    			$query = [
		    				'user_type' => 1,
		    				'profile_status' => 1,
		    				'name' => $name
		    			];
		    			$doctor_detail = User::get_doctor_by_search_query($query);
		    			// dd(count($doctor_detail));
		    			$result = [];
		    			foreach ($doctor_detail as $key => $value) {
		    				$result[] = $this->getUserDetail($value);
		    			}
		    			$Response = [
							'message'  => trans('messages.success.success'),
							'response' => $result
						];
				      return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE'));
		    		}
    			}else{
			    	$Response = [
						'message'  => trans('messages.invalid.request'),
					];
			      return Response::json( $Response , __('messages.statusCode.SHOW_ERROR_MESSAGE'));
    			}
    		}else{
    			$Response = [
    			  'message'  => trans('messages.invalid.credentials'),
    			];
        		return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE') );
    		}
    	}else {
	    	$Response = [
				'message'  => trans('messages.required.accessToken')
			];
	      return Response::json( $Response , __('messages.statusCode.SHOW_ERROR_MESSAGE') );
		}
 	}
}
