<?php

namespace database\seeders;

use App\Models\Ingredient;
use App\Models\Merchant;
use App\ValueObject\UnitConverter;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $merchant = Merchant::create([
            'name' => 'Merchant 1',
            'email' => 'test@test.com'
        ]);

        $beef = Ingredient::create([
            'merchant_id' => $merchant->id,
            'name' => 'Beef',
            'daily_total_stock' => UnitConverter::fromKgToGram(20),
            'current_stock' => UnitConverter::fromKgToGram(20),
            'alert_notification_sent' => false
        ]);

        $cheese = Ingredient::create(
            [
                'merchant_id' => $merchant->id,
                'name' => 'Cheese',
                'daily_total_stock' => UnitConverter::fromKgToGram(5),
                'current_stock' => UnitConverter::fromKgToGram(5),
                'alert_notification_sent' => false
            ]
        );

        $onion = Ingredient::create(
            [
                'merchant_id' => $merchant->id,
                'name' => 'Onion',
                'daily_total_stock' => UnitConverter::fromKgToGram(1),
                'current_stock' => UnitConverter::fromKgToGram(1),
                'alert_notification_sent' => false
            ]
        );


        $product = $merchant->products()->create([
            'name' => 'Cheese Burger'
        ]);

        // create cheeseburger ingredients with 150g Beef and 30g Cheese and 20g Onion
        $product->ingredients()->attach([
            $beef->id => ['ingredient_weight' => 150],
            $cheese->id => ['ingredient_weight' => 30],
            $onion->id => ['ingredient_weight' => 20]
        ]);

    }
}
