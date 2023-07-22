<?php

namespace oval\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use oval;

class LogoutListener
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
     * Handle the event on user logout.
     *
     *	Delete the api_token for the user, and save tracking.
     *
     * @param  Logout  $event
     * @return void
     */
    public function handle(Logout $event)
    {
        $user = $event->user;
        if (!empty($user)) {
			$user->api_token = null;
			$user->save();

			$tracking = new oval\Models\Tracking;
			$tracking->user_id = $user->id;
			$tracking->event = "Logout";
			$tracking->event_time = date("Y-m-d H:i:s");
			$tracking->save();
		}
    }
}
