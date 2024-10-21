<?php

namespace App\Http\Middleware;

use App\Models\Client;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
class ApiPermission extends Middleware
{
    protected $except = [ 'api.home','api.data','api.page','api.services','api.service.details','api.property.index' ];
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {



        if (Auth::guard('api')->check()) {

            //user not verified
            if ((Auth::guard('api')->user()->verified_at == null)){
                return response()->json([
                    'status' => false,
                    'msg' => __('Account not verified yet'),
                    'code' => 308,
                    'data'=>false
                ],200);
            }
            if ((Auth::guard('api')->user()->status == 'in-active')){
                return response()->json([
                    'status' => false,
                    'msg' => __('Your Account is In Active'),
                    'code' => 309,
                    'data'=>false
                ],200);
            }
            if(!empty(Auth::guard('api')->user()->parent_id) ){

            //    Auth::guard('web')->login(Client::find(Auth::guard('api')->user()->parent_id));

//                Auth('api')::loginUsingId(Auth::guard('api')->user()->parent_id);
//                pd(Auth::id());
            }
        }else{

//                $token = 'cc1a39ecdca4bcfcad8336eb5484e134'; // md5(date('Y-m').'_Osouly');
//            $header_token = $request->header('token');
//            if($token != $header_token ){
//                  return response()->json([
//                    'status' => false,
//                    'msg' => __('In valid Token'),
//                    'code' => 307,
//                    'data'=>false
//                ],200);
//            }


        }
         return $next($request);
    }
}
