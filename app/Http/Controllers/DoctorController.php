<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use DB;
use Mail;
use Log;
use Response;
use Session;
use \Carbon\Carbon;
use App\User;
use App\Otp;
use App\UserDetail;
use App\Review;
use App\TimeSlot;
use App\DoctorAvailability;
use App\PatientBookmark;
use App\Category;
use App\Appointment;
use Hash;
use Auth;
use Exception;


class DoctorController extends Controller
{
    public function getList(Request $request){
        Log::info('----------------------DoctorController--------------------------getList'.print_r($request->all(),True));
        $query = [
    		'status' => 1,
    		'user_type' => 1
    	];
    	$list = User::getUserList($query);
        $result = [];
        $doctor_availabilities = [];
        // dd(count($list));
        foreach ($list as $key => $value) {
            $doctor_availabilities = DoctorAvailability::Where(['doctor_id' => $value->id])->get();
            $result[] = [
                'id' => $value->id,
                'name' => $value->name,
                'email' => $value->email,
                'country_code' => $value->country_code,
                'mobile' => $value->mobile,
                'profile_image' => $value->profile_image,
                'speciality_id' => $value->speciality_id,
                'experience' => $value->experience,
                'working_place' => $value->working_place,
                'latitude' => $value->latitude,
                'longitude' => $value->longitude,
                'about_me' => $value->about_me,
                'remember_token' => $value->remember_token,
                'device_token' => $value->device_token,
                'device_type' => $value->device_type,
                'user_type' => $value->user_type,
                'profile_status' => $value->profile_status,
                'notification' => $value->notification,
                'language' => $value->language,
                'doctor_availabilities' => $doctor_availabilities
            ];
        }
    	$response = [
    		'message' => __('messages.success.success'),
    		'response' => $result
    	];
    	return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
    }

    // at search by category this api will hit only every time
    public function getDoctorBySpecialityId_FOR_PATIENT_SEARCH(Request $request){
        Log::info('----------------------DoctorController--------------------------getDoctorBySpecialityId_FOR_PATIENT_SEARCH'.print_r($request->all(),True));

        $accessToken =  $request->header('accessToken');
        $speciality_id =  $request->speciality_id;
        if( !empty( $accessToken ) ) {
            $PATIENT_DETAIL = User::Where(['remember_token' => $accessToken])->first();
            if(count($PATIENT_DETAIL)){
                $validations = [
                    'speciality_id' => 'required',
                ];
                $validator = Validator::make($request->all(),$validations);
                if( $validator->fails() ) {
                    $response = [ 'message'   =>  $validator->errors($validator)->first()
                    ];
                    return Response::json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
                } else {
                    $query = [
                        'status' => 1,
                        'user_type' => 1,
                        'speciality_id' => $speciality_id
                    ];
                    $result = [];
                    $list = User::getDoctorBySpecialityId($query);
                    $otp_detail = [];
                    if(count($list)) {
                        foreach ($list as $key => $value) {
                            // dd($value);
                            $data = $this->getUserDetail($value);
                            $bookmarked = PatientBookmark::where(['patient_id' => $PATIENT_DETAIL->id , 'doctor_id' => $data['id']])->count();
                            $Review = Review::where(['doctor_id' => $data['id'] , 'status_by_doctor' => 1])->get();
                            $result[] = [
                                'UserIdentificationType' => $data['UserIdentificationType'],
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
                                'status' => $data['status'],
                                'profile_status' => $data['profile_status'],
                                'notification' => $data['notification'],
                                'language' => $data['language'],
                                'otp_detail' => $data['otp_detail'],
                                'qualification' => $data['qualification'],
                                'mother_language' => $data['mother_language'],
                                'doctor_availabilities' => $data['doctor_availabilities'],
                                'bookmarked' => $bookmarked,
                                'reviews' => $Review
                            ];
                        }
                    }
                    $response = [
                        'message' => __('messages.success.success'),
                        'response' => $result
                    ];
                    return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
                }
            }else{
                $response['message'] = trans('messages.invalid.detail');
                return response()->json($response,401);
            }
        }else {
            $Response = [
                'message'  => trans('messages.required.accessToken'),
            ];
          return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE') );
        }
    }

