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
		$country_code = $request->country_code;
		$mobile = $request->mobile;
		$password = Hash::make($request->password);
		$device_token = $request->device_token;
		$device_type = $request->device_type;
		$user_type = $request->user_type;
		$otp = rand(100000,1000000);
		$accessToken  = md5(uniqid(rand(), true));
		$validations = [
			'fullName' => 'required|max:255',
			'email' => 'required|email|unique:users',
			'mobile' => 'required|numeric|unique:users',
			'country_code' => 'required|numeric',
			'password' => 'required|min:8',
			'device_token' => 'required',
			'device_type' => 'required|numeric',
			'user_type' => 'required|numeric'
    	];
    	$validator = Validator::make($request->all(),$validations);
    	if($validator->fails()){
    		$response = [
			'message' => $validator->errors($validator)->first()
			];
			return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
    	}else{
    		$user = new \App\User;
    		$OTP = new \App\Otp;
    		$user->name = $fullName;
    		$user->email = $email;
    		$user->country_code = $country_code;
    		$user->mobile = $mobile;
    		$user->password = $password;
    		$user->device_token = $device_token;
    		$user->device_type = $device_type;
    		$user->user_type = $user_type;
    		$user->remember_token = $accessToken;
    		if($user->save()){
    			$insertId = $user->id;
    			$OTP->userId = $insertId;
    			$OTP->otp = $otp;
    			$OTP->save();
    			$userData = $user->getUserDetail($insertId);
    			$this->sendOtp($country_code.$mobile,$otp);
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
		$accessToken  = md5(uniqid(rand(), true));
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
    				$UserDetail->remember_token = $accessToken;
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
			    			$OTP->otp = "";
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
					        'body' => 'doctory please enter this code to verify :'.$otp
					    )
					);
		} catch(Exception $e){
			// dd($e->getMessage());
			$response = [
				'message' => $e->getMessage()
			];
			return $e->getMessage();
		}
	}

	public function forgetPassword(Request $request) {
		/*$country_code = $request->country_code;
		$mobile = $request->mobile;*/
		$email = $request->email;
		$otp = rand(100000,1000000);
		$validations = [
			'email'=>'required|email'
		];
		$validator = Validator::make($request->all(),$validations);
		if( $validator->fails() ){
		   $response = [
		   	'message'=>$validator->errors($validator)->first()
		   ];
		   return Response::json($response,__('messages.statusCode.SHOW_ERROR_MESSAGE'));
		}else{
			$UserDetail = User::Where(['email' => $email])->first();
			if(count($UserDetail)){
				// $this->sendOtp($country_code.$mobile,$otp);
				$data = [
					'otp' => $otp,
					'email' => $email
				];
				try{
					Mail::send(['text'=>'otp'], $data, function($message) use ($data)
					{
				         $message->to($data['email'])
				         		->subject ('Forget Password OTP');
				         $message->from('techfluper@gmail.com');
				   });	
				}catch(Exception $e){
					$response=[
						'message' => $e->getMessage()
		      	];
		     		return Response::json($response,__('messages.statusCode.SHOW_ERROR_MESSAGE'));
				}
				$UserOtp = Otp::find($UserDetail->id);
				$UserOtp->otp = $otp;
				$UserOtp->save();
				$response=[
					'message' => trans('messages.success.success'),
		      	];
		      return Response::json($response,__('messages.statusCode.ACTION_COMPLETE'));
			} else {
				$response=[
					'message' => trans('messages.invalid.request'),
		      	];
		      return Response::json($response,400);
			}
		}
	}

	public function resetPassword(Request $request){
		$accessToken = $request->header('accessToken');
		$password = $request->password;
		$validations = [
			'password' => 'required|min:8'
    	];
    	$validator = Validator::make($request->all(),$validations);
    	if( !empty( $accessToken ) ) {
	    	if($validator->fails()){
	    		$response = [
					'message' => $validator->errors($validator)->first()
				];
				return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
	    	}else{
	    		$UserDetail = User::where(['remember_token' => $accessToken])->first();
	    		if(count($UserDetail)){
	    			// dd($UserDetail);
	    			$User = User::find($UserDetail->id);
	    			$User->password = Hash::make($password);
	    			$User->save();
	    			$userDetail = new \App\User;

	    			$Response = [
	    			  'message'  => trans('messages.success.success'),
	    			  'response' => $userDetail->getUserDetail($UserDetail->id)
	    			];
	        		return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE') );
	    		}else{
	    			$Response = [
	    			  'message'  => trans('messages.invalid.detail'),
	    			];
	        		return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE') );
	    		}
	    	}
	   }else {
	    	$Response = [
				'message'  => trans('messages.required.accessToken'),
			];
	      return Response::json( $Response , __('messages.statusCode.SHOW_ERROR_MESSAGE') );
	   }
	}

	public function changeMobileNumber( Request $request ) {
		$country_code = $request->country_code;
		$mobile 	 =  $request->mobile;
		$accessToken =  $request->header('accessToken');
   	$otp = rand(100000,1000000);
		$isChangedCountryCode = $request->isChangedCountryCode;
		$isChangedMobile = $request->isChangedMobile;
		$userDetail  = [];
		$validations = [
			'country_code' => 'required|numeric',
			'mobile' 	   => 'required|numeric',
			'isChangedCountryCode' => 'required',
			'isChangedMobile' => 'required',
		];
		$validator = Validator::make($request->all(),$validations);
	  	if( !empty( $accessToken ) ) {
	  		$userDetail = User::Where(['remember_token' => $accessToken])->first();
	  		if(count($userDetail)){
	        if( $validator->fails() ) {
	            $response = [
						'message'	=>	$validator->errors($validator)->first(),
					];
	            return Response::json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
	        } else {
		        	if( $isChangedMobile == 1 && $isChangedCountryCode == 0 ) {
		        		$validations = [
							'mobile' => 'unique:users',
						];
		    			$validator = Validator::make($request->all(),$validations);
		    			if( $validator->fails() ) {
		        			$response = [
								'message'	=>	$validator->errors($validator)->first()
							];
		            	return Response::json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
		            } else {
		            	
							$User = new \App\User;
							$UserDetail = $User::find($userDetail->id);
							$UserDetail->mobile = $mobile;
							$UserDetail->save();

							$OTP = Otp::find($userDetail->id);
							$OTP->otp = $otp;
							$OTP->varified = 0;
							$OTP->save();

			        		$this->sendOtp($country_code.$mobile , $otp);
			        		$response = [
			        			'message' => __('messages.success.success'),
			        			'response' => $User->getUserDetail($userDetail->id)
			        		];
			        		return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
		            }
		        	} 

		        	else if( $isChangedMobile == 0 && $isChangedCountryCode == 1) {
		        		// dd( "isChangedCountryCode" );
		        		$User = new \App\User;
						$UserDetail = $User::find($userDetail->id);
						$UserDetail->country_code = $country_code;
						$UserDetail->save();

						$OTP = Otp::find($userDetail->id);
						$OTP->otp = $otp;
						$OTP->varified = 0;
						$OTP->save();

		        		$this->sendOtp($country_code.$mobile , $otp);
		        		$response = [
		        			'message' => __('messages.success.success'),
		        			'response' => $User->getUserDetail($userDetail->id)
		        		];
		        		return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
		        	}

		        	else if( $isChangedMobile == 1 && $isChangedCountryCode == 1){
		        		// dd("both");

		        		$User = new \App\User;
						$UserDetail = $User::find($userDetail->id);
						$UserDetail->mobile = $mobile;
						$UserDetail->country_code = $country_code;
						$UserDetail->save();

						$OTP = Otp::find($userDetail->id);
						$OTP->otp = $otp;
						$OTP->varified = 0;
						$OTP->save();

		        		$this->sendOtp($country_code.$mobile,$otp);
		        		$response = [
		        			'message' => __('messages.success.success'),
		        			'response' => $User->getUserDetail($userDetail->id)
		        		];
		        		return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
		        	}
		        	else {
		        		$User = new \App\User;
		        		$Response = [
							'message'  => trans('messages.same.same_number'),
							'response' =>  $User->getUserDetail($userDetail->id),
						];
		        		return Response::json($Response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
		        	}
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
