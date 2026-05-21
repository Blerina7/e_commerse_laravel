<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function build()
    {
        return $this->subject('Kodi juaj i verifikimit - Mini Amazon')
                    ->html("<h3>Mirëseerdhët në Mini Amazon!</h3><p>Kodi juaj për verifikimin e llogarisë është: <strong>{$this->code}</strong></p>");
    }
}