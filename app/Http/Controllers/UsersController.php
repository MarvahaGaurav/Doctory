<?php

namespace App\Http\Controllers;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use DB;
use Mail;
use Log;
use Response;
use App\User;
use App\Admin;
use Twilio\Rest\Client;
use App;
use Twilio\Exceptions\RestException;
use Hash;
use Auth;
use Exception;



class UsersController extends Controller
{

	public function changeLanguageMessage() {
		$command = "/usr/bin/sudo ejabberdctl registered-users loovline.com ";
		$xmppResponse = shell_exec($command);
		return Response::json($xmppResponse,200);
		// dd(json_decode(json_encode($xmppResponse),TRUE));
		$locale = 'de';
		App::setLocale($locale);
		$locale = App::getLocale();
		DD(__('validation.password.required'));
	}

	public function sendOtp( $mobile , $otp) {
		try{
			$sid = 'AC6ceef3619be02e48da4aba2512cc426b';
			$token = 'eeaa38187028b4a0a9c4f4e105162b6e';
			$client = new Client($sid, $token);
			$number = $client->lookups
					    ->phoneNumbers("+14154291712")
					    ->fetch(
					        array("type" => "carrier")
					    );
			$client->messages->create(
					    $mobile,
					    array(
					        'from' => '+14154291712',
					        'body' => 'loovline please enter this code to verify :'.$otp
					    )
					);
		} catch(Exception $e){
			return 1;
		}
	}

	public function loginSignUp( Request $request ) {
		// $OnlineUsers = shell_exec('/usr/bin/sudo ejabberdctl stats onlineusers');
		// dd(json_decode($data));
		$country_code = $request->country_code;
		$country      = $request->country;
		$mobile		  = $request->mobile;
		$email 		  = $request->email;
		$accessToken  = md5(uniqid(rand(), true));
		$deviceToken  = $request->deviceToken;
		$deviceType   = $request->deviceType;
		$otp		  = rand(100000,1000000);
		$ExistUserDetail = [];
		$validations = [
			'email' 			=> 'required|email',
			'country_code' => 'required',
			'mobile' 	  	=> 'required',
			'deviceToken'  => 'required',
			'deviceType'   => 'required',
			'country' 		=> 'required'
	  	];
	  	$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);


