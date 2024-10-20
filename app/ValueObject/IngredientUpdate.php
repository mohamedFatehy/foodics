<?php

namespace App\ValueObject;

class IngredientUpdate
{

    public int $ingredientDailyStock;
    public int $currentStock;
    public int $decrementStock;
    public bool $isNotificationSent;

    public function __construct(int $ingredientDailyStock, int $currentStock, int $decrementStock, int $isNotificationSent)
    {
        $this->ingredientDailyStock = $ingredientDailyStock;
        $this->currentStock = $currentStock;
        $this->decrementStock = $decrementStock;
        $this->isNotificationSent = $isNotificationSent;
    }

    public function decreaseStockBy(float $decrementStock): IngredientUpdate
    {
        $this->decrementStock += $decrementStock;
        return $this;
    }

    public function shouldNotifyMerchant(): bool
    {
        $stockPercentage = ($this->currentStock - $this->decrementStock) / $this->ingredientDailyStock * 100;
        return $stockPercentage < 50 && !$this->isNotificationSent;
    }

    public function isStockNotEnough(): bool
    {
        return ($this->currentStock - $this->decrementStock ) < 0;
    }
}
