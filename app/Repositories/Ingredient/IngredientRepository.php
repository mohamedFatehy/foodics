<?php

namespace App\Repositories\Ingredient;

use Illuminate\Support\Facades\DB;

class IngredientRepository implements IIngredientRepository
{
    public function update(array $ingredientUpdates): void
    {
        if (! empty($ingredientUpdates)) {
            $cases = [];
            $ids = [];

            foreach ($ingredientUpdates as $id => $update) {
                $cases[] = "WHEN id = $id THEN current_stock - $update->decrementStock";
                $ids[] = $id;
            }

            // Create a single raw query for batch updating the current stock
            $casesSql = implode(' ', $cases);
            $idsSql = implode(',', $ids);

            // Update all ingredients using one query for optimization
            DB::statement("UPDATE ingredients SET current_stock = CASE $casesSql END WHERE id IN ($idsSql)");
        }
    }
}
