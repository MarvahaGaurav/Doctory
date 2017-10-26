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

    

    
}
