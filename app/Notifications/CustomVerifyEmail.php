<?php

namespace App\Notifications;

use App\Mail\Auth\VerifyEmailMail;
use App\Notifications\CriticalEmailNotification;
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends BaseVerifyEmail
{
    // Critical email - always sent immediately (no queue)

    /**
     * Determine if this email should be queued
     * Email verification is critical and should never be queued
     */
    public function shouldQueue(): bool
    {
        return false;
    }

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

        // Return our custom mail class with to() method
        return (new VerifyEmailMail($notifiable, $verificationUrl, $stats))
            ->to($notifiable->email);
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
