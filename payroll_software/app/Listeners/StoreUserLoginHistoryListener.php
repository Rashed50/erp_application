<?php

namespace App\Listeners;

use App\Events\LoginHistoryEvent;
use Illuminate\Support\Facades\Request;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Http\Controllers\DataServices\AuthenticationDataService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StoreUserLoginHistoryListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  \App\Providers\LoginHistoryEvent  $event
     * @return void
     */
    public function handle(LoginHistoryEvent $event)
    {

        // Define a unique key for this specific request.
        // We'll combine the user ID and a timestamp to be extra safe,
        // though the `__once_per_request` key alone is often sufficient.
        $sessionKey = 'login_history_logged_' . $event->user->id . '_' . now()->timestamp;

        // Use a simple and effective session flag to prevent duplicate entries.
        // We check if the flag is not set, and if not, we set it and proceed.
        if (Request::session()->has($sessionKey)) {
            return;
        }

        // This is a more robust check that also handles the case where the key might be
        // present but needs to be "reset" for a new login.
        if (Request::session()->get('login_handled', false)) {
            return;
        }

        // Set a session flag to prevent this listener from running again
        // for the same request.
        Request::session()->put('login_handled', true);

        $current_timestamp = Carbon::now()->toDateTimeString();
        // dd('login ip is ',$event->user->ip);

        $loging_user = $event->user;
         DB::table('login_histories')->insert(
            [
                'user_id' => $loging_user->id,
                'email' => $loging_user->email,
                'login_ip' =>$loging_user->ip,
                'created_at' => $current_timestamp, 'updated_at' => $current_timestamp
            ]
        );

      //  (new AuthenticationDataService())->insertLoginUserHistory($event->user, $current_timestamp);

    }
}
