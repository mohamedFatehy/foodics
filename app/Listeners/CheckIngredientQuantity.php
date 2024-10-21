<?php

namespace App\Listeners;

use App\Events\OrderCreatedEvent;
use App\Jobs\SendEmailToMerchantJob;
use App\Models\Ingredient;
use App\Observability\LogEventIds;
use Illuminate\Support\Facades\Log;

class CheckIngredientQuantity
{
    /**
     * Handle the event.
     */
    public function handle(OrderCreatedEvent $event): void
    {
        try {
            foreach ($event->updatedIngredients as $id  => $ingredient) {
                if ($ingredient->shouldNotifyMerchant()) {
                    $ingredientModel = Ingredient::with('merchant')->find($id);
                    if ($ingredientModel->alert_notification_sent) {
                        continue;
                    }
                    $ingredientModel->update(['alert_notification_sent' => true]);
                    // Send email to the merchant for the ingredient shortage in Queue
                    SendEmailToMerchantJob::dispatch($ingredientModel->name,$ingredientModel->merchant->name,$ingredientModel->merchant->email);
                }
            }
        } catch (\Exception $e) {
            Log::log('error', $e->getMessage(), ['event_id' => LogEventIds::LogEvent_In_Sending_Email_To_Merchant]);
        }}
}
