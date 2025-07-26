<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class GeneralEmailNotification extends QueueableEmailNotification
{
    protected $emailType;
    protected $subject;
    protected $greeting;
    protected $lines;
    protected $actionText;
    protected $actionUrl;
    protected $salutation;

    public function __construct(
        string $emailType,
        string $subject,
        string $greeting = 'Xin chào!',
        array $lines = [],
        string $actionText = null,
        string $actionUrl = null,
        string $salutation = 'Trân trọng, Đội ngũ MechaMap'
    ) {
        $this->emailType = $emailType;
        $this->subject = $subject;
        $this->greeting = $greeting;
        $this->lines = $lines;
        $this->actionText = $actionText;
        $this->actionUrl = $actionUrl;
        $this->salutation = $salutation;

        // Set queue based on email type
        $this->queue = $this->getQueueName();
    }

    /**
     * Get the email type for this notification
     */
    protected function getEmailType(): string
    {
        return $this->emailType;
    }

    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject($this->subject)
            ->greeting($this->greeting);

        // Add content lines
        foreach ($this->lines as $line) {
            $message->line($line);
        }

        // Add action button if provided
        if ($this->actionText && $this->actionUrl) {
            $message->action($this->actionText, $this->actionUrl);
        }

        return $message->salutation($this->salutation);
    }
}
