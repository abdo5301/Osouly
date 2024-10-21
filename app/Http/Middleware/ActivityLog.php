<?php

namespace App\Http\Middleware;

use App\Models\Staff;
use Spatie\Activitylog\Models\Activity as Activity;
use App\Models\ActivityLog as ActivityLogModel;
use Closure;
use Illuminate\Support\Facades\Auth;

class ActivityLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     */
//    public function handle($request, Closure $next)
//    {
//
//
//        if (Auth::guard()->check()) {
//            ActivityLogModel::create([
//                'log_name' => 'default',
//                'description' => \Request::route()->getName(),
//                'subject_id' => (isset($request->segments()[2]))?$request->segments()[2]:'',
//                'subject_type' => '',
//                'causer_id' => Auth::id(),
//                'causer_type' => 'App\Models\Staff',
//                'properties'=>serialize( array_merge($request->toArray(),$request->segments())),
//                'ip'=>$_SERVER['REMOTE_ADDR'],
//                'user_agent'=> $_SERVER['HTTP_USER_AGENT'],
//                'url'=>$request->url(),
//                'method'=>$request->method()
//            ]);
//
//        }
//
//        return $next($request);
//    }
}
