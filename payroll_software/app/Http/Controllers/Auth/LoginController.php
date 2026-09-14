<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Auth,Exception;
class LoginController extends Controller
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
    protected $redirectTo = RouteServiceProvider::HOME;
      protected function redirectTo(){
         if (Auth()->user()) {
             return route('admin.dashboard');
         }
      }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
      //  $this->middleware('guest')->except('logout');
    }
    
    
    public function showLoginForm()
    {

        return view('auth.login');
    }


    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            'username' => [trans('auth.failed')],
        ]);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */


    public function username()
    {
        $login = request()->input('username');

        if(is_numeric($login)){
            $field = 'phone_number';
        } elseif (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $field = 'email';
        } else {
            $field = 'username';
        }

        request()->merge([$field => $login]);

        return $field;
    }
    
     //  API Login Called Here
    /*
    public function login(Request $request)
    {

        $this->validateLogin($request);
        if ($this->attemptLogin($request)) {
            $user = $this->guard()->user();
            $user->generateToken();
            return response()->json([
                'data' => $user->toArray(),
            ]);
        }
        return $this->sendFailedLoginResponse($request);
    }

    */

    public function logout(Request $request)
    {
        $user = Auth()->user();
        if ($user) {
            $user->remember_token = null;
            $user->save();
        }
        Auth::logout();
        return redirect('/login');
    }


     // 3 this called after credentials method
    public function authenticated(Request $request, $user)
    {
      // 
        if ($user->status == 0) {
            Auth()->logout();
            return back()->with('error', 'Your account is not active. Please contact with administrator.');
        }
        return redirect()->intended($this->redirectPath());
    }
    
      

}
