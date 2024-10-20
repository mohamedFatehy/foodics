<?php

namespace App\Repositories\Order;


use App\Models\Order;

class OrderRepository implements IOrderRepository
{

    /**
     * @param array $products
     * @return Order
     */
    public function createOrder(array $products): Order
    {
        $order = Order::create([]);

        $order->products()->attach(collect($products)->transform(function ($product) {
            return ['product_id' => $product['product_id'], 'quantity' => $product['quantity']];
        }));

        $order->load('products.ingredients');
        return $order;
    }
}
