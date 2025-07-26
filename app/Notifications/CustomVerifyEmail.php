<?php

namespace App\Notifications;

use App\Mail\Auth\VerifyEmailMail;
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;

class CustomVerifyEmail extends BaseVerifyEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        
        // Get community stats for email
        $stats = $this->getCommunityStats();

        // Send using our custom mail class
        Mail::to($notifiable->email)->send(new VerifyEmailMail($notifiable, $verificationUrl, $stats));

        // Return empty MailMessage to satisfy interface
        return new MailMessage();
    }

    /**
     * Get community statistics for email
     *
     * @return array
     */
    private function getCommunityStats(): array
    {
        try {
            // Get real stats from database
            $userCount = \App\Models\User::where('is_active', 1)->count();
            $threadCount = \App\Models\Thread::count();
            $showcaseCount = \App\Models\Showcase::count();

            return [
                'users' => $userCount . '+',
                'discussions' => $threadCount . '+',
                'showcases' => $showcaseCount . '+'
            ];
        } catch (\Exception $e) {
            // Fallback stats if database query fails
            return [
                'users' => '64+',
                'discussions' => '118+',
                'showcases' => '25+'
            ];
        }
    }
}
