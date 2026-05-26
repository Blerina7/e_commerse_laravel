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
                    ->html("<h3>Mirseerdhet ne Mini Amazon!</h3><p>Kodi juaj per verifikimin e llogarise eshte: <strong>{$this->code}</strong></p>");
    }
}