<?php

namespace App\Http\Middleware;
use \App\User;
use Response;
use Closure;

class PatientStatusAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {   
        if(!empty(($request->header('accessToken')))){
            $userDetail = User::where(['remember_token' => $request->header('accessToken')])->first();
            if(empty($userDetail)){
                $Response = [
                  'message'  => trans('messages.invalid.detail'),
                ];
                return Response::json( $Response , trans('messages.statusCode.INVALID_ACCESS_TOKEN') );
            }else{

                // dd($userDetail);
                if(!empty($userDetail) && $userDetail['status'] == 0 && $userDetail['user_type'] == 2){
                    $response = [
                        'message' =>  __('messages.Account_blocked_Patient'),
                        'status' => $userDetail['status'],
                        'response' => []
                    ];
                    return response()->json($response,__('messages.statusCode.INVALID_ACCESS_TOKEN'));
                }
                return $next($request);
            }
        } else {
            $response['message'] = __('messages.required.accessToken');
            return response()->json($response,401);
        }
    }
}
