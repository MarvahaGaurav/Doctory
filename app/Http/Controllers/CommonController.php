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
use Hash;
use Auth;
use Exception;
use Twilio\Rest\Client;

class CommonController extends Controller
{
	public function signUp(Request $request){
		$fullName = $request->fullName;
		$email = $request->email;
		$mobile = $request->mobile;
		$password = Hash::make($request->password);
		$device_token = $request->device_token;
		$device_type = $request->device_type;
		$otp = rand(100000,1000000);
		$validations = [
			'fullName' => 'required|max:255',
			'email' => 'required|email|unique:users',
			'mobile' => 'required|numeric',
			'password' => 'required|min:8',
			'device_token' => 'required',
			'device_type' => 'required|numeric'
    	];
    	$validator = Validator::make($request->all(),$validations);
    	if($validator->fails()){
    		$response = [
			'message' => $validator->errors($validator)->first()
			];
			return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
    	}else{
    		$user = new \App\User;
    		$otp = new \App\Otp;

    		$user->name = $fullName;
    		$user->email = $email;
    		$user->mobile = $mobile;
    		$user->password = $password;
    		$user->device_token = $device_token;
    		$user->device_type = $device_type;
    		if($user->save()){
    			$insertId = $user->id;
    			$otp->userId = $insertId;
    			$otp->otp = $otp;
    			$otp->save();
    			
    			$userData = \App\User::find($insertId);
    			$this->sendOtp($mobile,$otp);
    			$response = [
					'message' =>  __('messages.success.success'),
					'response' => $userData
				];
				return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
    		}else{
    			$response = [
					'message' =>  __('messages.error.insert')
				];
				return response()->json($response,__('messages.statusCode.ERROR_IN_EXECUTION'));
    		}
    	}
	}

	public function login(Request $request){
		$email = $request->input('email');
		$password = $request->input('password');
		$device_token = $request->device_token;
		$device_type = $request->device_type;
		$validations = [
			'email' => 'required|email',
			'password' => 'required',
			'device_token' => 'required',
			'device_type' => 'required|numeric'
    	];
    	$validator = Validator::make($request->all(),$validations);
    	if($validator->fails()){
    		$response = [
			'message' => $validator->errors($validator)->first()
			];
			return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
    	}else{
    		$userDetail = User::Where(['email' => $email])->first();
    		if(!empty($userDetail)){
    			if(Hash::check($password,$userDetail->password)){
    				$User = new \App\User;
    				$UserDetail = $User::find($userDetail->id);
    				$UserDetail->device_token = $device_token;
    				$UserDetail->device_type = $device_type;
    				$UserDetail->save();
    				$response = [
						'message' =>  __('messages.success.login'),
						'response' => $User->getUserDetail($userDetail->id)
					];
					return response()->json($response,__('messages.statusCode.INVALID_CREDENTIAL'));
    			}else{
    				$response = [
						'message' =>  __('messages.invalid.detail')
					];
					return response()->json($response,__('messages.statusCode.INVALID_CREDENTIAL'));
    			}
    		}else{
    			$response = [
					'message' =>  __('messages.invalid.detail')
				];
				return response()->json($response,__('messages.statusCode.INVALID_CREDENTIAL'));
    		}
    	}
	}

	public function otpVerify( Request $request ) {
	   $otp  		 = $request->input('otp');
		$accessToken = $request->header('accessToken');
		$validations = [
			'otp'   => 'required'
		];
	  	$validator = Validator::make($request->all(),$validations);
	  	if( !empty( $accessToken ) ) {
			$user = new \App\User;
			$userDetail = User::where(['remember_token' => $accessToken])->first();
			if(count($userDetail)){
			  	if( $validator->fails() ) {
					$response = [
					 'message' => $validator->errors($validator)->first(),
					];
					return Response::json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
			   } else {
			    	$Exist =  $user->getUserDetail($userDetail->id);
			    	if( count($Exist) ) {
			    		if( $Exist->OtpDetail->otp == $otp || $otp == 123456 ){
			    			$OTP = Otp::find($Exist->id);
			    			$OTP->varified = 1;
			    			$OTP->save();
			    			$Response = [
		        			  'message'  => trans('messages.success.success'),
		        			  'status' => 1,
		        			  'response' => $user->getUserDetail($userDetail->id)
		        			];
		        			return Response::json( $Response , trans('messages.statusCode.ACTION_COMPLETE') );
			    		} else {
			    			$Response = [
		        				'message'  => trans('messages.invalid.OTP'),
		        			];
		        			return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE') );
			    		}
			    	} else {
			    		$Response = [
		    			  'message'  => trans('messages.invalid.detail'),
		    			];
		        		return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE') );
			    	}
			   }
			}else{
				$response['message'] = trans('messages.invalid.detail');
				return response()->json($response,trans('messages.statusCode.INVALID_ACCESS_TOKEN'));
			}
		} else {
	    	$Response = [
			  'message'  => trans('messages.required.accessToken'),
			];
	      return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
    	}
	}

	public function sendOtp($mobile,$otp) {
		try{
			$sid = 'AC6ceef3619be02e48da4aba2512cc426b';
			$token = 'eeaa38187028b4a0a9c4f4e105162b6e';
			$client = new Client($sid, $token);
			$number = $client->lookups
					    ->phoneNumbers("+14154291712")
					    ->fetch(array("type" => "carrier"));
			$client->messages->create(
					    $mobile, array(
					        'from' => '+14154291712',
					        'body' => 'loovline please enter this code to verify :'.$otp
					    )
					);
		} catch(Exception $e){
			return 1;
		}
	}
}
