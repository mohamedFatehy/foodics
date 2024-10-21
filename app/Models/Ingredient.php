<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ingredient extends Model
{
    protected $fillable = ['merchant_id', 'name', 'daily_total_stock', 'current_stock', 'alert_notification_sent'];


    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }
}
