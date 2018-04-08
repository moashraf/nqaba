<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PasswordReset extends Mailable
{
    use Queueable, SerializesModels;
    protected $email,$link;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email,$link)
    {
        $this->email = $email;
        $this->link = $link;

        return $this->email .' - '. $this->link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('Mail.reset', ['email' => $this->email , 'link' => $this->link]);
    }
}
