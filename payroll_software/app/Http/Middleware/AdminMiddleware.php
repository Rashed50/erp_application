<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
use App\Events\LoginHistoryEvent;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
      /* user banner */
      // status 0 = user banned and 1 = active

        if (Auth::check() && Auth::user()->status == 0) {
            $banned = Auth::user()->status == '0';
            Auth::logout();
            if ($banned == 1) {
               $message = "Your Account Has Been Banned.Please Contact With Admin";
            }
            return redirect()->route('login')->with('status', $message)->withErrors([
                'banned' => 'Your Account Has been Banned. Please Contact Administrator'
            ]);
        }

      /* check authentication */
      if (Auth::check()) {
        $loging_user = Auth::user();
        $loging_user->ip = $request->ip();
        event(new LoginHistoryEvent($loging_user));
        return $next($request);
      }else{
         return redirect()->route('login');
      }


    }
}
