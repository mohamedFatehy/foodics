<?php

use App\ValueObject\IngredientUpdate;

it('test can update stock', function () {
    $ingredient = new IngredientUpdate(100, 80, 0, false);
    $ingredient->decreaseStockBy(30);
    expect($ingredient->decrementStock)->toBe(30);
});

it('test should notify merchant when stock falls below 50%', function () {
    $ingredient = new IngredientUpdate(100, 80, 40, false);
    $shouldNotify = $ingredient->shouldNotifyMerchant();
    expect($shouldNotify)->toBeTrue();
});

it('test should not notify merchant when notification is already sent', function () {
    $ingredient = new IngredientUpdate(100, 80, 40, true);
    $shouldNotify = $ingredient->shouldNotifyMerchant();
    expect($shouldNotify)->toBeFalse();
});

it('test does not notify merchant when stock is above 50%', function () {
    $ingredient = new IngredientUpdate(100, 80, 10, false);
    $shouldNotify = $ingredient->shouldNotifyMerchant();
    expect($shouldNotify)->toBeFalse();
});

it('test can detect if stock is not enough', function () {
    $ingredient = new IngredientUpdate(100, 50, 0, false);
    $ingredient->decreaseStockBy(60);
    $isStockNotEnough = $ingredient->isStockNotEnough();
    expect($isStockNotEnough)->toBeTrue();
});

it('test stock is enough when decrement is less than current stock', function () {
    $ingredient = new IngredientUpdate(100, 50, 0, false);
    $ingredient->decreaseStockBy(20);
    $isStockNotEnough = $ingredient->isStockNotEnough();
    expect($isStockNotEnough)->toBeFalse();
});

it('test stock is enough when decrement is exactly equal 0', function () {
    $ingredient = new IngredientUpdate(100, 50, 0, false);
    $ingredient->decreaseStockBy(50);
    $isStockNotEnough = $ingredient->isStockNotEnough();
    expect($isStockNotEnough)->toBeFalse();
});

it('test does not decrement stock if zero decrement is passed', function () {
    $ingredient = new IngredientUpdate(100, 50, 0, false);
    $ingredient->decreaseStockBy(0);
    expect($ingredient->decrementStock)->toBe(0);
});
