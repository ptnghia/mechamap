<?php

namespace App\Mail\Newsletter;

use App\Mail\BaseMail;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewsletterMail extends BaseMail
{
    public $subject;
    public $content;
    public $articles;
    public $unsubscribeUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($subject, $content, $articles = [], $unsubscribeUrl = null)
    {
        parent::__construct();
        $this->subject = $subject;
        $this->content = $content;
        $this->articles = $articles;
        $this->unsubscribeUrl = $unsubscribeUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter.newsletter',
            with: [
                'emailContent' => $this->content,
                'articles' => $this->articles,
                'unsubscribeUrl' => $this->unsubscribeUrl,
                'stats' => $this->stats,
            ],
        );
    }
}
