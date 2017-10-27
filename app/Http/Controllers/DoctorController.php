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
use App\Category;
use Hash;
use Auth;
use Exception;


class DoctorController extends Controller
{
    public function getList(Request $request){
    	$query = [
    		'status' => 1,
    		'user_type' => 1
    	];
    	$list = User::getUserList($query);
    	$response = [
    		'message' => __('messages.success.success'),
    		'response' => $list
    	];
    	return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
    }

    public function getDoctorBySpecialityId(Request $request){
        $accessToken =  $request->header('accessToken');
        $speciality_id =  $request->speciality_id;
        if( !empty( $accessToken ) ) {
            $userDetail = User::Where(['remember_token' => $accessToken])->first();
            if(count($userDetail)){
                $validations = [
                    'speciality_id' => 'required|numeric',
                ];
                $validator = Validator::make($request->all(),$validations);
                if( $validator->fails() ) {
                    $response = [
                        'message'   =>  $validator->errors($validator)->first()
                    ];
                    return Response::json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
                } else {
                    $query = [
                        'status' => 1,
                        'user_type' => 1,
                        'speciality_id' => $speciality_id
                    ];
                    $list = User::getDoctorBySpecialityId($query);
                    $response = [
                        'message' => __('messages.success.success'),
                        'response' => $list
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

    
}
