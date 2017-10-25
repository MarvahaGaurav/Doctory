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
use App\Subcategory;
use Hash;
use Auth;
use Exception;


class CategoryController extends Controller
{
	public function getCategoryList(Request $request){
		$categoryList = Category::Where(['status' => 1])->get();
		$response = [
			'message' => __('messages.success.success'),
			'response' => $categoryList
		];
		return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
	}

	public function getSubCategory(Request $request){
		$categoryId = $request->categoryId;
		$validations = [
			'categoryId' => 'numeric|required'
    	];
    	$validator = Validator::make($request->all(),$validations);
    	if($validator->fails()){
    		$response = [
				'message' => $validator->errors($validator)->first()
			];
			return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
    	}else{
			$SubcategoryList = Category::getSubCatByCatId($categoryId);
			$response = [
				'message' => __('messages.success.success'),
				'response' => $SubcategoryList
			];
			return response()->json($response,trans('messages.statusCode.ACTION_COMPLETE'));
		}
	}

	public function getSubCatAndCat(Request $request){
		$data = Category::getSubCatAndCat();
		$response = [
				'message' => __('messages.success.success'),
				'response' => $data
			];
			return response()->json($response,trans('messages.statusCode.ACTION_COMPLETE'));
	}
}
