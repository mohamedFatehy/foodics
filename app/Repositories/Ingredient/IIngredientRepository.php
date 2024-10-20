<?php

namespace App\Repositories\Ingredient;

interface IIngredientRepository
{
    public function update(array $ingredientUpdates): void;
}