    public function save_doctor_timing_for_availability(Request $request){
        Log::info('----------------------DoctorController--------------------------save_doctor_timing_for_availability'.print_r($request->all(),True));
        $accessToken = $request->header('accessToken');
        $daysArr = $request->days;
        $timeslotsArr = $request->timeslots;
        if( !empty( $accessToken ) ) {
            $validations = [
                'days' => 'required|array',
                'timeslots' => 'required|array',
            ];
            $validator = Validator::make($request->all(),$validations);
            if( $validator->fails() ) {
                $response = [ 'message'=>$validator->errors($validator)->first()
                ];
                return Response::json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
            } else {
                $DoctorDetail = User::where(['remember_token' => $accessToken])->first();
                if($DoctorDetail){
                    DoctorAvailability::where(['doctor_id' => $DoctorDetail->id])->delete();
                    foreach ($daysArr as $key => $dayId) {
                        foreach ($timeslotsArr as $key => $timeSlotId) {
                            DoctorAvailability::insert([
                            'day_id' => $dayId,
                            'time_slot_id' => $timeSlotId,
                            'doctor_id' => $DoctorDetail->id
                            ]);

                            /*$doctor_availabilities_Data = [
                                'day_id' => $dayId,
                                'time_slot_id' => $timeSlotId,
                                'doctor_id' => $DoctorDetail->id
                            ]; 
                            $exist = DoctorAvailability::where([
                                'day_id' => $dayId,
                                'time_slot_id' => $timeSlotId,
                                'doctor_id' => $DoctorDetail->id
                                ])->first();
                            if(!$exist){
                                DoctorAvailability::insert([
                                'day_id' => $dayId,
                                'time_slot_id' => $timeSlotId,
                                'doctor_id' => $DoctorDetail->id
                                ]);
                            }*/
                        }
                    }
                    $result = DoctorAvailability::where(['doctor_id' => $DoctorDetail->id])->get();
                    $response = [
                        'message' => __('messages.success.success'),
                        'response' => $result
                    ];
                    return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
                }else {
                    $response=[
                        'message' => trans('messages.invalid.request'),
                    ];
                    return Response::json($response,__('messages.statusCode.INVALID_ACCESS_TOKEN'));
                }
            }
        }else {
            $Response = [
                'message'  => trans('messages.required.accessToken'),
            ];
          return Response::json( $Response , __('messages.statusCode.SHOW_ERROR_MESSAGE') );
        }
    }


    /*public function get_review_rating_at_doctor_app(Request $request){
        $accessToken =  $request->header('accessToken');
        if( !empty( $accessToken ) ) {
            $DOCTOR_DETAIL = User::Where(['remember_token' => $accessToken, 'user_type' => 1])->first();
            if(count($DOCTOR_DETAIL)){
                $reviews = Review::where(['doctor_id' => $DOCTOR_DETAIL->id , 'status_by_doctor' => 0])->get();
                $response = [
                    'message' => __('messages.success.success'),
                    'response' => $reviews
                ];
                return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
            }else{
                $response['message'] = trans('messages.invalid.detail');
                return response()->json($response,401);
            }
        }else {
            $Response = [
                'message'  => trans('messages.required.accessToken'),
            ];
          return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE') );
        }
    }

    public function change_status_of_reviews_from_doctor_app(Request $request)
    {
        $accessToken =  $request->header('accessToken');
        $review_id = $request->review_id;
        $acceptOrReject = $request->acceptOrReject; // 1 for accept / 2 for reject
        if( !empty( $accessToken ) ) {
            $DOCTOR_DETAIL = User::Where(['remember_token' => $accessToken, 'user_type' => 1])->first();
            if(count($DOCTOR_DETAIL)){
                $validations = [
                    'review_id'=>'required|numeric',
                    'acceptOrReject' => 'required|numeric'
                ];
                $validator = Validator::make($request->all(),$validations);
                if( $validator->fails() ){
                   $response = [
                    'message'=>$validator->errors($validator)->first()
                   ];
                   return Response::json($response,__('messages.statusCode.SHOW_ERROR_MESSAGE'));
                }else{
                    // dd($DOCTOR_DETAIL->id);
                    $REVIEW_DETAIL = Review::where(['doctor_id' => $DOCTOR_DETAIL->id , 'id' => $review_id , 'status_by_doctor' => 0])->first();
                    // dd($REVIEW_DETAIL);
                    if($REVIEW_DETAIL){
                        $REVIEW_DETAIL->status_by_doctor = $acceptOrReject;
                        $REVIEW_DETAIL->save();

                        if($acceptOrReject == 1){
                            $response = [
                                'message' => __('messages.success.review_published'),
                            ];
                            return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));     
                        }
                        if($acceptOrReject == 2){
                            $response = [
                                'message' => __('messages.success.review_un_published'),
                            ];
                            return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));     
                        }
                    }else{
                        $response['message'] = trans('messages.invalid.request');
                        return response()->json($response,__('messages.statusCode.SHOW_ERROR_MESSAGE'));
                    }
                }
                
            }else{
                $response['message'] = trans('messages.invalid.detail');
                return response()->json($response,__('messages.statusCode.SHOW_ERROR_MESSAGE'));
            }
        }else {
            $Response = [
                'message'  => trans('messages.required.accessToken'),
            ];
            return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE') );
        }
    }*/

