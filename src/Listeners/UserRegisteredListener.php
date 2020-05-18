<?php

namespace App\Listeners;

use App\Events\UserRegisteredEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UserRegisteredListener
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
     * Handle the event.
     *
     * @param  \App\Events\UserRegisteredEvent $event
     * @return void
     */
    public function handle(UserRegisteredEvent $event)
    {
        $appName = env('APP_NAME', 'MH-87');
        $verificationUrl = 'http://' . env('APP_FRONTEND_URL') . '/auth/verify?code=' . $event->user->profile_code . '&email=' . $event->user->email;

        $statement = "Your account has been created on [$appName]. Pls verify with the link provided below.\n\n{$verificationUrl}";
        @mail($event->user->email, "[$appName] Welcome on-board!", $statement, []);
        return false;
    }
}
