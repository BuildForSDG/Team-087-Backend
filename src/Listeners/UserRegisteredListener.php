<?php

namespace App\Listeners;

use App\Events\UserRegisteredEvent;
use App\Mail\UserVerify;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail as MailFacade;
use SendGrid;
use SendGrid\Mail\{Content, From, Mail, To};

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
        $permittedFEs = explode(',', env('APP_FRONTEND_URL'));
        $verificationUrl = $permittedFEs[0] . '/verify?code=' . $event->user->profile_code . '&email=' . $event->user->email;

        $subject = "[$appName] Welcome on-board!";
        $messageBody = "Your account has been created on [$appName]. Kindly verify your e-mail address with the link provided below.\n\n{$verificationUrl}";


        if (app()->environment('local')) {
            @mail($event->user->email, $subject, $messageBody, []);
        } else {
            try {
                $from = new From("noreply@mental-lyf-staging.netlify.app", "Support");
                $to = new To($event->user->email, $event->user->first_name);
                $content = new Content("text/plain", $messageBody);
                $mail = new Mail($from, $to, $subject, $content);

                $response = (new SendGrid(env('SENDGRID_API_KEY')))->client->mail()->send()->post($mail);

                Log::info("Mail dispatch response (1):");
                Log::info("status-code: {$response->statusCode()}");
                Log::info("headers: {$response->body()}");
            } catch (\Exception $ex) {
                Log::error("Mail dispatch failed (1): {$ex->getMessage()}");
            }

            try {
                $response = MailFacade::to($event->user->email)->send(new UserVerify($event->user));

                Log::info("Mail dispatch response (2):");
                Log::info($response);
            } catch (\Exception $ex) {
                Log::error("Mail dispatch failed (2): {$ex->getMessage()}");
            }
        }
    }
}
