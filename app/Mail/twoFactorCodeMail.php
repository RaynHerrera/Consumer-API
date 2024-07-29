<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
      
class TwoFactorCodeMail extends Mailable
{
    use Queueable, SerializesModels;
    public $emails;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($emails)
    {
        $this->emails = $emails;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('2-factor Code')
                    ->view('email.two-factor-code')
                    ->with('datas', $this->emails);
    }
}