    public function get_all_appointment_of_doctor_by_date(Request $request){
        $accessToken = $request->header('accessToken');
        $date = date('Y-m-d',strtotime($request->date));
        $page_number = $request->page_number;
        // dd($date);
        if( !empty( $accessToken ) ) {
            $UserDetail = User::where(['remember_token'=>$accessToken])->first();
            if(count($UserDetail)){
                if($UserDetail->user_type == 1){
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
                        $result = Appointment::get_all_appointment_of_doctor_by_date($date,$UserDetail->id, $page_number);
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

    public function accept_or_reject_appointment(Request $request){
        $accessToken =  $request->header('accessToken');
        $appointment_id = $request->appointment_id;
        $accept_or_reject = $request->accept_or_reject;
        if( !empty( $accessToken ) ) {
            $DOCTOR_DETAIL = User::Where(['remember_token' => $accessToken])->first();
            if(count($DOCTOR_DETAIL)){
                if($DOCTOR_DETAIL->user_type == 1){
                    $validations = [
                        'accept_or_reject' => 'required|alpha',
                        'appointment_id' => 'required|numeric'
                    ];
                    $validator = Validator::make($request->all(),$validations);
                    if($validator->fails()){
                        $response = [
                            'message' => $validator->errors($validator)->first()
                        ];
                        return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
                    }else{
                        $AppointmentDetail = Appointment::Where(['id' => $appointment_id, 'doctor_id' => $DOCTOR_DETAIL->id])->first();
                        if($AppointmentDetail && $AppointmentDetail->doctor_id == $DOCTOR_DETAIL->id){

                            $appointmentDateInDb = Carbon::parse($AppointmentDetail->appointment_date)->format('Y-m-d');
                            // dd($appointmentDateInDb);

                            if($appointmentDateInDb >= Carbon::now()->format('Y-m-d')){

                                $Time_slot_detail = TimeSlot::find($AppointmentDetail->time_slot_id);
                                $Appointment_TimeSlot_StartTime = $Time_slot_detail->start_time;
                                $Appointment_TimeSlot_EndTime = $Time_slot_detail->end_time;

                                if( Carbon::parse(strtoupper(($Appointment_TimeSlot_StartTime)))->format('g:i A') > Carbon::now()->format('g:i A') ){
                                    $AppointmentDetail->status_of_appointment = $accept_or_reject;
                                    $AppointmentDetail->save();
                                    if($accept_or_reject == 'Accepted'){
                                        $Response = [
                                            'message'  => trans('messages.success.appointment_accepted'),
                                        ];
                                        //HERE I HAVE TO SEND NOTIFICATION TO PATIENT
                                        return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
                                    }
                                    if($accept_or_reject == 'Rejected'){
                                        $Response = [
                                            'message'  => trans('messages.success.appointment_rejected'),
                                        ];
                                        //HERE I HAVE TO SEND NOTIFICATION TO PATIENT
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
                            $Response = [
                                'message'  => trans('messages.invalid.request'),
                            ];
                            return Response::json( $Response , __('messages.statusCode.NO_DATA_FOUND') );
                        }
                    }
                }else{
                    $Response = [
                        'message'  => trans('messages.invalid.request'),
                    ];
                    return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
                }
            }else{
                $response['message'] = trans('messages.invalid.detail');
                return response()->json($response,__('messages.statusCode.INVALID_ACCESS_TOKEN'));
            }
        }else {
            $Response = [
                'message'  => trans('messages.required.accessToken'),
            ];
          return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE') );
        }
    }

    public function reschedule_appointment_by_doctor(Request $request){
        $accessToken =  $request->header('accessToken');
        $appointment_id = $request->appointment_id;
        $patient_id = $request->patient_id;
        $day_id = $request->day_id;
        $time_slot_id = $request->time_slot_id;
        $appointment_date = $request->appointment_date;
        if( !empty( $accessToken ) ) {
            $DOCTOR_DETAIL = User::Where(['remember_token' => $accessToken])->first();
            if(count($DOCTOR_DETAIL)){
                if($DOCTOR_DETAIL->user_type == 1){
                    $validations = [
                        'patient_id' => 'required|numeric',
                        'appointment_id' => 'required|numeric',
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
                        $appointment_date_from_user = Carbon::parse($appointment_date);
                        if(!$appointment_date_from_user->isYesterday()){
                            $check_doctor_availability = DoctorAvailability::where(['doctor_id' => $DOCTOR_DETAIL->id,'day_id' => $day_id ,'time_slot_id' => $time_slot_id])->first();
                            if($check_doctor_availability){
                                $AlreadyBusyTimeSlot = Appointment::where([
                                    'doctor_id' => $DOCTOR_DETAIL->id,
                                    'time_slot_id' => $time_slot_id, 
                                    'day_id' => $day_id,
                                    'appointment_date' => $appointment_date_from_user
                                ])
                                ->where('status_of_appointment','<>','Rejected')
                                ->where('patient_id','<>',$patient_id)
                                ->get();
                                // dd($AlreadyBusyTimeSlot);
                                if(!count($AlreadyBusyTimeSlot)){
                                    $AppointmentDetail = Appointment::find($appointment_id);
                                    if($AppointmentDetail && $AppointmentDetail->patient_id = $patient_id){
                                        $AppointmentDetail->time_slot_id = $time_slot_id;
                                        $AppointmentDetail->day_id = $day_id;
                                        $AppointmentDetail->appointment_date = $appointment_date_from_user;
                                        $AppointmentDetail->rescheduled_by_doctor = 1;
                                        $AppointmentDetail->status_of_appointment = 'Pending';
                                        $AppointmentDetail->save();
                                        // HERE I HAVE TO SEND NOTIFICATION TO GET CONFIRM ABOUT RESCHEDULED APPOINTMENT
                                        $Response = [
                                            'message'  => trans('messages.success.appointment_rescheduled'),
                                            'response' => Appointment::find($appointment_id)
                                        ];
                                        return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
                                    }else{
                                        $Response = [
                                            'message'  => trans('messages.success.NO_DATA_FOUND'),
                                        ];
                                        return Response::json( $Response , __('messages.statusCode.NO_DATA_FOUND'));
                                    }
                                }else{
                                    if($AlreadyBusyTimeSlot[0]->patient_id == $patient_id){
                                        $AppointmentDetail = Appointment::find($appointment_id);
                                        $AppointmentDetail->time_slot_id = $time_slot_id;
                                        $AppointmentDetail->day_id = $day_id;
                                        $AppointmentDetail->appointment_date = $appointment_date_from_user;
                                        $AppointmentDetail->rescheduled_by_doctor = 1;
                                        $AppointmentDetail->status_of_appointment = 'Pending';
                                        $AppointmentDetail->save();
                                        // HERE I HAVE TO SEND NOTIFICATION TO GET CONFIRM ABOUT RESCHEDULED APPOINTMENT
                                        $Response = [
                                            'message'  => trans('messages.success.appointment_rescheduled'),
                                            'response' => Appointment::find($appointment_id)
                                        ];
                                        return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
                                    }else{
                                        $response = [
                                            'messages' => __('messages.Already_Busy_Time_Slot_With_Other_Patient')
                                        ];
                                        return response()->json($response,__('messages.statusCode.ALREADY_EXIST'));
                                    }
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
                    }
                }else{
                    $Response = [
                        'message'  => trans('messages.invalid.request'),
                    ];
                    return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
                }
            }else{
                $response['message'] = trans('messages.invalid.detail');
                return response()->json($response,__('messages.statusCode.INVALID_ACCESS_TOKEN'));
            }
        }else {
            $Response = [
                'message'  => trans('messages.required.accessToken'),
            ];
          return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE') );
        }
    }

}
