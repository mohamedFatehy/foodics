<?php

namespace App\Services;

use App\Events\OrderCreatedEvent;
use App\Models\Order;
use App\Repositories\Ingredient\IIngredientRepository;
use App\Repositories\Order\IOrderRepository;
use App\ValueObject\IngredientUpdate;
use Illuminate\Support\Facades\DB;

class OrderService
{
    private IOrderRepository $orderRepository;

    private IIngredientRepository $ingredientRepository;

    public function __construct(IOrderRepository $orderRepository, IIngredientRepository $ingredientRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->ingredientRepository = $ingredientRepository;
    }

    /**
     * @throws \Exception
     */
    public function create(array $products): void
    {
        DB::beginTransaction();

        $order = $this->orderRepository->createOrder($products);

        $updatedIngredients = $this->getUpdatedIngredients($order);
        $this->ingredientRepository->update($updatedIngredients);

        // TODO notify the merchant about the insufficient stock
        OrderCreatedEvent::dispatch($updatedIngredients);
        DB::commit();
    }

    /**
     * @throws \Exception
     */
    private function getUpdatedIngredients(Order $order): array
    {
        $ingredientUpdates = [];
        $order->products->each(function ($product) use (&$ingredientUpdates) {
            $product->ingredients->each(
                function ($ingredient) use (&$ingredientUpdates, $product) {
                    $ingredientUpdates = $this->createOrUpdateIngredient($ingredientUpdates, $product, $ingredient);
                    $this->validateIngredientStocks($ingredientUpdates[$ingredient->id], $ingredient);

                });
        });

        return $ingredientUpdates;
    }

    /**
     * @throws \Exception
     */
    public function validateIngredientStocks(IngredientUpdate $ingredientUpdates, $ingredient): void
    {
        if ($ingredientUpdates->isStockNotEnough()) {
            throw new \Exception('Stock is not enough for this order due to insufficient stock of ingredient id: '.$ingredient->id);
        }
    }

    public function createOrUpdateIngredient($ingredientUpdates, $product, $ingredient): array
    {
        if (! array_key_exists($ingredient->id, $ingredientUpdates)) {
            $ingredientUpdates[$ingredient->id] = new IngredientUpdate(
                $ingredient->daily_total_stock,
                $ingredient->current_stock,
                $ingredient->pivot->ingredient_weight * $product->pivot->quantity,
                $ingredient->alert_notification_sent
            );
        } else {
            $ingredientUpdates[$ingredient->id] = $ingredientUpdates[$ingredient->id]->decreaseStockBy($ingredient->pivot->ingredient_weight * $product->pivot->quantity);
        }

        return $ingredientUpdates;
    }
}
