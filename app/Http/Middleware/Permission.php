<?php

namespace App\Http\Middleware;

use App\Models\Staff;
use Closure;
use Illuminate\Support\Facades\Auth;

class Permission
{
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
        if (Auth::guard()->check()) {
            $ignoredRoutes = [
                'system.dashboard',
                'system.test',
                'login',
                'logout',
                'system.misc.ajax',
                'system.staff.change-password',
                'system.staff.change-password-post',
                'system.notifications.url',
                'system.notifications.index',
                //null

                // Cloud
                'system.cloud.index',
                'system.cloud.show',
                'system.cloud.setting',
                'system.guest',
                // Cloud
            ];
            $canAccess = array_merge($ignoredRoutes,Staff::StaffPerms($request->user()->id)->toArray());
            if (!in_array(\Request::route()->getName(),$canAccess)){
                abort(401, 'Unauthorized.');
            }
        }

        return $next($request);
    }
}
