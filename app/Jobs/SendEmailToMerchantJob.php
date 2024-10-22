<?php

namespace App\Jobs;

use App\Mail\NotifyMerchantForIngredientMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendEmailToMerchantJob implements ShouldQueue
{
    use Queueable;

    public string $ingredientName;

    public string $merchantEmail;

    public string $merchantName;

    /**
     * Create a new job instance.
     */
    public function __construct(string $ingredientName, string $merchantName, string $merchantEmail)
    {
        $this->ingredientName = $ingredientName;
        $this->merchantEmail = $merchantEmail;
        $this->merchantName = $merchantName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->merchantEmail)->send(new NotifyMerchantForIngredientMail($this->ingredientName, $this->merchantName));
    }
}
