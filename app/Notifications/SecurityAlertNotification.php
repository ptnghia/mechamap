<?php

namespace App\Notifications;

use App\Mail\Security\SecurityAlertMail;
use Illuminate\Notifications\Notification;

class SecurityAlertNotification extends Notification
{
    // Critical email - always sent immediately (no queue)
    
    protected $alertType;
    protected $alertData;
    protected $ipAddress;
    protected $userAgent;

    public function __construct($alertType, $alertData = [], $ipAddress = null, $userAgent = null)
    {
        $this->alertType = $alertType;
        $this->alertData = $alertData;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
    }

    /**
     * Determine if this email should be queued
     * Security alerts are critical and should never be queued
     */
    public function shouldQueue(): bool
    {
        return false;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new SecurityAlertMail(
            $notifiable,
            $this->alertType,
            $this->alertData,
            $this->ipAddress,
            $this->userAgent
        ))->to($notifiable->email);
    }
}
