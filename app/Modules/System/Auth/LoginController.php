<?php

namespace App\Modules\System\Auth;

use App\Modules\System\SystemController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Illuminate\Http\Request;

class LoginController extends SystemController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/system';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return $this->view('auth.login');
    }

    protected function guard()
    {
        return Auth::guard('staff');
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        return redirect('/system/login');
    }

    /*protected function attemptLogin(Request $request)
    {
        dd('aaaaaaaaaaaa');
        return 'aaaaaaaaaaaaaaaa';
        return $this->guard()->attempt([
            'email'=> $request->email,
            'password'=> $request->password,
            'status'=>'active'
        ], $request->has('remember'));
    }*/

}
