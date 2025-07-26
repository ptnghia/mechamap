<?php

namespace App\Listeners;

use App\Mail\Auth\WelcomeMail;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Queue configuration for welcome emails
     */
    public $queue = 'emails-welcome';
    public $delay = 10; // 10 seconds delay to batch emails
    public $tries = 3;
    public $timeout = 60;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Verified $event): void
    {
        // Send welcome email after user verifies their email
        Mail::to($event->user->email)->send(new WelcomeMail($event->user));
    }
}
