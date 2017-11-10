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
use App\Qualification;
use App\DoctorQualification;
use App\Subcategory;
use Hash;
use Auth;
use Exception;

class QualificationController extends Controller
{
    public function getQualificationList(Request $request){
		Log::info('----------------------QualificationController--------------------------getQualificationList'.print_r($request->all(),True));
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);
    	
		$categoryList = Qualification::Where(['status' => 1])->get();
		$response = [
			'message' => __('messages.success.success'),
			'response' => $categoryList
		];
		return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
	}
}
