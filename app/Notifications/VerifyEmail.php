<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends VerifyEmailBase
{
    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    public function verificationUrl($notifiable)
    {
        $prefix = env('CLIENT_APP_URL', 'http://localhost:8000') . '/verify-email?verificationUrl=';

        $temporarySignedURL = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $notifiable->getKey()
            ]
        );

        return $prefix . urlencode($temporarySignedURL);
    }
}
