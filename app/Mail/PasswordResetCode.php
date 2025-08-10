<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class PasswordResetCode extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $resetCode;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, $resetCode)
    {
        $this->user = $user;
        $this->resetCode = $resetCode;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->view('emails.password-reset-code')
                    ->subject('Kode Reset Password - ' . config('app.name'))
                    ->with([
                        'user' => $this->user,
                        'resetCode' => $this->resetCode,
                    ]);
    }
}