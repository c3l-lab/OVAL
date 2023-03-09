<?php

namespace oval\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use oval;

class LoginListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event on user login.
     *
     *	Set api_token for the user, and save tracking.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = $event->user;
        $user->api_token = str_random(60);
        $user->save();
        
        $tracking = new oval\Tracking;
		$tracking->user_id = $user->id;
		$tracking->event = "Login";
		$tracking->event_time = date("Y-m-d H:i:s");
		$tracking->save();
    }
}
