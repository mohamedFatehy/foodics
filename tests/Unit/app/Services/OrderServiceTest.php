<?php

namespace Tests\Unit;

use App\Models\Ingredient;
use App\Repositories\Ingredient\IIngredientRepository;
use App\Repositories\Order\IOrderRepository;
use App\Services\OrderService;
use App\ValueObject\IngredientUpdate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

uses(RefreshDatabase::class); // Ensure the database is refreshed for each test

beforeEach(function () {
    $this->orderRepository = Mockery::mock(IOrderRepository::class);
    $this->ingredientRepository = Mockery::mock(IIngredientRepository::class);
    $this->orderService = new OrderService($this->orderRepository, $this->ingredientRepository);
});

afterEach(function () {
    Mockery::close();
});

it('test throws an exception when ingredient stock is not enough', function () {
    $ingredient = new Ingredient(['id' => 1]);
    $ingredientUpdates = new IngredientUpdate(100, 20, 50, false);

    expect(fn () => $this->orderService->validateIngredientStocks($ingredientUpdates, $ingredient))
        ->toThrow(\Exception::class, 'Stock is not enough for this order due to insufficient stock of ingredient id: '.$ingredient->id);
});

it('test does not throw an exception when ingredient stock is sufficient', function () {

    $ingredient = new Ingredient(['id' => 1]);
    $ingredientUpdates = new IngredientUpdate(100, 50, 30, false);
    $this->orderService->validateIngredientStocks($ingredientUpdates, $ingredient);
    expect(true)->toBeTrue();
});

it('test creates a new ingredient update if it does not exist', function () {

    $ingredientUpdates = [];
    $ingredient = new Ingredient;
    $ingredient->id = 1;
    $ingredient->daily_total_stock = 100;
    $ingredient->current_stock = 80;
    $ingredient->pivot = (object) ['ingredient_weight' => 20];
    $ingredient->alert_notification_sent = false;

    $product = (object) ['pivot' => (object) ['quantity' => 2]];

    $updatedIngredients = $this->orderService->createOrUpdateIngredient($ingredientUpdates, $product, $ingredient);

    expect($updatedIngredients)->toHaveKey($ingredient->id)
        ->and($updatedIngredients[$ingredient->id]->decrementStock)->toBe(40);
});

it('test updates an existing ingredient when it already exists', function () {
    $ingredientUpdates = [
        1 => new IngredientUpdate(100, 80, 10, false),
    ];
    $ingredient = new Ingredient;
    $ingredient->id = 1;
    $ingredient->daily_total_stock = 100;
    $ingredient->current_stock = 80;
    $ingredient->pivot = (object) ['ingredient_weight' => 20];
    $ingredient->alert_notification_sent = false;

    $product = (object) ['pivot' => (object) ['quantity' => 2]]; // Product with quantity 2

    $updatedIngredients = $this->orderService->createOrUpdateIngredient($ingredientUpdates, $product, $ingredient);

    expect($updatedIngredients)->toHaveKey(1)
        ->and($updatedIngredients[1]->decrementStock)->toBe(50);
});
