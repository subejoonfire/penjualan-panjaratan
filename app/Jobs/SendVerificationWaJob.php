<?php
namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendVerificationWaJob implements ShouldQueue
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
            $token = $user->phone_verification_token;
            $phone = $user->phone;
            $message = "Kode verifikasi WhatsApp Anda: $token\nPenjualan Panjaratan";
            $fonnteToken = env('FONNTE_TOKEN');
            Http::withHeaders([
                'Authorization' => $fonnteToken,
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $phone,
                'message' => $message,
            ]);
        }
    }
}