<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotifyMerchantForIngredientMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $ingredientName;

    public string $merchantName;

    /**
     * Create a new message instance.
     */
    public function __construct(string $ingredientName, string $merchantName)
    {
        $this->ingredientName = $ingredientName;
        $this->merchantName = $merchantName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: 'no-reply@foodics.com',
            subject: 'Foodics: Ingredient Shortage Alert',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email',
            with: [
                'content' => 'Ingredient shortage alert for ( '.$this->ingredientName.' ) in your inventory is already less than 50% of the daily total stock.',
                'merchantName' => $this->merchantName,
            ],
        );
    }
}
