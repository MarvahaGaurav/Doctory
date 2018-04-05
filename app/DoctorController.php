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
use App\Notification;
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
        $locale = $request->header('locale');
        if(empty($locale)){
            $locale = 'en';
        }
        \App::setLocale($locale);
        
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
                return response()->json($response,trans('messages.statusCode.INVALID_ACCESS_TOKEN'));
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
        // dd($request->all());
        $day = $request->day;
        $timeslotsArr = $request->timeslots;
        $locale = $request->header('locale');
        if(empty($locale)){
            $locale = 'en';
        }
        \App::setLocale($locale);
        if( !empty( $accessToken ) ) {
            $validations = [
                'day' => 'required',
                // 'timeslots' => 'required_with:day',
            ];
            $validator = Validator::make($request->all(),$validations);
            if( $validator->fails() ) {
                $response = [ 'message'=>$validator->errors($validator)->first()
                ];
                return Response::json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
            } else {
                $DoctorDetail = User::where(['remember_token' => $accessToken])->first();
                // dd($timeslotsArr);
                if($DoctorDetail){
                    DoctorAvailability::where(['doctor_id' => $DoctorDetail->id,'day_id'=>$day])->delete();
                    if(!empty($timeslotsArr)){
                        foreach ($timeslotsArr as $key => $timeSlotId) {
                            DoctorAvailability::insert([
                            'day_id' => $day,
                            'time_slot_id' => $timeSlotId,
                            'doctor_id' => $DoctorDetail->id
                            ]);
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

    public function get_doctor_availability(Request $request){
        Log::info('------------------DoctorController------------get_doctor_availability');
        $accessToken = $request->header('accessToken');
        if( !empty( $accessToken ) ) {
            $UserDetail = User::where(['remember_token'=>$accessToken])->first();
            if(count($UserDetail)){
                if($UserDetail->user_type == 1){
                    $DoctorAvailability = DoctorAvailability::where(['doctor_id' => $UserDetail->id])->get();  
                    $day1 = []; 
                    $day2 = []; 
                    $day3 = []; 
                    $day4 = []; 
                    $day5 = []; 
                    $day6 = []; 
                    $day7 = []; 
                    foreach ($DoctorAvailability as $key => $value) {
                        if($value->day_id == 1){
                            array_push($day1,$value->time_slot_id);
                        }
                        if($value->day_id == 2){
                            array_push($day2,$value->time_slot_id);
                        }
                        if($value->day_id == 3){
                            array_push($day3,$value->time_slot_id);
                        }
                        if($value->day_id == 4){
                            array_push($day4,$value->time_slot_id);
                        }
                        if($value->day_id == 5){
                            array_push($day5,$value->time_slot_id);
                        }
                        if($value->day_id == 6){
                            array_push($day6,$value->time_slot_id);
                        }
                        if($value->day_id == 7){
                            array_push($day7,$value->time_slot_id);
                        }
                    }
                    $result = [
                        '1' => $day1,
                        '2' => $day2,
                        '3' => $day3,
                        '4' => $day4,
                        '5' => $day5,
                        '6' => $day6,
                        '7' => $day7,
                    ];
                    $Response = [
                        'message'  => trans('messages.success.success'),
                        'response' => $result
                    ];
                    return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
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
                return response()->json($response,trans('messages.statusCode.INVALID_ACCESS_TOKEN'));
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
                return response()->json($response,trans('messages.statusCode.INVALID_ACCESS_TOKEN'));
            }
        }else {
            $Response = [
                'message'  => trans('messages.required.accessToken'),
            ];
            return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE') );
        }
    }*/

    public function get_all_appointment_of_doctor_by_date(Request $request){
        Log::info('------------------DoctorController------------get_all_appointment_of_doctor_by_date');
        $accessToken = $request->header('accessToken');
        $date = date('Y-m-d',strtotime($request->date));
        $page_number = $request->page_number;
        $locale = $request->header('locale');
        if(empty($locale)){
            $locale = 'en';
        }
        \App::setLocale($locale);

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

    public function get_all_appointment_of_doctor(Request $request){
        Log::info('------------------DoctorController------------get_all_appointment_of_doctor');
        $accessToken = $request->header('accessToken');
        $date = date('Y-m-d');
        // $page_number = $request->page_number;
        $locale = $request->header('locale');
        if(empty($locale)){
            $locale = 'en';
        }
        \App::setLocale($locale);

        if( !empty( $accessToken ) ) {
            $UserDetail = User::where(['remember_token'=>$accessToken])->first();
            if(count($UserDetail)){
                if($UserDetail->user_type == 1){
                    // $result = Appointment::get_all_appointment_of_doctor($date,$UserDetail->id, $page_number);
                    $result = Appointment::get_all_appointment_of_doctor($date,$UserDetail->id);
                    $final_result = [];
                    // dd($result);
                    foreach ($result as $key => $value) {
                        // dd($value);
                        // dd(Carbon::now()->format('Y-m-d'));
                        $TimeSlotDetail = TimeSlot::find($value->time_slot_id);
                        // dd($TimeSlotDetail);
                        // $start_time = date('g:i A',strtotime($TimeSlotDetail->start_time));
                        $start_time = Carbon::parse($TimeSlotDetail->start_time);
                        // dd(Carbon::now()->format('g:i A'));
                        // dd(Carbon::now()->format('g:i A'));
                        // dd($start_time > Carbon::now());

                        // dd($value->appointment_date);
                        if($value->appointment_date == Carbon::now()->format('Y-m-d')  ){
                            // dd($start_time);
                            // dd($start_time > Carbon::now()->format('g:i A'));
                            if($start_time >= Carbon::now()){
                                $final_result[] = $value;
                            }
                        }
                        if($value->appointment_date > Carbon::now()->format('Y-m-d')){
                            $final_result[] = $value;
                        }
                    }
                    $Response = [
                        'message'  => trans('messages.success.success'),
                        'response' => $final_result
                    ];
                    return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
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
        Log::info('------------------DoctorController------------accept_or_reject_appointment');
        $accessToken =  $request->header('accessToken');
        $appointment_id = $request->appointment_id;
        $accept_or_reject = $request->accept_or_reject;
        $locale = $request->header('locale');
        if(empty($locale)){
            $locale = 'en';
        }
        \App::setLocale($locale);

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
                            // dd($appointmentDateInDb >= Carbon::now()->format('Y-m-d'));
                            if($appointmentDateInDb == Carbon::now()->format('Y-m-d')){
                                // dd(Carbon::now()->format('Y-m-d'));
                                $Time_slot_detail = TimeSlot::find($AppointmentDetail->time_slot_id);
                                // dd($Time_slot_detail);
                                $Appointment_TimeSlot_StartTime = $Time_slot_detail->start_time;
                                $Appointment_TimeSlot_EndTime = $Time_slot_detail->end_time;

                                if( Carbon::parse($Appointment_TimeSlot_StartTime ) > Carbon::now() ) {
                                    $AppointmentDetail->status_of_appointment = $accept_or_reject;
                                    $AppointmentDetail->save();
                                    if($accept_or_reject == 'Accepted'){
                                        $Response = [
                                            'message'  => trans('messages.success.appointment_accepted'),
                                        ];
                                        //HERE I HAVE TO SEND NOTIFICATION TO PATIENT
                                        Notification::insert(['doctor_id'=>$DOCTOR_DETAIL->id,'patient_id'=>$AppointmentDetail->patient_id,'type'=>1,'appointment_id' => $appointment_id]);
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
                            }else if($appointmentDateInDb > Carbon::now()->format('Y-m-d')){
                                $Time_slot_detail = TimeSlot::find($AppointmentDetail->time_slot_id);
                                $Appointment_TimeSlot_StartTime = $Time_slot_detail->start_time;
                                $Appointment_TimeSlot_EndTime = $Time_slot_detail->end_time;
                                
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

    public  function reschedule_appointment_by_doctor(Request $request){
        Log::info('------------------DoctorController------------reschedule_appointment_by_doctor');
        $accessToken =  $request->header('accessToken');
        $appointment_id = $request->appointment_id;
        $patient_id = $request->patient_id;
        $day_id = $request->day_id;
        $time_slot_id = $request->time_slot_id;
        $appointment_date = $request->appointment_date;
        $locale = $request->header('locale');
        if(empty($locale)){
            $locale = 'en';
        }
        \App::setLocale($locale);

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

                                        // $AppointmentDetail->time_slot_id = $time_slot_id;
                                        $AppointmentDetail->rescheduled_time_slot_id = $time_slot_id;

                                        // $AppointmentDetail->day_id = $day_id;
                                        $AppointmentDetail->rescheduled_day_id = $day_id;

                                        // $AppointmentDetail->appointment_date = $appointment_date_from_user;
                                        $AppointmentDetail->rescheduled_date = $appointment_date_from_user;


                                        $AppointmentDetail->rescheduled_by_doctor = 1;
                                        $AppointmentDetail->status_of_appointment = 'Pending';
                                        $AppointmentDetail->save();

                                        // HERE I HAVE TO SEND NOTIFICATION TO GET CONFIRM ABOUT RESCHEDULED APPOINTMENT
                                        Notification::insert(['doctor_id'=>$DOCTOR_DETAIL->id,'patient_id'=>$patient_id,'type'=>1,'appointment_id' => $appointment_id]);
                                        $Response = [
                                            'message'  => trans('messages.success.appointment_rescheduled'),
                                            'response' => Appointment::find($appointment_id)
                                        ];
                                        Log::info('----------------------DoctorController--------------------------reschedule_appointment_by_doctor---------response'.print_r($Response,True));
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
                                        // $AppointmentDetail->time_slot_id = $time_slot_id;
                                        $AppointmentDetail->rescheduled_time_slot_id = $time_slot_id;

                                        // $AppointmentDetail->day_id = $day_id;
                                        $AppointmentDetail->rescheduled_day_id = $day_id;

                                        // $AppointmentDetail->appointment_date = $appointment_date_from_user;
                                        $AppointmentDetail->rescheduled_date = $appointment_date_from_user;


                                        $AppointmentDetail->rescheduled_by_doctor = 1;
                                        $AppointmentDetail->status_of_appointment = 'Pending';
                                        $AppointmentDetail->save();


                                        // HERE I HAVE TO SEND NOTIFICATION TO GET CONFIRM ABOUT RESCHEDULED APPOINTMENT
                                        Notification::insert(['doctor_id'=>$DOCTOR_DETAIL->id,'patient_id'=>$patient_id,'type'=>1,'appointment_id' => $appointment_id]);
                                        $Response = [
                                            'message'  => trans('messages.success.appointment_rescheduled'),
                                            'response' => Appointment::find($appointment_id)
                                        ];
                                        Log::info('----------------------DoctorController--------------------------reschedule_appointment_by_doctor---------response'.print_r($Response,True));
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

    public function accept_or_reject_appointment_by_doctor_rescheduled_by_patient(Request $request){
        Log::info('----------------------PatientController--------------------------accept_or_reject_appointment_by_doctor_rescheduled_by_patient'.print_r($request->all(),True));
        $accessToken = $request->header('accessToken');
        $appointment_id = $request->appointment_id;
        $patient_id = $request->patient_id;
        $time_slot_id = $request->time_slot_id;
        $day_id = $request->day_id;
        $accept_or_reject = $request->accept_or_reject;
        $locale = $request->header('locale');
        if(empty($locale)){
            $locale = 'en';
        }
        \App::setLocale($locale);

        if( !empty( $accessToken ) ) {
            $UserDetail = User::where(['remember_token'=>$accessToken])->first();
            if(count($UserDetail)){
                if($UserDetail->user_type == 1){ // for Doctor Only

                    $validations = [
                        'appointment_id' => 'required|numeric',
                        'patient_id' => 'required|numeric',
                        // 'time_slot_id' => 'required|numeric',
                        // 'day_id' => 'required|numeric',
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
                        if($AppointmentDetail && $AppointmentDetail->patient_id == $patient_id && $AppointmentDetail->rescheduled_by_patient == 1)
                        {
                            $appointmentDateInDb = Carbon::parse($AppointmentDetail->appointment_date)->format('Y-m-d');
                            // dd($appointmentDateInDb);
                            if($appointmentDateInDb >= Carbon::now()->format('Y-m-d')){
                                $Time_slot_detail = TimeSlot::find($AppointmentDetail->time_slot_id);
                                $Appointment_TimeSlot_StartTime = $Time_slot_detail->start_time;
                                $Appointment_TimeSlot_EndTime = $Time_slot_detail->end_time;
                                // dd($Time_slot_detail);
                                if( Carbon::parse(strtoupper(($Appointment_TimeSlot_StartTime)))->format('g:i A') > Carbon::now()->format('g:i A') )
                                {

                                    $AppointmentDetail->status_of_appointment = $accept_or_reject;
                                    
                                    if($accept_or_reject == 'Accepted'){
                                        // dd('Accepted');
                                        $AppointmentDetail->time_slot_id = $AppointmentDetail->rescheduled_time_slot_id;
                                        /*$AppointmentDetail->rescheduled_time_slot_id = "";*/

                                        $AppointmentDetail->day_id = $AppointmentDetail->rescheduled_day_id;
                                        /*$AppointmentDetail->rescheduled_day_id = '';*/
                                        
                                        $AppointmentDetail->appointment_date = $AppointmentDetail->rescheduled_date;
                                        /*$AppointmentDetail->rescheduled_date = "";*/

                                        $AppointmentDetail->save();
                                        $Response = [
                                            'message'  => trans('messages.success.appointment_accepted'),
                                        ];

                                        Log::info('----------------------DoctorController--------------------------accept_or_reject_appointment_by_doctor_rescheduled_by_patient---------response'.print_r($Response,True));

                                        return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
                                    }
                                    if($accept_or_reject == 'Rejected'){
                                        $AppointmentDetail->save();
                                        $Response = [
                                            'message'  => trans('messages.success.appointment_rejected'),
                                        ];

                                        Log::info('----------------------DoctorController--------------------------accept_or_reject_appointment_by_doctor_rescheduled_by_patient---------response'.print_r($Response,True));

                                        return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
                                    }
                                }else{
                                    // dd('expired');
                                   $AppointmentDetail->status_of_appointment = "Expired";
                                   $AppointmentDetail->save();
                                   $response = [
                                       'message' => __('messages.invalid.appointment_expired')
                                   ];
                                   Log::info('----------------------DoctorController--------------------------accept_or_reject_appointment_by_doctor_rescheduled_by_patient---------response'.print_r($Response,True));
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

    public function get_doctor_available_time_slots(Request $request){
        Log::info('------------------DoctorController------------get_doctor_available_time_slots');
        $accessToken = $request->header('accessToken');
        if( !empty( $accessToken ) ) {
         $UserDetail = User::where(['remember_token'=>$accessToken])->first();
         if(count($UserDetail)){
            if($UserDetail->user_type == 1){
                 $drId = $UserDetail->id;
                 $doctor_availabilities = DoctorAvailability::Where(['doctor_id' => $drId])->get();
                 $day1 = []; 
                 $day2 = []; 
                 $day3 = []; 
                 $day4 = []; 
                 $day5 = []; 
                 $day6 = []; 
                 $day7 = []; 
                 $dates = [ 
                     Carbon::now()->addDay(1)->format('Y-m-d'),
                     Carbon::now()->addDay(2)->format('Y-m-d'),
                     Carbon::now()->addDay(3)->format('Y-m-d'),
                     Carbon::now()->addDay(4)->format('Y-m-d'),
                     Carbon::now()->addDay(5)->format('Y-m-d'),
                     Carbon::now()->addDay(6)->format('Y-m-d')
                 ];
                 // dd($dates);
                 $days = [
                     Carbon::now()->addDay(1)->dayOfWeek+1,
                     Carbon::now()->addDay(2)->dayOfWeek+1,
                     Carbon::now()->addDay(3)->dayOfWeek+1,
                     Carbon::now()->addDay(4)->dayOfWeek+1,
                     Carbon::now()->addDay(5)->dayOfWeek+1,
                     Carbon::now()->addDay(6)->dayOfWeek+1
                 ];

               foreach ($doctor_availabilities as $key => $value) {
                  foreach ($days as $key => $value1) {
                     if($value1 == 1 && $value->day_id == 1){
                           $busyOrFree = Appointment::where(['doctor_id'=>$drId,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])
                           ->where('status_of_appointment','<>','rejected')
                           ->where('appointment_date',$dates[$key])
                           ->first();
                             if($busyOrFree){
                                 array_push($day1,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                              }else{
                               array_push($day1,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);
                              }
                     }
                     if($value1 == 2 && $value->day_id == 2){
                        $busyOrFree = Appointment::where(['doctor_id'=>$drId,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->where('status_of_appointment','<>','rejected')
                            ->where('appointment_date',$dates[$key])
                            ->first();
                        if($busyOrFree){
                                array_push($day2,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                            }else{
                            array_push($day2,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);
                        }
                     }

                     if($value1 == 3 && $value->day_id == 3){
                        $busyOrFree = Appointment::where(['doctor_id'=>$drId,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->where('status_of_appointment','<>','rejected')
                            ->where('appointment_date',$dates[$key])
                            ->first();
                           if($busyOrFree){
                            array_push($day3,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                           }else{
                                array_push($day3,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);           
                           }
                     }
                     if($value1 == 4 && $value->day_id == 4){
                        $busyOrFree = Appointment::where(['doctor_id'=>$drId,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->where('status_of_appointment','<>','rejected')
                            ->where('appointment_date',$dates[$key])
                            ->first();
                           if($busyOrFree){
                            array_push($day4,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                        }else{
                            array_push($day4,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);
                        }
                     }
                     if($value1 == 5 && $value->day_id == 5){
                        $busyOrFree = Appointment::where(['doctor_id'=>$drId,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->where('status_of_appointment','<>','rejected')
                            ->where('appointment_date',$dates[$key])
                            ->first();
                        if($busyOrFree){
                                array_push($day5,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                            }else{
                            array_push($day5,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);
                        }
                     }
                     if($value1 == 6 && $value->day_id == 6){
                        $busyOrFree = Appointment::where(['doctor_id'=>$drId,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->where('status_of_appointment','<>','rejected')
                            ->where('appointment_date',$dates[$key])
                            ->first();
                        if($busyOrFree){
                            array_push($day6,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                        }else{
                            array_push($day6,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);
                        }
                     }
                     if($value1 == 7 && $value->day_id == 7){
                        $busyOrFree = Appointment::where(['doctor_id'=>$drId,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->where('status_of_appointment','<>','rejected')
                            ->where('appointment_date',$dates[$key])
                            ->first();
                        if($busyOrFree){
                            array_push($day7,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                        }else{
                            array_push($day7,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);
                        }
                     }
                  }

                  if($value->day_id == 1){
                     if(Carbon::now()->dayOfWeek+1 == 1){
                          $busyOrFree = Appointment::where(['doctor_id'=>$drId,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])
                          ->where('status_of_appointment','<>','rejected')
                          ->where('appointment_date',Carbon::now()->addDay(1)->format('Y-m-d'))
                          ->first();
                             array_push($day1,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                      }
                  }
                  if($value->day_id == 2){
                     if(Carbon::now()->dayOfWeek+1 == 2){
                       $busyOrFree = Appointment::where(['doctor_id'=>$drId,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->where('status_of_appointment','<>','rejected')
                           ->where('appointment_date',Date('Y-m-d'))
                           ->first();
                         array_push($day2,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                     }
                  }
                  if($value->day_id == 3){
                     if(Carbon::now()->dayOfWeek+1 == 3){
                       $busyOrFree = Appointment::where(['doctor_id'=>$drId,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->where('status_of_appointment','<>','rejected')
                           ->where('appointment_date',Date('Y-m-d'))
                           ->first();
                       array_push($day3,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                     }
                  }
                  if($value->day_id == 4){
                     if(Carbon::now()->dayOfWeek+1 == 4){
                       $busyOrFree = Appointment::where(['doctor_id'=>$drId,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->where('status_of_appointment','<>','rejected')
                           ->where('appointment_date',Date('Y-m-d'))
                           ->first();
                       array_push($day4,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                     }
                  }
                  if($value->day_id == 5){
                     if(Carbon::now()->dayOfWeek+1 == 5){
                       $busyOrFree = Appointment::where(['doctor_id'=>$drId,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->where('status_of_appointment','<>','rejected')
                           ->where('appointment_date',Date('Y-m-d'))
                           ->first();
                         array_push($day5,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                      }
                  }
                  if($value->day_id == 6){
                     if(Carbon::now()->dayOfWeek+1 == 6){
                       $busyOrFree = Appointment::where(['doctor_id'=>$drId,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->where('status_of_appointment','<>','rejected')
                           ->where('appointment_date',Date('Y-m-d'))
                           ->first();
                       array_push($day6,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                     }
                  }
                  if($value->day_id == 7){
                     if(Carbon::now()->dayOfWeek+1 == 7){
                       $busyOrFree = Appointment::where(['doctor_id'=>$drId,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->where('status_of_appointment','<>','rejected')
                           ->where('appointment_date',Date('Y-m-d'))
                           ->first();
                       array_push($day7,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                     }
                  }
               }

               $doctor_availabilities_result = [
                  '1' => $day1,
                  '2' => $day2,
                  '3' => $day3,
                  '4' => $day4,
                  '5' => $day5,
                  '6' => $day6,
                  '7' => $day7,
               ];
               Log::info('----------------------DoctorController--------------------------get_doctor_available_time_slots---------response'.print_r($doctor_availabilities_result,True));
               $Response = [
                     'message'  => trans('messages.success.success'),
                     'response' => $doctor_availabilities_result
               ];
               return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
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

    public function get_notification_list(Request $request){
        Log::info('------------------DoctorController------------get_notification_list');
        $accessToken = $request->header('accessToken');
        $locale = $request->header('locale');
        if(empty($locale)){
            $locale = 'en';
        }
        \App::setLocale($locale);

        if( !empty( $accessToken ) ) {
            $UserDetail = User::where(['remember_token'=>$accessToken])->first();
            if(count($UserDetail)){
                Log::info('UserDetail'.print_r($UserDetail,True));
                if($UserDetail->user_type == 1){
                    $Notification = Notification::where(['doctor_id'=>$UserDetail->id,'type'=>2])->get();
                    $result = [];
                    foreach ($Notification as $key => $value) {
                        $drName = User::where(['id'=>$value->doctor_id])->select('name')->first()->name;
                        $patient_Name = User::where(['id'=>$value->patient_id])->select('name')->first()->name;
                        $Appointment = Appointment::find($value->appointment_id);
                        // dd($Appointment);
                        $result[] = [
                            'notification_id' => $value->id,
                            'doctor_id' => $value->doctor_id,
                            'doctor_name' => $drName,
                            'patient_id' => $value->patient_id,
                            'patient_name' => $patient_Name,
                            'appointment_id' => $value->appointment_id,
                            'time_slot_id' => $Appointment->time_slot_id,
                            'day_id' => $Appointment->day_id,
                            'appointment_date' => $Appointment->appointment_date,
                            'rescheduled_time_slot_id' => $Appointment->rescheduled_time_slot_id,
                            'rescheduled_day_id' => $Appointment->rescheduled_day_id,
                            'rescheduled_date' => $Appointment->rescheduled_date,
                            'type' => $value->type,
                            // 'created_at' => Carbon::parse($value->created_at)->format('h:i A, d M'),
                            'created_at' => $value->created_at,
                            'updated_at' => $value->updated_at
                            // 'updated_at' => Carbon::parse($value->updated_at)->format('h:i A, d M'),
                        ];
                    }
                    Log::info('----------------------DoctorController--------------------------get_notification_list---------response'.print_r($result,True));
                    $response = [
                        'message' => __('messages.success.success'),
                        'response' => $result
                    ];
                    return response()->json($response,trans('messages.statusCode.ACTION_COMPLETE'));
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
                return Response::json( $Response , trans('messages.statusCode.INVALID_ACCESS_TOKEN') );
            }
        }else {
            $Response = [
                'message'  => trans('messages.required.accessToken')
            ];
          return Response::json( $Response , __('messages.statusCode.SHOW_ERROR_MESSAGE') );
        }
    }

}
