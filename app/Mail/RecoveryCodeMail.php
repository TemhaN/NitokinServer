<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RecoveryCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $recoveryCode;

    public function __construct(User $user, $recoveryCode)
    {
        $this->user = $user;
        $this->recoveryCode = $recoveryCode;
    }

    public function build()
    {
        return $this->subject('Код восстановления доступа')
                    ->view('emails.recovery_code')
                    ->with([
                        'recoveryCode' => $this->recoveryCode,
                    ]);
    }
}