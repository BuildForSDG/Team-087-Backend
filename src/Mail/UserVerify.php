<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserVerify extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The user instance.
     *
     * @var User
     */
    protected $user;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $appName = env('APP_NAME', 'MH-87');
        $permittedFEs = explode(',', env('APP_FRONTEND_URL'));

        return $this->bcc('emadimabua@gmail.com', 'Emma')->subject("[$appName] Welcome on-board!")->view('email.user.verify', [
            'appName' => $appName,
            'firstName' => $this->user->first_name,
            'code' => $this->user->profile_code,
            'email' => $this->user->email,
            'verificationUrl' => $permittedFEs[0] . '/verify'
        ]);
    }
}
