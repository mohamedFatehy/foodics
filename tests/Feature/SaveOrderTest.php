<?php

use App\Models\Ingredient;
use App\Models\Order;
use App\ValueObject\UnitConverter;
use database\seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

uses(RefreshDatabase::class); // Ensure the database is refreshed for each test

it('test returns validation error response when no request data provided', function () {
    $response = $this->postJson('/api/orders', []);
    $response->assertStatus(ResponseStatus::HTTP_UNPROCESSABLE_ENTITY);
});

it('test return validation error when wrong request data given', function () {
    $response = $this->postJson('/api/orders', ['products' => [
        ['product_id' => 1, 'quantity' => 2],
        ['product_id' => 2, 'quantity' => 3],
    ]]);
    $response->assertStatus(ResponseStatus::HTTP_UNPROCESSABLE_ENTITY);
});

it('test create order fails due to in sufficient quantity', function () {

    $this->seed(DatabaseSeeder::class);
    /*
     * seeder will create cheeseburger product with the following data
     *  - Beef 150g
     *  - Cheese 30g
     *  - Onion 20g
     *  the stock of each ingredient is 20K beef, 5K cheese, 1K onion respectively
     */

    $response = $this->postJson('/api/orders', ['products' => [
        ['product_id' => 1, 'quantity' => 60]
    ]]);

    // in case of onion 20*60 = 1200g which is more than the stock
    $response->assertStatus(ResponseStatus::HTTP_INTERNAL_SERVER_ERROR);
});

it('test create order successfully and Ingredients successfully deducted', function () {

    // arrange
    $this->seed(DatabaseSeeder::class);
    /*
     * seeder will create cheeseburger product with the following data
     *  - Beef 150g
     *  - Cheese 30g
     *  - Onion 20g
     *  the stock of each ingredient is 20K beef, 5K cheese, 1K onion respectively
     */

    $orderQuantity = 8;
    $response = $this->postJson('/api/orders', ['products' => [
        ['product_id' => 3, 'quantity' => $orderQuantity]
    ]]);

    $response->assertStatus(ResponseStatus::HTTP_CREATED);

    // assert order created
    $this->assertEquals(1, Order::count());
    $this->assertEquals(1, Order::first()->products->count());
    $this->assertEquals(3, Order::first()->products->first()->id);

    $newBeefStock = UnitConverter::fromKgToGram(20) - (150 * $orderQuantity);
    $newCheeseStock = UnitConverter::fromKgToGram(5) - (30 * $orderQuantity);
    $newOnionStock = UnitConverter::fromKgToGram(1) - (20 * $orderQuantity);

    // assert ingredients stock deducted
    $ingredients = Ingredient::all();
    foreach ($ingredients as $ingredient) {
        if ($ingredient->name === 'Beef') {
            $this->assertEquals($newBeefStock, $ingredient->current_stock);
        } elseif ($ingredient->name === 'Cheese') {
            $this->assertEquals($newCheeseStock, $ingredient->current_stock);
        } elseif ($ingredient->name === 'Onion') {
            $this->assertEquals($newOnionStock, $ingredient->current_stock);
        }
    }
});

it('test create order successfully and Ingredients successfully deducted with multiple products', function () {

    // arrange
    $this->seed(DatabaseSeeder::class);
    /*
     * seeder will create cheeseburger 2 product with the following data
     *  - Beef 150g
     *  - Cheese 30g
     *  - Onion 20g
     *-------------------------
     *  - Beef 200g
     *  - Cheese 50g
     *  - Onion 15g
     *
     *  the stock of each ingredient is 20K beef, 5K cheese, 1K onion respectively
     */

    $product1Quantity = 8;
    $product2Quantity = 5;
    $response = $this->postJson('/api/orders', ['products' => [
        ['product_id' => 5, 'quantity' => $product1Quantity],
        ['product_id' => 6, 'quantity' => $product2Quantity],
    ]]);

    $response->assertStatus(ResponseStatus::HTTP_CREATED);

    // assert order created
    $this->assertEquals(1, Order::count());
    $this->assertEquals(2, Order::first()->products->count());
    $this->assertEquals(5, Order::first()->products->first()->id);
    $this->assertEquals(6, Order::first()->products->get(1)->id);

    $beefNewStock = UnitConverter::fromKgToGram(20) - ((150 * $product1Quantity) + (200 * $product2Quantity));
    $cheeseNewStock = UnitConverter::fromKgToGram(5) - ((30 * $product1Quantity) + (50 * $product2Quantity));
    $onionNewStock = UnitConverter::fromKgToGram(1) - ((20 * $product1Quantity) + (15 * $product2Quantity));

    // assert ingredients stock deducted
    $ingredients = Ingredient::all();
    foreach ($ingredients as $ingredient) {
        if ($ingredient->name === 'Beef') {
            $this->assertEquals($beefNewStock, $ingredient->current_stock);
        } elseif ($ingredient->name === 'Cheese') {
            $this->assertEquals($cheeseNewStock, $ingredient->current_stock);
        } elseif ($ingredient->name === 'Onion') {
            $this->assertEquals($onionNewStock, $ingredient->current_stock);
        }
    }
});

