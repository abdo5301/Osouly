<?php

namespace App\Http\Middleware;

use App\Models\Staff;
use Closure;
use Illuminate\Support\Facades\Auth;

class Language
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
            if(in_array($request->language,['ar','en'])){
                Staff::find(Auth::id())->update([
                    'language'=> $request->language
                ]);
                \App::setLocale($request->language);

                if($request->backByLanguage){
                    return redirect()->back();
                }

            }else{
                \App::setLocale(Auth::user()->language);
            }
        }else{

            if(isset($request->lang) && in_array($request->lang,['ar','en'])) {
                \App::setLocale($request->lang);
            }else {
                \App::setLocale('ar');
            }
        }



        return $next($request);
    }
}