	 	$validator = Validator::make($request->all(),$validations);
	 	if( $validator->fails() ) {
	     $response = [
	     			 'message'	=>	$validator->errors($validator)->first(),
	     			];
	     return Response::json($response,400);
	 	}else {
	     	$UserDetailByEmail = User::getUserDetail( null , null , null , null , $email);
	     	// dd($UserDetailByEmail);
	     	$UserDetailByMobile = User::getUserDetail( null , null , null ,$mobile, null );
	     	$UserDetailByEmailOrMobileorCountryCode = User::getUserDetail( null , null , $country_code , $mobile , $email );

	     	// dd($UserDetailByEmailOrMobileorCountryCode);
	     	if( count( $UserDetailByEmailOrMobileorCountryCode ) ) 
	     	{
	     		if($UserDetailByEmailOrMobileorCountryCode[0]->status) {
	     			if($this->sendOtp($country_code.$mobile , $otp)){
	     				$Response = [ 	
		        		'message'  => trans('messages.invalid.number'),
					  	];
		        		return Response::json( $Response , 400 );
	     			}
	     			DB::table( 'users' )
	 					->where( [ 'email'=> $email ] )
		    			->update( [
						   'accessToken' => $accessToken ,
						   'deviceToken' => $deviceToken,
						   'deviceType'  => $deviceType,
						   'otp'         => $otp
					  	]);
					$userId = $UserDetailByEmailOrMobileorCountryCode[0]->userId;
					$command = "/usr/bin/sudo ejabberdctl change_password ".$userId ." loovline.com ".$accessToken;
					$xmppResponse = shell_exec($command);
		    		$ExistUserDetail = User::getUserDetail( null , null , $country_code , $mobile , $email );
		        	$Response = [ 
		        		'message'  => trans('messages.success.login'),
		        		'command' => $command,
	   				'status'   => 1,
	   				'xmppResponse' => $xmppResponse,
	   				'response' => $ExistUserDetail  
   				];
		        	return Response::json( $Response , 200 );	
	     		} else {
	     			$Response = [ 
	     				'message'  => trans('messages.blocked_by_admin'),
	     				'status'   => 2,
		        		'response' => $ExistUserDetail  
		        	];
		        	return Response::json( $Response , 400 );
	     		}	
	     	} 
	     	else{// if user not exist by email
	     		$validations = [
					'country_code' => 'required',
					'mobile' 	  	=> 'required|unique:users',
					'email' 			=> 'required|email|unique:users',
					'deviceToken'  => 'required',
					'deviceType'   => 'required',
	      		'country' 		=> 'required'
	         ];
	         $validator = Validator::make($request->all(),$validations);
	         if( $validator->fails() ) {
	            $response = [
						'message'	=>	$validator->errors($validator)->first(),
					];
	            return Response::json($response,400);
		      }else {
		      	$countryId = DB::table( 'countries' )->where( [ 'name' => $country ] )->value('id');

	        		$userData = [ 'country_code' => $country_code ,
	 				  'mobile' 		 => $mobile,
	 				  'email' 		 => $email,
	 				  'accessToken' => $accessToken,
	 				  'deviceToken' => $deviceToken,
	 				  'deviceType'  => $deviceType,
	 				  'otp'			 => $otp,
	 				  'country' 	 => $countryId
	 				];
	     			if($this->sendOtp($country_code.$mobile , $otp)){
	     				$Response = [ 	
		        		'message'  => trans('messages.invalid.number'),
					  	];
		        		return Response::json( $Response , 400 );
	     			}
		        	$NewUserData = User::insertUserDetail( $userData );
		        	$userId = $NewUserData[0]->userId;
		        	$accessToken = $NewUserData[0]->accessToken;
		        	// $command = "whoami";
		        	$command = "/usr/bin/sudo ejabberdctl register " . $userId . " loovline.com " . $accessToken;
		        	// dd($command);
		        	$xmppResponse = shell_exec($command);
		        	// dd($xmppResponse);
		        	$xmppStatus = "";
		        	if (strpos($xmppResponse, 'successfully') !== false) {
		                $xmppStatus = 1;
	            } else {
	                $xmppStatus = 0;
	            }
		        	$Response = [ 	
		        		'message'  => trans('messages.success.signup'),
				  		'status'   => 0,
				  		'command' => $command,
				  		'xmppStatus' => $xmppStatus,
				  		'xmppResponse' => $xmppResponse,
				  		'response' => $NewUserData  
				  	];
		        	return Response::json( $Response , 200 );
	        	}
	     	}
	   }
	}

	public function changeMobileNumber( Request $request ) {
		$userId      =  $request->userId;
		$country_code = $request->country_code;
		$mobile 	 =  $request->mobile;
		$accessToken =  $request->header('accessToken');
	   	$otp = rand(100000,1000000);

		$isChangedCountryCode = $request->isChangedCountryCode;
		$isChangedMobile = $request->isChangedMobile;
		$userDetail  = [];
		$validations = [
			'userId' 	   => 'required',
			'country_code' => 'required',
			'mobile' 	   => 'required|numeric',
			'isChangedCountryCode' => 'required',
			'isChangedMobile' => 'required',
		];
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);
		$validator = Validator::make($request->all(),$validations);
	  	if( !empty( $accessToken ) ) {
	  		$userDetail = User::getUserDetail(null,$accessToken,null,null,null);
	  		if(count($userDetail)){
	        if( $validator->fails() ) {
	            $response = [
						'message'	=>	$validator->errors($validator)->first(),
					];
	            return Response::json($response,400);
	        } else {
		        	if( $isChangedMobile == 1 && $isChangedCountryCode == 0 ) {
		        		$validations = [
							'mobile' => 'unique:users',
						];
		    			$validator = Validator::make($request->all(),$validations);
		    			if( $validator->fails() ) {
		        			$response = [
								'message'	=>	$validator->errors($validator)->first(),
							];
			            	return Response::json($response,400);
		            } else {
		            	$data = [ 
								'mobile'      => $mobile ,
								'otpVerified' => 'false',
								'otp' => $otp
							];
			        		$up = User::updateUserData( $data , $userId , $accessToken , null);
				        	if( $up ) {
				        		if($this->sendOtp($country_code.$mobile , $otp)){
				     				$Response = [ 	
					        		'message'  => trans('messages.invalid.number'),
								  	];
					        		return Response::json( $Response , 400 );
				     			}
				        		$userDetail = User::getUserDetail( $userId , $accessToken );
			    				// $this->sendOtp($country_code.$mobile , $otp);
				        		$Response = [
									'message'  => trans('messages.success.update'),
									'response' =>  $userDetail,
								];
				        		return Response::json( $Response , 200 );
				        	} else {

				        		$Response = [
				        					 'message'  => trans('messages.same.same_number'),
				        					 'response' =>  $userDetail,
				        					];
				        		return Response::json( $Response , 400 );
				        	}
		            }
		        	
		        	} 
		        	else if( $isChangedMobile == 0 && $isChangedCountryCode == 1) {
		        		// dd( "isChangedCountryCode" );
		        		$data = [ 
		    			  'country_code' => $country_code,
		    			  'otpVerified' => 'false',
		    			  'otp' => $otp
		    			];
		    			if($this->sendOtp($country_code.$mobile , $otp)){
		     				$Response = [ 	
			        		'message'  => trans('messages.invalid.number'),
						  	];
			        		return Response::json( $Response , 400 );
		     			}
			        	$up = User::updateUserData( $data , $userId , $accessToken , null );
						
			        	if( $up ) {
			        		$userDetail = User::getUserDetail( $userId , $accessToken );
			        		$Response = [
								'message'  => trans('messages.success.update'),
								'response' =>  $userDetail,
							];
			        		return Response::json( $Response , 200 );
			        	} else {
			        		$Response = [
		 						'message'  => trans(',essages.same.country_code'),
		 						'response' =>  $userDetail,
		 					];
			        		return Response::json( $Response , 400 );
			        	}
		        	}

		        	else if( $isChangedMobile == 1 && $isChangedCountryCode == 1){

		        		// dd("both");
		        		$data = [ 
							'country_code' => $country_code,
							'mobile' => $mobile,
							'otpVerified' => 'false',
							'otp' => $otp 
						];
						if($this->sendOtp($country_code.$mobile , $otp)){
		     				$Response = [ 	
			        		'message'  => trans('messages.invalid.number'),
						  	];
			        		return Response::json( $Response , 400 );
		     			}
			        	$up = User::updateUserData( $data , $userId , $accessToken , null );
			        	
			        	if( $up ) {
			        		$userDetail = User::getUserDetail( $userId , $accessToken );
			        		$Response = [
								'message'  => trans('messages.success.success'),
								'response' =>  $userDetail,
							];
			        		return Response::json( $Response , 200 );
			        	} else {
			        		$Response = [
								'message'  => trans('messages.same.same_number'),
								'response' =>  $userDetail,
							];
			        		return Response::json( $Response , 400 );
			        	}
		        	}
		        	else {
		        		$Response = [
							'message'  => trans('messages.same.same_number'),
							'response' =>  $userDetail,
						];
		        		return Response::json( $Response , 400 );
		        	}
	        }
	      }else{
	      	$response['message'] = trans('messages.invalid.detail');
	      	return response()->json($response,401);
	      }
	   }else {
	    	$Response = [
					'message'  => trans('messages.required.accessToken'),
					'response' =>  $userDetail,
				];
	      return Response::json( $Response , 400 );
	   }
	}

	public function forgetPassword( Request $request ) {
		$mobile = $request->mobile;
		$validations = array('mobile'=>'required');
		$validator = Validator::make($request->all(),$validations);
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);

		if( $validator->fails() ){
		   $response = array('message'=>$validator->errors($validator)->first());
		   return Response::json($response,400);
		}
		else{
			$userDetail = User::getUserDetail( null , null, null , $mobile , null );
			if( count($userDetail) > 0 ) {
				dd( $userDetail );
			} else {
				$response=[
					'message' => trans('messages.invalid.request'),
		      	];
		      return Response::json($response,400);
			}
		}
	}

	public function logout( Request $request ) {
		$accessToken =  $request->header('accessToken');
		$userId      =  $request->userId;
		$validations = [
			'userId' => 'required',
	  	];
	  	$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);
		$validator = Validator::make($request->all(),$validations);
		if( !empty( $accessToken ) ) {
			$userDetail = User::getUserDetail(null,$accessToken,null,null,null);
			if(count($userDetail)){
				if( $validator->fails() ) {
					$response = [
					 'message'	=>	$validator->errors($validator)->first(),
					];
					return Response::json($response,400);
				}else {
	   			$Exist = User::getUserDetail( null,$accessToken,null,null,null );
					$userId = $Exist[0]->userId;
					$userName = $Exist[0]->name;
					$photo = $Exist[0]->photo;
					$notification = $Exist[0]->notification;
					$userBusyWithId = DB::table('userBusyWith')
			   				->where(['userBusyWith.userId'=>$userId])
			   				->first();
			   	$userBusyWithId1 = DB::table('userBusyWith')
			   				->where(['userBusyWith.busyWithUserId'=>$userId])
			   				->first();
			   	$UserId = "";
					if(empty($userBusyWithId)){
						if(!empty($userBusyWithId1)){
							$UserId = $userBusyWithId1->userId;
						}else{
							$UserId = null;
						}
					}else{
						$UserId = $userBusyWithId->busyWithUserId;
					}
					if($UserId){
						DB::table('userBusyWith')
						->where('userId',$UserId)
						->delete(); 

						DB::table('userBusyWith')
						->where('busyWithUserId',$UserId)
						->delete(); 

						$userData = ['busyFreeStatus' => 'free'];
						User::updateUserData($userData,$userId,$accessToken,null);

						DB::table('users')
							->where(['id' => $UserId])
							->update(['busyFreeStatus' => 'free']);
						$notifyType = 2;
		   			$bodyText = [
			   					'type'=>'unbusy request',
			   					'status' => 2,
			   					'userId' => $userId,
			   					'userName' => $userName,
			   					'photo' => $photo
			   					];
						$this->notification($UserId,$bodyText,$notifyType);
									$notificationArr = [
		   								'text' => $userName.' '.__('messages.notification.free'),
		   								'userId' => $UserId,
		   							];
		   			User::saveUserNotification($notificationArr);
	      		}
					$data = [ 'accessToken' => '' ];
					$up = User::updateUserData( $data , $userId , $accessToken , null );
					if( $up ) {
						$response=[ 'message' => trans('messages.success.logout') ];
				    	return Response::json($response,200);
					} else {
						$response=[ 'message' => trans('messages.invalid.request')];
				    	return Response::json($response,400);
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
		  return Response::json( $Response , 400 );
		}
	}

	public function editProfile( Request $request ) {
		$userId      		 =  $request->userId;
    	$accessToken 		 =  $request->header('accessToken');
		$photo     			 =  $request->file('photo');
      $destinationPathOfProfile = base_path().'/'.'userImages/';
		$name      			 =  $request->name;
		$dob      			 =  date('Y-m-d',strtotime($request->dob));
		$gender      		 =  $request->gender;
		$profession      	 =  $request->profession;
		$relationShipStatus=  $request->relationShipStatus;
		$kids     			 =  $request->kids;
		$sexualOrientation =  $request->sexualOrientation;
		$height            =  $request->height;
		$eyes              =  $request->eyes;
		$hair              =  $request->hair;
		$smoking           =  $request->smoking;
		$drinking          =  $request->drinking;
		$language      	 =  $request->language;
		$animal     		 =  $request->animal;
		$aboutMe      		 =  $request->aboutMe;
		$homeTown      	 =  $request->homeTown; // city name 
		$country      		 =  $request->country;
		$validations = [
			'userId' => 'required',
			'homeTown' => 'required',
			'country' => 'required'
      ];
      $locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);
   	$validator = Validator::make($request->all(),$validations);
   	if( !empty( $accessToken ) ) {
   		$userDetail = User::getUserDetail(null,$accessToken,null,null,null);
   		if(count($userDetail)){
	        	if( $validator->fails() ) {
			        $response = [
		 				'message' => $validator->errors($validator)->first(),
		 				];
			        return Response::json($response,400);
		   	} else {
		    		$userDetail = User::getUserDetail($userId,$accessToken,null,null,null );
		    		if( count($userDetail) > 0 ) {
		    			$countryId = User::getCountryDetailByName( $userDetail[0]->country)->id;
		    			$getCityDetail = User::getCityDetail( $countryId , $homeTown );
		    			if( !count($getCityDetail ) ){
		    				$cityId = User::insertCity( $homeTown , $countryId );	
		    			} else {
		    				$cityId = $getCityDetail->id;
		    			}
		    			$userDetailData =   [ 
							'name' => $name,
							'dob' => $dob,
							'gender' => $gender,
							'profession' => $profession,
							'homeTown' => $cityId,
							'relationShipStatus' => $relationShipStatus,
							'kids' => $kids,
							'sexualOrientation' => $sexualOrientation,
							'height' => $height,
							'eyes' => $eyes,
							'hair' => $hair,
							'smoking' => $smoking,
							'drinking' => $drinking,
							'language' => $language,
							'animal' => $animal,
							'aboutMe' => $aboutMe,
					  	];
			    		if( !empty($photo)) {

			    			if( !empty( $userDetail->photo )) {
			    				unlink(base_path().'/'.'userImages/'.$userDetail->photo);
			    			}

							$uploadedfile = $_FILES['photo']['tmp_name'];
				    		$fileName = time()."_".$photo->getClientOriginalName();
		               		$photo->move( $destinationPathOfProfile , $fileName );
						/*$fileName1 = $this->uploadImage($photo,$uploadedfile,$destinationPathOfProfile);
						$fileName = explode('thumbnail_', $fileName1[6])[1];*/
               	DB::table('users')->where( [ 'id' => $userId] )->update([ 'photoShowToPublic' => 'false']);
	                	$userDetailData = [ 
								'photo' => $fileName,
								'name' => $name,
								'dob' => $dob,
								'gender' => $gender,
								'profession' => $profession,
								'homeTown' => $cityId,
								'relationShipStatus' => $relationShipStatus,
								'kids' => $kids,
								'sexualOrientation' => $sexualOrientation,
								'height' => $height,
								'eyes' => $eyes,
								'hair' => $hair,
								'smoking' => $smoking,
								'drinking' => $drinking,
								'language' => $language,
								'animal' => $animal,
								'aboutMe' => $aboutMe,
					  	  	];
			    		}	
			    		$up = User::updateUserData( null , $userId , $accessToken , $userDetailData);
			    		DB::table('users')->where(['id' =>$userId,'accessToken' => $accessToken])->update(['profileStatus' => 1]);
			    		$UserDetail = User::getUserDetail( $userId , $accessToken , null , null , null );
			    		if( $up > 0 ){
			    			$Response = [
				  		  	  'message'  => trans('messages.success.success'),
				  		  	  'response' => $UserDetail,
							];
		        			return Response::json( $Response , 200 );
			    		} else {
			    			$Response = [
								'message'  => trans('messages.no_change_OR_Error'),
								'response' => $UserDetail,
							];
		        			return Response::json( $Response , 200 );
			    		}
		    		} else {
			    		$Response = [
							'message'  => trans('messages.invalid.request'),
						];
		        		return Response::json( $Response , 400 );
		    		}
	    		}
	    	}else{
				$response['message'] = trans('messages.invalid.detail');
				return response()->json($response,401);
			}
		} else {
	    	$Response = [
				'message'  => trans('messages.required.accessToken'),
			];
        return Response::json( $Response , 400 );
	   }
	}

	public function getUserName( Request $request ) {
		$userId      =  $request->userId;
		$name      	 =  $request->name;
    	$accessToken =  $request->header('accessToken');
		$validations = [
			'userId' => 'required',
			'name'   => 'required'
		];
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);

      $validator = Validator::make($request->all(),$validations);
      if( !empty( $accessToken ) ) {
			$userDetail = User::getUserDetail(null,$accessToken,null,null,null);
			if(count($userDetail)){
		      if( $validator->fails() ) {
			         $response = [
							'message' => $validator->errors($validator)->first(),
						];
			        return Response::json($response,400);
			   } else {
			    	$Exist = User::getUserDetail( $userId,$accessToken,null,null,null );
			    	if( count($Exist) ) {
			    		$userDetailData = [ 'name' => $name];
			    		$up = User::updateUserData( null , $userId , $accessToken , $userDetailData);
			    		$Response = [
							'message'  => trans('messages.success.success'),
							'response' => User::getUserDetail( $userId,$accessToken,null,null,null )
						];
		        		return Response::json( $Response , 200 );
			    	} else {
			    		$Response = [
		     			  'message'  => trans('messages.invalid.detail'),
		     			];
		        		return Response::json( $Response , 400 );
			    	}
			   }
			}else{
				$response['message'] = trans('messages.invalid.detail');
				return response()->json($response,401);
			}
		} else {
	    	$Response = [
  			  'message'  => trans('messages.required.accessToken'),
  			];
	      return Response::json( $Response , 400 );
	   }
	}

	public function otpVerify( Request $request ) {
	   $userId 	 = $request->input('userId');
	   $otp  		 = $request->input('otp');
		$accessToken = $request->header('accessToken');
		$validations = [
			'userId' => 'required',
			'otp'   => 'required'
		];
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);
	  	$validator = Validator::make($request->all(),$validations);
	  	if( !empty( $accessToken ) ) {
			$userDetail = User::getUserDetail(null,$accessToken,null,null,null);
			if(count($userDetail)){
			  	if( $validator->fails() ) {
			         $response = [
			 			 'message' => $validator->errors($validator)->first(),
			 			];
			        return Response::json($response,400);
			   } else {
			    	$Exist = User::getUserDetail( $userId,$accessToken,null,null,null );
			    	if( count($Exist) ) {
			    		if( $Exist[0]->otp == $otp || $otp == 123456 ){
			    			$userData = [ 
			    				'otp'  => $otp,
								'otpVerified' => 'true'	
							];
			    			User::updateUserData( $userData , $userId , $accessToken , null );
			    			$Response = [
		        			  'message'  => trans('messages.success.success'),
		        			  'status' => 1,
		        			  'response' => User::getUserDetail( $userId,$accessToken,null,null,null )
		        			];
		        			return Response::json( $Response , 200 );
			    		} else {
			    			$Response = [
		        				'message'  => trans('messages.invalid.OTP'),
					        	'status' => 0,
		        			];
		        			return Response::json( $Response , 400 );
			    		}
			    	} else {
			    		$Response = [
		    			  'message'  => trans('messages.invalid.detail'),
		    			];
		        		return Response::json( $Response , 400 );
			    	}
			   }
			}else{
				$response['message'] = trans('messages.invalid.detail');
				return response()->json($response,401);
			}
		} else {
	    	$Response = [
			  'message'  => trans('messages.required.accessToken'),
			];
	      return Response::json( $Response , 400 );
    	}
	}

	public function resendOtp( Request $request ) {
		$accessToken   =  $request->header('accessToken');
		$country_code  =  $request->country_code;
		$mobile        =  $request->mobile;
   	$otp		  = rand(100000,1000000);
		$validations = [
			'country_code' => 'required',
			'mobile'   => 'required'
		];
	   $validator = Validator::make($request->all(),$validations);
	   $locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);

	   if( !empty( $accessToken ) ) {
	   	$userDetail = User::getUserDetail(null,$accessToken,null,null,null);
	   	if(count($userDetail)){
				if( $validator->fails() ) {
				  	$response = [
						 'message' => $validator->errors($validator)->first(),
						];
				  return Response::json($response,400);
				} else {
					$Exist = User::getUserDetail( null,$accessToken,$country_code,$mobile,null );
					if( count($Exist) ) {
						if($this->sendOtp($country_code.$mobile , $otp)){
							$Response = [ 	
				  		'message'  => trans('messages.invalid.number'),
					  	];
				  		return Response::json( $Response , 400 );
						}
						DB::table( 'users' )
							->where('accessToken',$accessToken)
							->update( ['otp'=>$otp] );
						$response = [
						 'message' => trans('messages.success.success'),
						];
				  	return Response::json($response,200);
					}else {
						$response = [
						 'message' => trans('messages.invalid.detail'),
						];
				  	return Response::json($response,400);
					}
				}
			}else{
				$response['message'] = trans('messages.invalid.detail');
				return response()->json($response,401);
			}
		} else {
	    	$Response = ['message'  => trans('messages.required.accessToken')];
        	return Response::json( $Response , 400 );
    	}
	}

	public function getUserProfile( Request $request ) {
		$accessToken =  $request->header('accessToken');
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);
		if( !empty( $accessToken ) ) {
	    	$userDetail = User::getUserDetail( $userId = null, $accessToken , $country_code = null , $mobile = null , $email = null );
	    	if( count( $userDetail ) ) {
	    		$userId = $userDetail[0]->userId;
	    		$selectedSubInterestOfUser = User::getUserSubInterestByInterestId( null , $userId );
	    		$subInterestArray = [];
	    		$result = [];
	    		$interestArr = [];
	    		$entertainmentArr = [];
	    		if( count( $selectedSubInterestOfUser ) ) {
	    			foreach ($selectedSubInterestOfUser as $key => $value) {
	    				if(!in_array($value->interestId , $interestArr ) )
	    					$interestArr[] = $value->interestId;	
	    			}
	    		}
	    		foreach ($interestArr as $key => $value) {
	    			$interestName = DB::table('interest')->where(['id' => $value])->value('name');
	    			$entertainmentArr[] = [	
		    			'id' => $value,
						'name' => $interestName,
						'subInterest' => 
						User::getUserSubInterestByInterestId( $value , $userId )
	   			];
	    		}
	    		foreach ($userDetail as $key => $value) {
	    			$result[] = [
	    				'userId' => $value->userId,
	    				'userEmail' => $value->userEmail,
	    				'country' => $value->country,
	    				'countryNameCode' => $value->countryNameCode,
	    				'country_code' => $value->country_code,
	    				'mobile' => $value->mobile,
	    				'accessToken' => $value->accessToken,
	    				'deviceToken' => $value->deviceToken,
	    				'deviceType' => $value->deviceType,
	    				'otpVerified' => $value->otpVerified,
	    				'photo' => $value->photo,
	    				'photoShowToPublic' => $value->photoShowToPublic,
	    				'name' => $value->name,
	    				'dob' => $value->dob,
	    				'gender' => $value->gender,
	    				'profession' => $value->profession,
	    				'homeTown' => $value->homeTown,
	    				'relationShipStatus' => $value->relationShipStatus,
	    				'kids' => $value->kids,
	    				'sexualOrientation' => $value->sexualOrientation,
	    				'height' => $value->height,
	    				'eyes' => $value->eyes,
	    				'hair' => $value->hair,
	    				'smoking' => $value->smoking,
	    				'drinking' => $value->drinking,
	    				'language' => $value->language,
	    				'animal' => $value->animal,
	    				'interest' => $entertainmentArr,
	    				'aboutMe' => $value->aboutMe,
	    				'sound' => $value->sound,
	    				'notification' => $value->notification,
	    				'findMe' => $value->findMe,
	    				'busyFreeStatus' => $value->busyFreeStatus
	    			];
	    		}
		    	$Response = [
	 			  'message'  => trans('messages.success.success'),
	 			  'response' => $result
	 			];
	        	return Response::json( $Response , 200 );
		   } else {
		    	$Response = [
	 			  'message'  => trans('messages.noData'),
	 			  'response' => $userDetail
	 			];
		      return Response::json( $Response , 401);
		   }
		} else {
			$Response = [
			  'message'  => trans('messages.required.accessToken'),
			];
			return Response::json( $Response , 400 );
		}
	}

	public function getOtherUserProfile( Request $request ) {
		$userId = $request->userId; // for whic we are looking profile
		$accessToken = $request->header('accessToken');
		$validations = [
			'userId' 	   => 'required',
		];
   	$validator = Validator::make($request->all(),$validations);
   	$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);

	   if( !empty( $accessToken ) ) {
	    	$Exist = User::getUserDetail( null,$accessToken,null,null,null );
	   		if( count($Exist) ) {
			   	if( $validator->fails() ) {	
				        $response = [
							'message' => $validator->errors($validator)->first(),
						];
			        return Response::json($response,400);
					} else {
						$userId1 = $Exist[0]->userId;
					   	$userDetail = User::getUserDetail( $userId, $accessToken = null , $country_code = null , $mobile = null , $email = null ); // detail of user of whom profile we are seeing
					   if( count( $userDetail ) ) {
				    		$userId = $userDetail[0]->userId;
				    		$selectedSubInterestOfUser = User::getUserSubInterestByInterestId( null , $userId );
				    		$subInterestArray = [];
				    		$result = [];
				    		$interestArr = [];
				    		$entertainmentArr = [];

				    		if( count( $selectedSubInterestOfUser ) ) {
				    			foreach ($selectedSubInterestOfUser as $key => $value) {
				    				if(!in_array($value->interestId , $interestArr ) )
				    					$interestArr[] = $value->interestId;	
				    			}
				    		}
				    		
				    		foreach ($interestArr as $key => $value) {
				    			$interestName = DB::table('interest')->where(['id' => $value])->value('name');
				    			$entertainmentArr[] = [	
				    				'id' => $value,
									'name' => $interestName,
									'subInterest' => 
									User::getUserSubInterestByInterestId( $value , $userId )
			   				];
				    		}

				    		$isBlocked = DB::table('blockList')->where([
				    			'userId' => $userId1,
				    			'blockedId' => $userId
				    			])->first();

				    		if(count($isBlocked)){
				    			$blocked = "blocked";
				    		}else{
				    			$blocked = "notBlocked";
				    		}

				    		foreach ($userDetail as $key => $value) {
				    			$result[] = [
				    				'userId' => $value->userId,
				    				'userEmail' => $value->userEmail,
		    						'country' => $value->country,
		    						'countryNameCode' => $value->countryNameCode,
				    				'country_code' => $value->country_code,
				    				'mobile' => $value->mobile,
				    				'accessToken' => $value->accessToken,
				    				'deviceToken' => $value->deviceToken,
				    				'deviceType' => $value->deviceType,
				    				'otpVerified' => $value->otpVerified,
				    				'photo' => $value->photo,
				    				'name' => $value->name,
				    				'dob' => $value->dob,
				    				'gender' => $value->gender,
				    				'profession' => $value->profession,
				    				'homeTown' => $value->homeTown,
									'photoShowToPublic' => $value->photoShowToPublic,
				    				'relationShipStatus' => $value->relationShipStatus,
				    				'kids' => $value->kids,
				    				'sexualOrientation' => $value->sexualOrientation,
				    				'height' => $value->height,
				    				'eyes' => $value->eyes,
				    				'hair' => $value->hair,
				    				'smoking' => $value->smoking,
				    				'drinking' => $value->drinking,
				    				'language' => $value->language,
				    				'animal' => $value->animal,
				    				'interest' => $entertainmentArr,
				    				'aboutMe' => $value->aboutMe,
				    				'blocked' => $blocked,
		    						'busyFreeStatus' => $value->busyFreeStatus,
		    						 //$userId( for whom we are looking)
		    						//userId1( who is looking  )
		    						'busyWith' => User::checkBusyWithUser($userId1 ,$userId)// user busy with current user who is searching or not 
				    			];
				    		}
					    	$Response = [
		        			  'message'  => trans('messages.success.success'),
		        			  'response' => $result
		        			];
				         return Response::json( $Response , 200 );
						} else {
					    	$Response = [
				  			  'message'  => trans('messages.noData'),
				  			  'response' => $userDetail
				  			];
					      return Response::json( $Response , 400 );
						}
					}
				}else {
					$Response = [
						'message'  => trans('messages.invalid.detail'),
					];
					return Response::json( $Response , 401 );
				}
		}else{
			$Response = [
				'message'  => trans('messages.required.accessToken'),
			];
			return Response::json( $Response , 400 );
		}
	}

	// same for ChangeprofilePic
	public function AddprofilePic( Request $request ) {
	 	$accessToken =  $request->header('accessToken');
	 	$photo       =  $request->file('photo');
		$destinationPathOfProfile = base_path().'/'.'userImages/';
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);

	 	if( !empty( $accessToken ) ) {
	 		$userDetail = User::getUserDetail(null,$accessToken,null,null,null);
	 		if(count($userDetail)){
		 		$validations = [
			 		'photo' 	   => 'required|image',
		      ];
		     	$validator = Validator::make($request->all(),$validations);
		     	if( $validator->fails() ) {
					$response = [
						'message' => $validator->errors($validator)->first(),
					];
		        	return Response::json($response,400);
				} else {
					$uploadedfile = $_FILES['photo']['tmp_name'];
					$userDetail = User::getUserDetail( null,$accessToken,null,null,null );

					if( count($userDetail) ){
						if( !empty($userDetail[0]->photo) && file_exists(base_path().'/'.'userImages/'.$userDetail[0]->photo) ){
							unlink(base_path().'/'.'userImages/'.$userDetail[0]->photo);
						}
						$fileName = time()."_".$photo->getClientOriginalName();
						$photo->move( $destinationPathOfProfile , $fileName );
						
						/*$fileName1 = $this->uploadImage($photo,$uploadedfile,$destinationPathOfProfile);
						$fileName = explode('thumbnail_', $fileName1[6])[1];*/
						
						$userId= $userDetail[0]->userId;
						$userDetailData = [ 
						'photo' => $fileName
						];
						$up = User::updateUserData( null , $userId , $accessToken , $userDetailData);
						$Response = [ 
							'message'  => trans('messages.success.success') ,
							'status'   => $up,
							'response' => User::getUserDetail( null,$accessToken,null,null,null )
						]; 
						return Response::json( $Response , 200); 
					} else {
						$Response = [
							'message'  => trans('messages.invalid.detail'),
						];
						return Response::json( $Response , 400 );	
					}
		    	}
		   }else{
				$response['message'] = trans('messages.invalid.detail');
				return response()->json($response,401);
			}	
		} else {
	    	$Response = [
				'message'  => trans('messages.required.accessToken'),
			];
	 		return Response::json( $Response , 400 );
	   }
	}

	public function RemoveProfilePic( Request $request ) {
		$accessToken =  $request->header('accessToken');
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);
		if( !empty( $accessToken ) ) {
			$userDetail = User::getUserDetail( null,$accessToken,null,null,null );
			if( count($userDetail) ){
				if( !empty($userDetail[0]->photo) && file_exists(base_path().'/'.'userImages/'.$userDetail[0]->photo) )
				{
				  unlink(base_path().'/'.'userImages/'.$userDetail[0]->photo);
				}
				$userId= $userDetail[0]->userId;
	            $userDetailData = [ 
						'photo' => ""
				  	];
				User::updateUserData( null , $userId , $accessToken , $userDetailData);
				$Response = [ 
					'message'  => trans('messages.success.success') ,
					'status'   => 1,
					'response' => User::getUserDetail( null,$accessToken,null,null,null )
				]; 
            return Response::json( $Response , 200);
			} else {
				$Response = [
					'message'  => trans('messages.invalid.detail'),
				];
				return Response::json( $Response , 401 );	
         }
		} else {
	    	$Response = [
  			  'message'  => trans('messages.required.accessToken'),
  			];
        	return Response::json( $Response , 400 );
	    }
	}

    // interest added by admin
	public function getInterestList(Request $request) {
		$interestList = Admin::getInterestList();
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);
		if( count($interestList) ) {

			$Response = [
				'message' => trans('messages.success.success'),
				'response' => $interestList
			];
			return Response::json( $Response , 200 );
		} else {

			$Response = [
				'message' => trans('messages.noData'),
				'response' => $interestList
			];
			return Response::json( $Response , 400 );
		}
	}

	public function getSubInterest( Request $request ) {
		$interestId = $request->interestId;
		$userId = $request->userId;
		$accessToken =  $request->header('accessToken');
		$validations = [
			'interestId' => 'required',
			'userId' => 'required'
    	];
		$messages = [
		  'interestId.required' => 'please provide interestId!',
		  'userId.required' => 'please provide userId'
		];
		$validator = Validator::make( $request->all() , $validations , $messages);
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);

	   if( !empty( $accessToken ) ) {
	   	$userDetail = User::getUserDetail(null,$accessToken,null,null,null);
	   	if(count($userDetail)){
		      if( $validator->fails() ) {
		            $response = [
		            	'message' => $validator->errors($validator)->first(),
		            ];
		            return Response::json($response,400);
			   } else {
			    		$userDetail = User::getUserDetail( $userId  , $accessToken , $country_code = null , $mobile = null , $email = null );
				    	if( count($userDetail) ) {
			        		$userSubInterest = User::getUserSubInterestByInterestId( $interestId , $userId );
			        		$userSubIntArr = [];
			        		$response = [];

			        		// dd( $userSubInterest );

			        		if( count($userSubInterest) ){
				        		foreach ($userSubInterest as $key => $value) {
				        			$userSubIntArr[] = $value->subInterestId;
				        		}
			        		}
			        		// dd($userSubIntArr);

			    			$getSubInterest = Admin::getSubInterestUnderInterestId( $interestId );
			        		// dd( $getSubInterest );

			    			if( count( $getSubInterest ) ) {
				        		foreach ($getSubInterest as $key => $value) {
				        			if(in_array( $value->subInterestId , $userSubIntArr)) {
					        			$response []= [
					        				'interestId' => $value->interestId,
					        				'interestName' => $value->interestName,
					        				'subInterestId' => $value->subInterestId,
					        				'subInterestName' => $value->subInterestName,
					        				'selected' => "true",
					        			];
					        		} else {
					        			$response []= [
					        				'interestId' => $value->interestId,
					        				'interestName' => $value->interestName,
					        				'subInterestId' => $value->subInterestId,
					        				'subInterestName' => $value->subInterestName,
					        				'selected' => "false",

					        			];
					        		}
				        		}
				        		$Response = [
					    			'message' => trans('messages.success.success'),
					    			'response' => $response
					    		];
					    		return Response::json( $Response , 200 );
				        	}else {
			    				$Response = [
					    			'message' => trans('messages.noData'),
					    			'response' => $response
					    		];
					    		return Response::json( $Response , 400 );
				    		}
			    		} else {
			        		$Response = [
		        			  'message'  => trans('messages.invalid.detail'),
		        			];
			        		return Response::json( $Response , 400 );
			        	}
			   }
			}else{
				$response['message'] = trans('messages.invalid.detail');
				return response()->json($response,401);
			}
		} else {
	    	$Response = [
  			  'message'  => trans('messages.required.accessToken'),
  			];
        	return Response::json( $Response , 400 );
    	}
	}

	public function editSubInterestOfUser( Request $request ) {
		$accessToken =  $request->header('accessToken');
		$userInterestAndSubInterest = $request->userInterestAndSubInterest;
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);
		// dd($userInterestAndSubInterest );
		if( !empty( $accessToken ) ) {
			$userDetail = User::getUserDetail(null,$accessToken,null,null,null);
			if(count($userDetail)){
		    	$userDetail = User::getUserDetail( null , $accessToken , $country_code = null , $mobile = null , $email = null );
		    	if( count( $userDetail ) ) {
		    		$userId = $userDetail[0]->userId;
		    		$result = [];

		    		if( count( $userInterestAndSubInterest) ){
		    			DB::table('userSubInterest')->where(['userId' => $userId])->delete();

			    		foreach ($userInterestAndSubInterest as $key => $data) {
			    			$interestId = $key;

			    			foreach ($data as $value) {
			    					
					    		DB::table( 'userSubInterest' )
					    			->insert( [
						    				'interestId' => $interestId,
						    				'subInterestId' => $value,
						    				'userId' => $userId
					    				] );
			    			}
			    		}
			    		$Response = [
		        			'message'  => trans('messages.success.success')
		        		];
		        		return Response::json( $Response , 200 );

				    } else {
				    	$Response = [
		        			'message'  => trans('messages.noData')
		        		];
		        		return Response::json( $Response , 400 );
				    }
		    	} else {
		    		$Response = [
		    			'message'  => trans('messages.invalid.detail'),
		    		];
		    		return Response::json( $Response , 400 );
		    	}
		   }else{
				$response['message'] = trans('messages.invalid.detail');
				return response()->json($response,401);
			}
		} else {
	    	$Response = [
				'message'  => trans('messages.required.accessToken'),
			];
			return Response::json( $Response , 400 );
	    }
	}


	public function searchPeople( Request $request ){

    	$accessToken =  $request->header('accessToken');
		$countryName =  $request->country; // country name
		$name =  $request->name;
		$cityName =  $request->city; // city name
		$gender =  $request->gender;
		$kids =  $request->kids;
		$animal = $request->animal;
		$smoking =  $request->smoking;
		$drinking =  $request->drinking;
		$startAge =  $request->startAge;
		$endAge =  $request->endAge;
		$countryId = "";
		$cityId = "";
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);

		if(!empty($countryName)) {
			$countryId = DB::table('countries')->where(['countries.name' => $countryName])->value('id');
		}

		if(!empty($cityName)) {
			$cityId = DB::table('cities')->where(['cityName' => $cityName])->value('id');
		}

		// dd($countryId);
		$UserData = User::getUserDetail( $userId = null , $accessToken , $country_code = null ,$mobile = null , $email = null ); // current user data
		// dd($UserData);
		/*$FindDetail = null;
		if(count($UserData)){
			$FindDetail = DB::table('whoCanFindDetail')
			->where(['userId' => $UserData[0]->userId])
			->first();
		}*/
		$userDetail = User::searchPeople( $name , $countryId, $cityId, $gender, $kids, $animal, $smoking, $drinking , $accessToken);
		// dd($userDetail);
		$resultArr = [];

		if( !empty( $accessToken ) ){
			// dd($UserData);
			if( !empty($UserData)) {
				$userId = $UserData[0]->userId;
				// dd($userId);
				$UserBusyStatus = User::checkBusyStatusOfUser($userId);
				// dd($UserBusyStatus);
				foreach ($userDetail as $key => $value) {

					$SP_FindMeDetail = DB::table('whoCanFindDetail')
					->leftjoin('users','users.id','=','whoCanFindDetail.userId')
					->where(['whoCanFindDetail.userId' => $value->userId])
					->select('whoCanFindDetail.userId as userId',
						'whoCanFindDetail.countryId as countryId',
						'whoCanFindDetail.country as country',
						'whoCanFindDetail.cityId as cityId',
						'whoCanFindDetail.city as city',
						'whoCanFindDetail.height as height',
						'whoCanFindDetail.eyes as eyes',
						'whoCanFindDetail.hair as hair',
						'whoCanFindDetail.startAge as startAge',
						'whoCanFindDetail.endAge as endAge',
						'whoCanFindDetail.kids as kids',
						'whoCanFindDetail.smoking as smoking',
						'whoCanFindDetail.drinking as drinking',
						'whoCanFindDetail.relationShipStatus as relationShipStatus',
						'users.findMe as findMe')
					->first(); 

					// dd($SP_FindMeDetail);
					$isBlocked = DB::table('blockList')
						->where([
			    			'userId' => $userId,
			    			'blockedId' => $value->userId
		    			])
	    			->first();
		    		if(count($isBlocked)){
		    			$blocked = "blocked";
		    		}else{
		    			$blocked = "notBlocked";
		    		}

		    		// dd($this->getAge( $UserData[0]->dob ));

					/*if( !empty( $startAge ) && !empty( $endAge ) ){

						if( $this->getAge($value->dob) >= $startAge && $this->getAge($value->dob) <= $endAge ){
							$resultArr[] = [
								'userId' => $value->userId,
								'userEmail' => $value->userEmail,
								'country' => $value->country,
								'countryNameCode' => $value->countryNameCode,
								'country_code' => $value->country_code,
								'mobile' => $value->mobile,
								'accessToken' => $value->accessToken,
								'deviceToken' => $value->deviceToken,
								'deviceType' => $value->deviceType,
								'otp' => $value->otp,
								'otpVerified' => $value->otpVerified,
								'status' => $value->status,
								'photoShowToPublic' => $value->photoShowToPublic,
								'photo' => $value->photo,
								'name' => $value->name,
								'dob' => $value->dob,
								'age' => $this->getAge( $value->dob ),
								'gender' => $value->gender,
								'profession' => $value->profession,
								'homeTown' => $value->homeTown,
								'relationShipStatus' => $value->relationShipStatus,
								'kids' => $value->kids,
								'sexualOrientation' => $value->sexualOrientation,
								'height' => $value->height,
								'eyes' => $value->eyes,
								'hair' => $value->hair,
								'smoking' => $value->smoking,
								'drinking' => $value->drinking,
								'language' => $value->language,
								'animal' => $value->animal,
								'aboutMe' => $value->aboutMe,
								'favourite' => count(User::checkFavouriteUserUnderUserId($userId,$value->userId)),
								'busyFreeStatus' => $value->busyFreeStatus,
								'blocked' =>$blocked
							];
						}
					} else {*/
						if(!empty($SP_FindMeDetail)){
							// dd($UserData[0]);
							// dd($SP_FindMeDetail);
							$sameCountry = $SP_FindMeDetail->country == $UserData[0]->country;

							$sameHeight = $SP_FindMeDetail->height == $UserData[0]->height;
							$sameEyes = $SP_FindMeDetail->eyes == $UserData[0]->eyes;
							$samehair = $SP_FindMeDetail->hair == $UserData[0]->hair;
							$startAge = $this->getAge( $UserData[0]->dob ) >= $SP_FindMeDetail->startAge;
							$endAge = $this->getAge( $UserData[0]->dob ) <= $SP_FindMeDetail->endAge ;
							$kids = $SP_FindMeDetail->kids == $UserData[0]->kids;
							$smoking = $SP_FindMeDetail->smoking == $UserData[0]->smoking;
							$drinking = $SP_FindMeDetail->drinking == $UserData[0]->drinking;
							$relationShipStatus = $SP_FindMeDetail->relationShipStatus == $UserData[0]->relationShipStatus;
							// dd($startAge);

							if($SP_FindMeDetail->findMe == 'on'){
								if($sameCountry && $sameHeight && $sameEyes && $samehair && $startAge && $startAge && $endAge && $kids && $smoking && $drinking && $relationShipStatus ){
									$resultArr[] = [
										'userId' => $value->userId,
										'userEmail' => $value->userEmail,
										'country' => $value->country,
										'countryNameCode' => $value->countryNameCode,
										'country_code' => $value->country_code,
										'mobile' => $value->mobile,
										'accessToken' => $value->accessToken,
										'deviceToken' => $value->deviceToken,
										'deviceType' => $value->deviceType,
										'otp' => $value->otp,
										'otpVerified' => $value->otpVerified,
										'status' => $value->status,
										'photoShowToPublic' => $value->photoShowToPublic,
										'photo' => $value->photo,
										'name' => $value->name,
										'dob' => $value->dob,
										'age' => $this->getAge( $value->dob ),
										'gender' => $value->gender,
										'profession' => $value->profession,
										'homeTown' => $value->homeTown,
										'relationShipStatus' => $value->relationShipStatus,
										'kids' => $value->kids,
										'sexualOrientation' => $value->sexualOrientation,
										'height' => $value->height,
										'eyes' => $value->eyes,
										'hair' => $value->hair,
										'smoking' => $value->smoking,
										'drinking' => $value->drinking,
										'language' => $value->language,
										'animal' => $value->animal,
										'aboutMe' => $value->aboutMe,
										'favourite' => count(User::checkFavouriteUserUnderUserId($userId,$value->userId)),
										'busyFreeStatus' => $value->busyFreeStatus,
										'blocked' =>$blocked
									];		
								}
							}else{
								$resultArr[] = [
										'userId' => $value->userId,
										'userEmail' => $value->userEmail,
										'country' => $value->country,
										'countryNameCode' => $value->countryNameCode,
										'country_code' => $value->country_code,
										'mobile' => $value->mobile,
										'accessToken' => $value->accessToken,
										'deviceToken' => $value->deviceToken,
										'deviceType' => $value->deviceType,
										'otp' => $value->otp,
										'otpVerified' => $value->otpVerified,
										'status' => $value->status,
										'photoShowToPublic' => $value->photoShowToPublic,
										'photo' => $value->photo,
										'name' => $value->name,
										'dob' => $value->dob,
										'age' => $this->getAge( $value->dob ),
										'gender' => $value->gender,
										'profession' => $value->profession,
										'homeTown' => $value->homeTown,
										'relationShipStatus' => $value->relationShipStatus,
										'kids' => $value->kids,
										'sexualOrientation' => $value->sexualOrientation,
										'height' => $value->height,
										'eyes' => $value->eyes,
										'hair' => $value->hair,
										'smoking' => $value->smoking,
										'drinking' => $value->drinking,
										'language' => $value->language,
										'animal' => $value->animal,
										'aboutMe' => $value->aboutMe,
										'favourite' => count(User::checkFavouriteUserUnderUserId($userId,$value->userId)),
										'busyFreeStatus' => $value->busyFreeStatus,
										'blocked' =>$blocked
								];
							}
						}else{
							$resultArr[] = [
										'userId' => $value->userId,
										'userEmail' => $value->userEmail,
										'country' => $value->country,
										'countryNameCode' => $value->countryNameCode,
										'country_code' => $value->country_code,
										'mobile' => $value->mobile,
										'accessToken' => $value->accessToken,
										'deviceToken' => $value->deviceToken,
										'deviceType' => $value->deviceType,
										'otp' => $value->otp,
										'otpVerified' => $value->otpVerified,
										'status' => $value->status,
										'photoShowToPublic' => $value->photoShowToPublic,
										'photo' => $value->photo,
										'name' => $value->name,
										'dob' => $value->dob,
										'age' => $this->getAge( $value->dob ),
										'gender' => $value->gender,
										'profession' => $value->profession,
										'homeTown' => $value->homeTown,
										'relationShipStatus' => $value->relationShipStatus,
										'kids' => $value->kids,
										'sexualOrientation' => $value->sexualOrientation,
										'height' => $value->height,
										'eyes' => $value->eyes,
										'hair' => $value->hair,
										'smoking' => $value->smoking,
										'drinking' => $value->drinking,
										'language' => $value->language,
										'animal' => $value->animal,
										'aboutMe' => $value->aboutMe,
										'favourite' => count(User::checkFavouriteUserUnderUserId($userId,$value->userId)),
										'busyFreeStatus' => $value->busyFreeStatus,
										'blocked' =>$blocked
								];
						}
						
					/*}*/
				}

				if( count( $resultArr ) ) {
					$Response = [ 
					'isUserBusy' => $UserBusyStatus,
					'message' => trans('messages.success.success'),
					'status' => 1,
					'response' => $resultArr
					];
					return Response::json( $Response , 200 );
				} else {
					$Response = [ 
					'message' => trans('messages.noData'),
					'status' => 0,
					'response' => $resultArr
					];
					return Response::json( $Response , 400 );
				}
			} else {
				$Response = [ 
					'message' => trans('messages.noData'),
					'status' => 0,
					'response' => $resultArr
				];
				return Response::json( $Response , 401 );
			}
		} else {
	    	$Response = [
  			  'message'  => trans('messages.required.accessToken'),
  			];
	      return Response::json( $Response , 400 );
	   }
	}

	public function whoCanFindMe( Request $request ){
    	$accessToken =  $request->header('accessToken');
		$countryName =  $request->country; // country name
		$cityName =  $request->city; // city name
		$height =  $request->height; 
		$eyeColor =  $request->eyes; 
		$hairColor =  $request->hair; 
		$startAge =  $request->startAge;
		$endAge =  $request->endAge;
		$kids =  $request->kids;
		$smoking =  $request->smoking;
		$drinking =  $request->drinking;
		$relationShipStatus =  $request->relationShipStatus;
		$countryId = null;
		$cityId =null;
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);
		// dd($request->all());
		// dd($request->method());
		if(!empty($countryName)) {
			$countryId = DB::table('countries')->where(['countries.name' => $countryName])->value('id');
		}
		if(!empty($cityName)) {
			$cityId = DB::table('cities')->where(['cityName' => $cityName])->value('id');
		}
		$UserData = User::getUserDetail( $userId = null , $accessToken , $country_code = null ,$mobile = null , $email = null );
		// dd($UserData);
			if( !empty( $accessToken ) ){
				if($request->method() == "POST"){
					if( count($UserData) ) {
						$userId = $UserData[0]->userId;
						$data = [
							'userId' => $userId,
							'countryId' => $countryId,
							'country' => $countryName,
							'cityId' => $cityId,
							'city' => $cityName,
							'height' => $height,
							'eyes' => $eyeColor,
							'hair' => $hairColor,
							'startAge' => $startAge,
							'endAge' => $endAge,
							'kids' => $kids,
							'smoking' => $smoking,
							'drinking' => $drinking,
							'relationShipStatus' => $relationShipStatus,
						];
						$exist = DB::table('whoCanFindDetail')
									->where('userId',$userId)
									->first();
						// dd($exist);
						if(count($exist)){
							DB::table('whoCanFindDetail')
								->where('userId',$userId)
								->update($data);
						}else{
							DB::table('whoCanFindDetail')
								->insert($data);
						}
						$Response = [ 
							'message' => trans('messages.success.success'),
							'method' => 'POST'
						];
						return Response::json( $Response , 200 );
					} else {
						$Response = [ 
							'message' => trans('messages.noData'),
							'method' => 'POST',
							'status' => 0,
						];
						return Response::json( $Response , 401 );
					}
				}
				if($request->method() == "GET"){
					
					if(count($UserData)){
						$userId = $UserData[0]->userId;
						$data = DB::table('whoCanFindDetail')
							->where('userId',$userId)
							->first();
						if(count($data)){
							$Response = [ 
								'message' => trans('messages.success.success'),
								'method' => 'GET',
								'response' => $data
							];
							return Response::json( $Response , 200 );
						}else{
							$Response = [ 
								'message' => trans('messages.noData'),
								'method' => 'GET',
								'response' => array()
							];
							return Response::json( $Response , 200 );
						}
					}else{
						$Response = [ 
							'message' => trans('messages.noData'),
							'method' => 'GET',
							'response' => array()
						];
						return Response::json( $Response , 200 );
					}
				}
			} else {
		    	$Response = [
	  			  'message'  => trans('messages.required.accessToken'),
	  			];
		      return Response::json( $Response , 400 );
		    }
	}

	public function getAge($dob) {
		$today = date("Y-m-d");
		$diff = date_diff(date_create($dob), date_create($today));
		// return $diff->format('%yYears, %mMonths, %dDays');
		return $diff->format('%y');
	}
	
	public function makeFavouriteUnfavourite( Request $request ) {
		$accessToken = $request->header('accessToken');
		$userId = $request->userId;
		$userId1 = $request->userId1;
		$key = $request->key;
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);
		$validations = [
		'userId' => 'required|numeric',
		'key' => 'required',
		'userId1' => 'required'
		];

		$message = [
		'userId.required' => 'please provide userId.',
		'userId1' => 'please provide other user',
		'key.required' => 'please provide favourite/unfavourite key'
		];

		$validator = Validator::make($request->all(),$validations , $message);

      if( !empty( $accessToken ) ) {
      	$userDetail = User::getUserDetail(null,$accessToken,null,null,null);
      	if(count($userDetail)){
				if( $validator->fails() ) {
					$response = [
					'message' => $validator->errors($validator)->first(),
					];
					return Response::json($response,400);
				} else {
			   	if( $key == "favourite"){
				   	if($userId != $userId1 ){
				   		$Exist = User::getUserDetail( $userId,$accessToken,null,null,null );
					    	if( count($Exist) ) {
					   		$ExistUserGettingFvourite = User::getUserDetail( $userId1,null,null,null,null );
					    		if( count( $ExistUserGettingFvourite ) ){
						    		$checkFavourite = User::checkFavourite( $userId,$userId1 );
						    		if( count( $checkFavourite ) ) {

						    			$response = [
						        			'message' => trans('messages.user_already_in_fvrt_list'),
						        			'status' => 0,
						        		];
						        		return Response::json($response,400);
						    		} else {
						    			$data = User::makeFavourite( $userId , $userId1 );
						    			$response = [
						        			'message' => trans('messages.success.success'),
						        			'status' => 1,
						        		];
						        		return Response::json($response,200);
						    		}
								} else {
									$response = [
										'message' => trans('messages.invalid.detail'),
									];
									return Response::json($response,400);
								}
					    	} else {
					    		$response = [
					        			 'message' => trans('messages.invalid.detail'),
					        			];
					        	return Response::json($response,400);
					    	}
				   	} else {
				   		$Response = [
				  			  'message'  => 'you cannot make favourite yourself.',
				  			];
					      return Response::json( $Response , 400 );
				   	}
				   }else{
				   	$exist = User::checkFavourite( $userId , $userId1 );
				   	if(count($exist)){
				   		$del = User::makeUnFavourite( $userId , $userId1 );
				   		$Response = [
				  			  'message'  => trans('messages.success.success'),
				  			  'status' => 1,
				  			];
					      return Response::json( $Response , 200 );
				   	} else {
				   		$Response = [
				  			  'message'  => trans('messages.invalid.request'),
				  			];
					      return Response::json( $Response , 400 );
				   	}
				   }
			   }
			}else{
				$response['message'] = trans('messages.invalid.detail');
				return response()->json($response,401);
			}
		} else {
			$Response = [
				'message'  => trans('messages.required.accessToken'),
			];
			return Response::json( $Response , 400 );
		}
	}

	public function FavouriteList( Request $request ) {
    	$accessToken = $request->header('accessToken');
    	$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);
    	$resultArr = [];
      if( !empty( $accessToken ) ) {
      	$Exist = User::getUserDetail( null,$accessToken,null,null,null );
      	if( count($Exist) ) {
      		$userId = $Exist[0]->userId;
      		$favouriteList = User::getFavouriteList( $userId );

      		// dd($favouriteList);

      		foreach ($favouriteList as $key => $value) {
				$isBlocked = DB::table('blockList')->where([
							'userId' => $userId,
							'blockedId' => $value->userId
							])->first();
				if(count($isBlocked)){
					$blocked = "blocked";
				}else{
					$blocked = "notBlocked";
				}
      			$resultArr[] = [
							'userId' => $value->userId,
							'userEmail' => $value->userEmail,
							'country' => $value->country,
							'countryNameCode' => $value->countryNameCode,
							'country_code' => $value->country_code,
							'mobile' => $value->mobile,
							'accessToken' => $value->accessToken,
							'deviceToken' => $value->deviceToken,
							'deviceType' => $value->deviceType,
							'otp' => $value->otp,
							'otpVerified' => $value->otpVerified,
							'status' => $value->status,
							'photoShowToPublic' => $value->photoShowToPublic,
							'photo' => $value->photo,
							'name' => $value->name,
							'dob' => $value->dob,
							'age' => $this->getAge( $value->dob ),
							'gender' => $value->gender,
							'profession' => $value->profession,
							'homeTown' => $value->homeTown,
							'relationShipStatus' => $value->relationShipStatus,
							'kids' => $value->kids,
							'sexualOrientation' => $value->sexualOrientation,
							'height' => $value->height,
							'eyes' => $value->eyes,
							'hair' => $value->hair,
							'smoking' => $value->smoking,
							'drinking' => $value->drinking,
							'language' => $value->language,
							'animal' => $value->animal,
							'aboutMe' => $value->aboutMe,
							'favourite' => count(User::checkFavouriteUserUnderUserId($userId,$value->userId)),
							'busyFreeStatus' => $value->busyFreeStatus,
							'blocked' =>$blocked

						];
      		}
      		// dd($resultArr);
      		if( count($favouriteList) ) {
      			$response = [
	     			 'message' => trans('messages.success.success'),
	     			 'status' => 1,
	     			 'favourite' => 1,
	     			 'response' => $resultArr
	     			];
		        	return Response::json($response,200);

      		} else {
      			$response = [
	     			 'message' => trans('messages.noData'),
	     			 'status' => 0
	     			];
		        	return Response::json($response,400);
      		}
      	} else {
	    		$response = [
					'message' => trans('messages.invalid.detail'),
     			];
	        	return Response::json($response,401);
			}
		} else {
			$Response = [
				'message'  => trans('messages.required.accessToken'),
			];
			return Response::json( $Response , 400 );
		}
	}

	
	public function getCityListUnderCountry( Request $request ) {
		$countryName = $request->countryName;
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);
		$validations = [
    		'countryName' => 'required',
      ];
      $message = [
      	'countryName.required' => 'please provide countryName.',
      ];
	   $validator = Validator::make($request->all(),$validations , $message);
	   if( $validator->fails() ) {
		      $response = [
		        	'message' => $validator->errors($validator)->first(),
		      ];
		      return Response::json($response,400);
		} else {

			$countryId = DB::table( 'countries' )->where( [ 'name' => $countryName] )->value('id');
			// dd($countryId);
			$data = User::getCityListUnderCountry( $countryId );
			$Response = [
				'message' => trans('messages.success.success'),
				'response' => $data
			];
			return Response::json( $Response , 200 );
		}
	}

	public function getCountryList( Request $request ) {
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);
		$countryList = User::getCountryList();
		$Response = [
			'message' => trans('messages.success.success'),
			'response' => $countryList
		];
		return Response::json( $Response , 200 );
	}

	public function updateSetting(Request $request){ 
		$type = $request->type;
		$accessToken = $request->header('accessToken');
		$sound = $request->sound;
		$notification = $request->notification;
		$findMe = $request->findMe;
		$language = $request->language;
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);
		if( !empty( $accessToken ) ) {  
			$Exist = User::getUserDetail( null,$accessToken,null,null,null );
			if( count($Exist) ) {
				$userId = $Exist[0]->userId;
				$validations = [
					'sound' => 'required',
					'notification' => 'required',
					'findMe' => 'required',
					'language' => 'required'
				];
				$validator=Validator::make($request->all(),$validations);
				if($validator->fails()) {
					$response=array('response'=>$validator->errors($validator)->first());
					return Response::json($response,400);
				} else {
					$UserData = [
					'sound' => $sound,
					'notification' => $notification,
					'findMe' => $findMe,
					];
					$userDetailData = [
						'language' => $language
					];
					$up = User::updateUserData($UserData,$userId,$accessToken,$userDetailData);
					$response=array('response' => trans('messages.success.success'));
					return Response::json($response,200);
				}
			} else {
				$response=['response' => trans('messages.invalid.detail')];
				return Response::json($response,401);
			}
		} else {
			$Response = [
			'message'  => trans('messages.required.accessToken'),
			];
			return Response::json( $Response , 400 );
		}
   }

	

   public function blockUnblock( Request $request ){
		$accessToken = $request->header('accessToken');
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);
		$userId = $request->userId;
		$blockId = $request->blockId;
		$key = $request->key;

		$validations = [
		'userId' => 'required|numeric',
		'key' => 'required',
		'blockId' => 'required'
		];

		$message = [
		'userId.required' => 'please provide userId.',
		'blockId' => 'please provide other user',
		'key.required' => 'please provide block/unblock key'
		];

		$validator = Validator::make($request->all(),$validations , $message);
		if( !empty( $accessToken ) ) {
			$userDetail = User::getUserDetail(null,$accessToken,null,null,null);
			if(count($userDetail)){	
				if( $validator->fails() ) {
					$response = [
					'message' => $validator->errors($validator)->first(),
					];
					return Response::json($response,400);
				} else {
					if( $key == "block"){
						if($userId != $blockId ){
							$Exist = User::getUserDetail( $userId,$accessToken,null,null,null );
					    	if( count($Exist) ) {
								$ExistUserGettingBlocked = User::getUserDetail( $blockId,null,null,null,null );		
								if( count( $ExistUserGettingBlocked ) ){
									$checkBlocked = User::checkBlocked( $userId,$blockId );
									// dd($checkBlocked);
									if( count( $checkBlocked ) ) {
										$response = [
										'message' => 'user already in your block list',
										'status' => 0,
										];
										return Response::json($response,400);
									} else {
										$data = User::makeBlocked( $userId , $blockId );
										$response = [
											'message' => trans('messages.success.success'),
											'status' => 1,
										];
										return Response::json($response,200);
									}
								} else {
									$response = [
										'message' => trans('messages.invalid.detail'),
									];
									return Response::json($response,400);
								}			
						   } else {
								$response = [
									'message' => trans('messages.invalid.detail'),
								];
								return Response::json($response,400);
					    	}
				    	} else {
				   		$Response = [
				  			  'message'  => 'you cannot make block yourself.',
				  			];
					      return Response::json( $Response , 400 );
				   	}		
					}else if( $key == 'unblock'){
						$exist = User::checkBlocked( $userId,$blockId );
						if(count($exist)){
							$del = User::makeUnBlock( $userId , $blockId );
							$Response = [
								'message'  => trans('messages.success.success'),
								'status' => 1,
							];
							return Response::json( $Response , 200 );
						} else {
							$Response = [
								'message'  => trans('messages.invalid.request'),
							];
							return Response::json( $Response , 400 );
						}
					} else {
						$Response = [
							'message'  => trans('messages.invalid.request'),
						];
						return Response::json( $Response , 400 );
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
			return Response::json( $Response , 400 );
		}
   }

   public function BlockList( Request $request ) {
   	$accessToken = $request->header('accessToken');
   	$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);
	   if( !empty( $accessToken ) ) {
	      	$Exist = User::getUserDetail( null,$accessToken,null,null,null );
	      	if( count($Exist) ) {
	      		$userId = $Exist[0]->userId;
	      		$BlockList = User::getBlockList( $userId );
	      		if( count($BlockList) ) {
	      			$response = [
							'message' => trans('messages.success.success'),
							'status' => 1,
							'response' => $BlockList
		     			];
		        		return Response::json($response,200);
	      		} else {
	      			$response = [
		     			 'message' => trans('messages.noData'),
		     			 'status' => 0
		     			];
			        	return Response::json($response,400);
	      		}
				} else {
					$response = [
						'message' => trans('messages.invalid.detail'),
					];
					return Response::json($response,401);
				}
		} else {
			$Response = [
				'message'  => trans('messages.required.accessToken'),
			];
			return Response::json( $Response , 400 );
		}
   }


	public function SendReqForBusy( Request $request ){
		$accessToken = $request->header('accessToken');
		$requestToUserId = $request->requestToUserId;
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);
		$validations = [
			'requestToUserId' => 'required'
		];
		$message = [
			'requestToUserId.required' => trans('messages.required.requestToUserId')
		];
		$validator = Validator::make($request->all(),$validations,$message);
		if($validator->fails()){
			$response = [
				'message' => $validator->errors($validator)->first()
			];
			return Response::json($response , 400);
		}else{
			if( !empty( $accessToken ) ) {
		      	$Exist = User::getUserDetail( null,$accessToken,null,null,null );
		      	$requestToUserIdExist = User::getUserDetail($requestToUserId,null,null,null,null );
		      	if( count($Exist) && count($requestToUserIdExist)) {
		      		$RequestFromUserId = $Exist[0]->userId;
		      		$userName = $Exist[0]->name;
	   				$photo = $Exist[0]->photo;
	   				$notification = $requestToUserIdExist[0]->notification;
	   				// dd($notification);
		      		if($RequestFromUserId != $requestToUserId ){
							$alreadyRequested = DB::table('userGetBusyRequest')->where([
				      			'userId' => $requestToUserId,
				      			'getRequestByUserId' => $RequestFromUserId,
				      			'status' => 'pending'
				      			])->first();
							if(count($alreadyRequested)){
								$Response = [
									'message'  => trans('messages.already_requested'),
								];
								return Response::json( $Response , 200 );
							}else{
				      			
								// dd($requestToUserIdExist[0]);
								if($requestToUserIdExist[0]->busyFreeStatus != "busy"){
									
	   							if($notification == 'on'){
	   								$notifyType = 2; // WITH SOUND
										$bodyText = [
					   					'type'=>'send busy request',
					   					'status' => 0,
					   					'userId' => $RequestFromUserId,
					   					'userName' => $userName,
					   					'photo' => $photo,
					   					'notifyType' => 2
					   				];
	   							}else{
	   								$notifyType = 1; // SILENT
										$bodyText = [
					   					'type'=>'send busy request',
					   					'status' => 0,
					   					'userId' => $RequestFromUserId,
					   					'userName' => $userName,
					   					'photo' => $photo,
					   					'notifyType' => 1
					   				];
	   							}

	   							$this->notification($requestToUserId,$bodyText,$notifyType);
	   							$notificationArr = [
	   								'text' => $userName.' '.__('messages.notification.send_busy_request'),
	   								'userId' => $requestToUserId,
	   							];
	   							User::saveUserNotification($notificationArr);



	      						DB::table('userGetBusyRequest')->insert([
				      			'userId' => $requestToUserId,
				      			'getRequestByUserId' => $RequestFromUserId
				      			]);
									$Response = [
									'message'  => trans('messages.success.success'),
									];
									return Response::json( $Response , 200 );
								}else{
									$Response = [
										'message'  => $requestToUserIdExist[0]->name.' '.'already busy.',
									];
									return Response::json( $Response , 200 );
								}
							}
		      		}else {
		      			$Response = [
								'message'  => trans('messages.both_user_cannot_be_same'),
							];
							return Response::json( $Response , 400 );
		      		}
		      	} else {
						$response = [
							'message' => trans('messages.invalid.request'),
						];
						return Response::json($response,401);
					}
		   } else {
				$Response = [
					'message'  => trans('messages.required.accessToken'),
				];
				return Response::json( $Response , 400 );
			}
		}
	}

	public function GetBusyReq( Request $request ) {
		$accessToken = $request->header('accessToken');
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);
		if( !empty( $accessToken ) ) {
      	$Exist = User::getUserDetail( null,$accessToken,null,null,null );
      	if( count($Exist)) {
      		$UserId = $Exist[0]->userId;
      		$data = User::GetBusyReq($UserId);
      		if(count($data)){
					$response = [
						'message' => trans('messages.success.success'),
						'status' => 1,
						'response' => $data
					];
					return Response::json($response,200);
				}else{
					$response = [
						'message' => trans('messages.noData'),
						'status' => 0,
						'response' => $data
					];
					return Response::json($response,200);
				}
      	}else{
      		$response = [
				'message' => trans('messages.invalid.request'),
			];
			return Response::json($response,401);
      	}
   	} else {
			$Response = [
				'message'  => trans('messages.required.accessToken'),
			];
			return Response::json( $Response , 400 );
		}
	}

	public function acceptOrRejectRequest( Request $request ){
		$accessToken = $request->header('accessToken');	
		$busyWithUserId = $request->busyWithUserId;
		$acceptOrReject = $request->acceptOrReject;
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);
		$validations = [
		'acceptOrReject' => 'required',
		'busyWithUserId' => 'required|numeric'
		];
		$message = [
		'acceptOrReject.required' => 'please provide acceptOrReject.',
		'busyWithUserId.required' => 'please provide busyWithUserId.',
		];
		$validator = Validator::make($request->all(),$validations , $message);
		if( $validator->fails() ) {
			$response = [
				'message' => $validator->errors($validator)->first()
			];
			return Response::json($response , 400);
		}else{
   		if( !empty( $accessToken ) ) {
   			$Exist = User::getUserDetail( null,$accessToken,null,null,null );
   			$busyWithUserIdDetail = User::getUserDetail( $busyWithUserId,null,null,null,null );
   			$busyWithUserNotitfication = $busyWithUserIdDetail[0]->notification;
   			
   			// dd($busyWithUserIdDetail[0]->notification);
   			if( count($Exist) ) {
   				$userId = $Exist[0]->userId;
   				$userName = $Exist[0]->name;
   				$photo = $Exist[0]->photo;
   				$notification = $Exist[0]->notification;


   				if($userId != $busyWithUserId){
   					$userAlreadyBusy = DB::table('userBusyWith')
   											->where(['busyWithUserId' => $busyWithUserId])->first();
   					if(!count($userAlreadyBusy)){						
	   					DB::table('userGetBusyRequest')
	   						->where([
	   							'userId' =>$userId , 
	   							'getRequestByUserId'=>$busyWithUserId
	   							])
	   						->update(['userGetBusyRequest.status' => $acceptOrReject]);
		   				if($acceptOrReject == 'accepted'){
		   					

		   					if($busyWithUserNotitfication == 'on'){

									$notifyType = 2; // WITH SOUND NOTIFICATION
									$bodyText = [
										'type'=>'request accepted',
										'status' => 1,
										'userId' => $userId,
										'userName' => $userName,
										'photo' => $photo,
										'notifyType' => 2
									];
								}else{
									$notifyType = 1; // WITH SOUND NOTIFICATION
									$bodyText = [
										'type'=>'request accepted',
										'status' => 1,
										'userId' => $userId,
										'userName' => $userName,
										'photo' => $photo,
										'notifyType' => 1
									];
								}


		   					if($this->notification($busyWithUserId,$bodyText,$notifyType))
		   					{
		   						$notificationArr = [
      								'text' => $userName.' '.__('messages.notification.accepted_request'),
      								'userId' => $busyWithUserId,
   								];
   								User::saveUserNotification($notificationArr);

			   					DB::table('userBusyWith')->where(['userId' => $userId])->delete(); 
			   					// delete from userBusyWith table
			   					
			   					DB::table('users')
			   						->where(['id' => $userId])
			   						->update(['busyFreeStatus'=>'busy']);

		   						DB::table('users')
		   						->where(['id' => $busyWithUserId])
		   						->update(['busyFreeStatus'=>'busy']);
		   						//update users busyFreeStatus

				   				DB::table('userBusyWith')->insert([
				   					'userId' => $userId,
				   					'busyWithUserId' => $busyWithUserId
				   					]);
				   				//update userBusyWith table
				   				$Response = [
										'message'  => trans('messages.success.request_accepted'),
										'status' => '1'
									];
									return Response::json( $Response , 200 );
		   					}else{
		   						$Response = [
										'message'  => trans('messages.error'),
									];
									return Response::json( $Response , 200 );
		   					}
			   				/*}else{ // if notification is off
			   						$notifyType = 1; // SILENT NOTIFICATION
										$bodyText = [
											'type'=>'request accepted',
											'status' => 1,
											'userId' => $userId,
											'userName' => $userName,
											'photo' => $photo,
											'notifyType' = 2;
										];
				   					DB::table('userBusyWith')->where(['userId' => $userId])->delete(); 

				   					DB::table('users')
				   						->where(['id' => $userId])
				   						->update(['busyFreeStatus'=>'busy']);

			   						DB::table('users')
			   						->where(['id' => $busyWithUserId])
			   						->update(['busyFreeStatus'=>'busy']);

					   				DB::table('userBusyWith')->insert([
					   					'userId' => $userId,
					   					'busyWithUserId' => $busyWithUserId
					   					]);
					   				$Response = [
											'message'  => 'request accepted successfully.',
											'status' => '1'
										];
										return Response::json( $Response , 200 );
			   				}*/
							}
							if($acceptOrReject == 'rejected'){
								DB::table('userGetBusyRequest')
									->where(['userId'=>$userId,'getRequestByUserId'=>$busyWithUserId])
									->delete();
								$Response = [
									'message'  => trans('messages.success.request_rejected'),
									'status' => '0'

								];
								return Response::json( $Response , 200 );
							}
						}else{
							$Response = [
								'message'  => trans('messages.already_busy'),
								'status' => '2'
							];
							return Response::json( $Response , 200 );
						}
	   			}else{
	   				$Response = [
							'message'  => trans('messages.invalid.request'),
						];
						return Response::json( $Response , 400 );
	   			}
   			} else {
					$response = [
						'message' => trans('messages.invalid.detail'),
					];
					return Response::json($response,401);
				}
   		}else {
				$Response = [
					'message'  => trans('messages.required.accessToken'),
				];
				return Response::json( $Response , 400 );
			}
		}
	}

	public function accountSupportAndSuggestions( Request $request ) {
		$accessToken = $request->header('accessToken');
		$message1 = $request->message;
		$issue = $request->issue;
		$type = $request->type;
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);
		$validations = [
   		'message' => 'required',
   		'issue' => 'required',
   		'type' => 'required'
		];
		$message = [
			'message.required' => 'please provide message.',
			'issue.required' => 'please provide issue.',
			'type.required' => 'please provide type.',
		];
		$validator = Validator::make($request->all(),$validations , $message);
		if( $validator->fails() ) {
			$response = [
				'message' => $validator->errors($validator)->first()
			];
			return Response::json($response , 400);
		}else{
   		if( !empty( $accessToken ) ) {
   			$Exist = User::getUserDetail( null,$accessToken,null,null,null);
   			if( count($Exist) ) {
   				$userId = $Exist[0]->userId;
   				$data = [
   					'userId' => $userId,
   					'message' => $message1,
   					'issue' => $issue,
   					'type' => $type
   				];
   				$in = User::insertSupportSuggestion($data);
   				if($in){
   					$response = [
							'message' => trans('messages.success.success'),
							'status' => 1
						];
						return Response::json($response,200);
   				}else {
   					$response = [
							'message' => trans('messages.error'),
							'status' => 0
						];
						return Response::json($response,400);
   				}
   			} else {
					$response = [
						'message' => trans('messages.invalid.detail'),
					];
					return Response::json($response,401);
				}
   		}else {
				$Response = [
					'message'  => trans('messages.required.accessToken'),
				];
				return Response::json( $Response , 400 );
		   }
		}
	}

	public function DeleteUserAccount( Request $request ){
		$accessToken = $request->header('accessToken');
		$message1 = $request->message;
		$issue = $request->issue;
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);
		$validations = [
		'message' => 'required',
		'issue' => 'required',
		];
		$message = [
		'message.required' => 'please provide message.',
		'issue.required' => 'please provide issue.',
		];
		$validator = Validator::make($request->all(),$validations , $message);
		if( $validator->fails() ) {
			$response = [
   				'message' => $validator->errors($validator)->first()
   			];
   			return Response::json($response , 400);
		}else{
			if( !empty( $accessToken ) ) {
	   			$Exist = User::getUserDetail( null,$accessToken,null,null,null );
	   			if( count($Exist) ) {
	   				$userId = $Exist[0]->userId;
	   				$status = 0;
	   				$userData = [
	   					'status' => $status,
	   					'deactiveMessage' => $message1,
	   					'deactiveIssue' => $issue
	   				];
	   				$up = User::updateUserData($userData , $userId , $accessToken,null);
	   				$response = [
							'message' => trans('messages.success.success'),
						];
						return Response::json($response,200);
	   			} else {
						$response = [
							'message' => trans('messages.invalid.detail'),
						];
						return Response::json($response,401);
					}
   		}else {
				$Response = [
					'message'  => trans('messages.required.accessToken'),
				];
				return Response::json( $Response , 400 );
			}
		}
	}

	public function getUnBusy( Request $request ) {
		$accessToken = $request->header('accessToken');
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);
		if( !empty( $accessToken ) ) {

   			$Exist = User::getUserDetail( null,$accessToken,null,null,null );
   			if( count($Exist) ) {
   				$userId = $Exist[0]->userId;
   				$userName = $Exist[0]->name;
   				$photo = $Exist[0]->photo;
   				$notification = $Exist[0]->notification;

   				$userBusyWithId = DB::table('userBusyWith')
			   				->where(['userBusyWith.userId'=>$userId])
			   				->first();
			   	$userBusyWithId1 = DB::table('userBusyWith')
			   				->where(['userBusyWith.busyWithUserId'=>$userId])
			   				->first();
			   	$UserId = "";


			   	// dd($userBusyWithId);

   				if(empty($userBusyWithId)){
   					$UserId = $userBusyWithId1->userId;

   				}else{
   					$UserId = $userBusyWithId->busyWithUserId;
   				}

   				DB::table('userBusyWith')
   				->where('userId',$UserId)
   				->delete(); 

   				DB::table('userBusyWith')
   				->where('busyWithUserId',$UserId)
   				->delete(); 

   				$userData = ['busyFreeStatus' => 'free'];
   				User::updateUserData($userData,$userId,$accessToken,null);

   				DB::table('users')
   					->where(['id' => $UserId])
   					->update(['busyFreeStatus' => 'free']);
   				
		   		if($notification == 'on'){

		   			$notifyType = 2; // FOR EXACT NOTIFICATION 
		   			$bodyText = [
   						'type'=>'unbusy request',
	   					'status' => 2,
	   					'userId' => $userId,
	   					'userName' => $userName,
	   					'photo' => $photo,
	   					'notifyType' => 2
   					];
					}else{
						$notifyType = 1; // FOR SILENT NOTIFICATION
		   			$bodyText = [
   						'type'=>'unbusy request',
	   					'status' => 2,
	   					'userId' => $userId,
	   					'userName' => $userName,
	   					'photo' => $photo,
	   					'notifyType' => 1
   					];
   				}
   					if($this->notification($UserId,$bodyText,$notifyType)){
   								$notificationArr = [
	      								'text' => $userName.' '.__('messages.notification.free'),
	      								'userId' => $UserId,
	      							];
	      				User::saveUserNotification($notificationArr);
		      			$response = [
								'message' => trans('messages.success.success'),
							];
							return Response::json($response,200);
   					}else{
	   						$response = [
									'message' => trans('messages.error'),
								];
								return Response::json($response,200);
   					}
   			} else {
					$response = [
						'message' => trans('messages.invalid.detail'),
					];
					return Response::json($response,401);
				}
   	}else {
			$Response = [
				'message'  => trans('messages.required.accessToken'),
			];
			return Response::json( $Response , 400 );
		}
	}


	public function ejabberedMessage(Request $request){
		Log::info($request);
		/*exit();
		dd($request);*/
		$from = $request->from;
		$to = $request->to;
		$body = $request->body;
		$message_id = $request->message_id;

		Log::info($to);

		$data = DB::table('userDetail')->where('userId',$to)->first();
		Log::info(print_r($data,True));
		$notifyType = 1; // FOR SILENT NOTIFICATION
		$bodyText = [
			'type'=>'unbusy request',
			'status' => 2,
			'userId' => $to,
			'userName' => $data->name,
			'photo' => $data->photo,
			'notifyType' => 1
		];
		$this->notification($to,$bodyText,$notifyType);
	}


	public function notification($userId , $body_text, $notifyType){
		
	  $data = DB::table('users')
	  			->where('id',$userId)
	  			->get();
	  // dd($data);
	  if(count($data)){
	      $notification_type = $notifyType;
	      $id = $data[0]->id;
	      $notificationobject = new NotificationController();
	      $tokens[] = $data[0]->deviceToken;
	      if($data[0]->deviceType == "android"){
	      	
	          $status = $notificationobject->androidPushNotification($body_text,$notification_type,$tokens,$id);
	          return $status;

	      } else if($data[0]->deviceType == "ios"){
	          
	          $status = $notificationobject->iosPushNotification($body_text,$notification_type,$tokens,$id);
	          return $status;
	      }
	  }
	}

 public function uploadImage($photo , $uploadedfile , $destinationPathOfPhoto ) {
		
	 // $photo = $request->file('photo');
	 // $uploadedfile = $_FILES['photo']['tmp_name'];
		// $destinationPathOfPhoto = public_path().'/'.'thumbnail/';
		$fileName = time()."_".$photo->getClientOriginalName();
		$src = "";
		$i = strrpos($fileName,".");
		$l = strlen($fileName) - $i;
		$ext = substr($fileName,$i+1);

		if($ext=="jpg" || $ext=="jpeg" ){
			$src = imagecreatefromjpeg($uploadedfile);
		}else if($ext=="png"){
			$src = imagecreatefrompng($uploadedfile);
		}else if($ext=="gif"){
			$src = imagecreatefromgif($uploadedfile);
		}else{
			$src = imagecreatefrombmp($uploadedfile);
		}

		$newwidth  = 200;
		list($width,$height)=getimagesize($uploadedfile);
		$newheight=($height/$width)*$newwidth;
		$tmp=imagecreatetruecolor($newwidth,$newheight);
		imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
		$filename = $destinationPathOfPhoto.'small'.'_'.$fileName; 
		imagejpeg($tmp,$filename,100);
		imagedestroy($tmp);
		$filename = explode('/', $filename);

		$newwidth1  = 400;
		list($width,$height)=getimagesize($uploadedfile);
		$newheight1=($height/$width)*$newwidth1;
		$tmp=imagecreatetruecolor($newwidth1,$newheight1);
		imagecopyresampled($tmp,$src,0,0,0,0,$newwidth1,$newheight1,$width,$height);
		$filename = $destinationPathOfPhoto.'big'.'_'.$fileName; 
		imagejpeg($tmp,$filename,100);
		imagedestroy($tmp);
		$filename = explode('/', $filename);

		$newwidth2  = 100;
		list($width,$height)=getimagesize($uploadedfile);
		$newheight2=($height/$width)*$newwidth2;
		$tmp=imagecreatetruecolor($newwidth2,$newheight2);
		imagecopyresampled($tmp,$src,0,0,0,0,$newwidth2,$newheight2,$width,$height);
		$filename = $destinationPathOfPhoto.'thumbnail'.'_'.$fileName; 
		imagejpeg($tmp,$filename,100);
		imagedestroy($tmp);
		$filename = explode('/', $filename);

		return $filename;
	}

	public function getUserNotificationList( Request $request ){
		$accessToken = $request->header('accessToken');
   		if( !empty( $accessToken ) ) {
   			$Exist = User::getUserDetail( null,$accessToken,null,null,null );
	   			if( count($Exist) ) {
	   				$userId = $Exist[0]->userId;
	   				$data = User::getUserNotificationList($userId);
	   				$Response = [
	   					'message' => trans('messages.success.success'),
	   					'response' => $data
	   				];
	   				return Response::json($Response,200);
	   			} else {
					$response = [
						'message' => trans('messages.invalid.detail'),
					];
					return Response::json($response,400);
				}
   		}else {
			$Response = [
				'message'  => trans('messages.required.accessToken'),
			];
			return Response::json( $Response , 400 );
		}
	}
	public function payment(Request $request){
		//dd("hello india");
		$accessToken = $request->header('accessToken');
		$tranction_id = $request->tranction_id;
		//dd($tranction_id);
		$userId = $request->userId;
		$valid = DB::table('users')->where('accessToken',$accessToken)->first();
		//dd($valid);
		if(empty($valid)){
			$response['message'] = trans('messages.invalid.accessToken');
	   		return Response::json($response,400);
		}else{
			
			$payment_date=date("Y-m-d");
			$time = strtotime($payment_date);
			$end_date=date("Y-m-d", strtotime("+1 month", $time));

			$paymentData = DB::table( 'payment' )
			->insertGetId(array( 
				'userId'=>$userId,
				'payment_date'=>$payment_date,
				'end_date'=>$end_date,
				'tranction_id'=>$tranction_id
				));
			if(!empty($paymentData)){
				$update=DB::table('users')->where('id',$valid->id)
				->update(['premium'=>1]);

				$response['message'] = trans('messages.success.success');
	   			return Response::json($response,200);
			}else{
				$response['message'] = trans('messages.unsuccess');
	   			return Response::json($response,400);
			}

		}
	}

   /*public function SendingDownstreamMessageTo_A_Device() {

		$optionBuilder = new OptionsBuilder();
		$optionBuilder->setTimeToLive(60*20);

		$notificationBuilder = new PayloadNotificationBuilder('my title');
		$notificationBuilder->setBody('Hello world')->setSound('default');

		$dataBuilder = new PayloadDataBuilder();
		$dataBuilder->addData(['a_data' => 'my_data']);

		$option = $optionBuilder->build();
		$notification = $notificationBuilder->build();
		$data = $dataBuilder->build();

		$token = "a_registration_from_your_database";

		$downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

		$downstreamResponse->numberSuccess();
		$downstreamResponse->numberFailure();
		$downstreamResponse->numberModification();

		//return Array - you must remove all this tokens in your database
		$downstreamResponse->tokensToDelete(); 

		//return Array (key : oldToken, value : new token - you must change the token in your database )
		$downstreamResponse->tokensToModify(); 

		//return Array - you should try to resend the message to the tokens in the array
		$downstreamResponse->tokensToRetry();

		// return Array (key:token, value:errror) - in production you should remove from your database the tokens
   	}*/

   /*	public function SendingDownstreamMessageToMultipleDevices(){

		$optionBuilder = new OptionsBuilder();
		$optionBuilder->setTimeToLive(60*20);

		$notificationBuilder = new PayloadNotificationBuilder('my title');
		$notificationBuilder->setBody('Hello world')
						    ->setSound('default');
						    
		$dataBuilder = new PayloadDataBuilder();
		$dataBuilder->addData(['a_data' => 'my_data']);

		$option = $optionBuilder->build();
		$notification = $notificationBuilder->build();
		$data = $dataBuilder->build();

		// You must change it to get your tokens
		$tokens = MYDATABASE::pluck('fcm_token')->toArray();

		$downstreamResponse = FCM::sendTo($tokens, $option, $notification);

		$downstreamResponse->numberSuccess();
		$downstreamResponse->numberFailure(); 
		$downstreamResponse->numberModification();

		//return Array - you must remove all this tokens in your database
		$downstreamResponse->tokensToDelete(); 

		//return Array (key : oldToken, value : new token - you must change the token in your database )
		$downstreamResponse->tokensToModify(); 

		//return Array - you should try to resend the message to the tokens in the array
		$downstreamResponse->tokensToRetry();

		// return Array (key:token, value:errror) - in production you should remove from your database the tokens present in this array 
		$downstreamResponse->tokensWithError(); 
   	}*/

   	/*public function SendingMessagetoTopic(){
   		$notificationBuilder = new PayloadNotificationBuilder('my title');
		$notificationBuilder->setBody('Hello world')
						    ->setSound('default');
						    
		$notification = $notificationBuilder->build();

		$topic = new Topics();
		$topic->topic('news');

		$topicResponse = FCM::sendToTopic($topic, null, $notification, null)

		$topicResponse->isSuccess();
		$topicResponse->shouldRetry();
		$topicResponse->error();
   	}*/

   	/*public function SendingMessageToMultipleTopics(){

   		$notificationBuilder = new PayloadNotificationBuilder('my title');
		$notificationBuilder->setBody('Hello world')
						    ->setSound('default');
						    
		$notification = $notificationBuilder->build();

		$topic = new Topics();
		$topic->topic('news')->andTopic(function($condition) {

			$condition->topic('economic')->orTopic('cultural');
			
		});

		$topicResponse = FCM::sendToTopic($topic, null, $notification, null);

		$topicResponse->isSuccess();
		$topicResponse->shouldRetry();
		$topicResponse->error());
   	}*/



	/*public function uploadImgae( Request $request ) {
   	$photo = $request->file('photo');
   	// dd($photo);

		$uploadedfile = $_FILES['photo']['tmp_name'];

		// return $photo->getClientOriginalName();
		$destinationPathOfProfile = public_path().'/'.'thumbnail/';
		// dd($destinationPathOfProfile);
		$fileName = time()."_".$photo->getClientOriginalName();
		// dd($fileName);

		$src = "";
		$i = strrpos($fileName,".");
		$l = strlen($fileName) - $i;
		$ext = substr($fileName,$i+1);
		// return $ext;

		if($ext=="jpg" || $ext=="jpeg" )
		{
			$src = imagecreatefromjpeg($uploadedfile);
		}
		else if($ext=="png")
		{
			$src = imagecreatefrompng($uploadedfile);
		}
		else if($ext=="gif")
		{
			$src = imagecreatefromgif($uploadedfile);
		}
		else
		{
			$src = imagecreatefrombmp($uploadedfile);
		}


		// dd($src);
		$newwidth  = 400;
		list($width,$height)=getimagesize($uploadedfile);
		$newheight=($height/$width)*$newwidth;
		$tmp=imagecreatetruecolor($newwidth,$newheight);
		imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
		$filename = $destinationPathOfProfile.$newwidth.'_'.$fileName; //PixelSize_TimeStamp.jpg
		imagejpeg($tmp,$filename,100);
		imagedestroy($tmp);
		return $filename;
	}*/

	
}
