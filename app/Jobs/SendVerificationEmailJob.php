<?php
namespace App\Jobs;

use App\Models\User;
use App\Mail\VerifyEmailCode;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendVerificationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user_id;

    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    public function handle()
    {
        $user = User::find($this->user_id);
        if ($user) {
            Mail::to($user->email)->send(new VerifyEmailCode($user->username, $user->verification_token));
        }
    }
}