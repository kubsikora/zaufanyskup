<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public $formData;

    public function __construct($data)
    {
        $this->formData = $data;
    }

    public function build()
    {
        return $this->subject('Nowe zgłoszenie z zaufanyskup.pl')
                    ->view('emails.contact-form');
    }
}
