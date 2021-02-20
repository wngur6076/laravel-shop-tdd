<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegisterConfirmMail extends Mailable
{
    use Queueable, SerializesModels;

    public $confirmUrl;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($confirmUrl)
    {
        $this->confirmUrl = $confirmUrl;
        $this->view('emails.auth.confirm');
        $this->subject(
            sprintf('[%s] 회원가입을 확인해주세요.', config('app.name'))
        );
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this;
    }
}
