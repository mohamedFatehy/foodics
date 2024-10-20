<?php

namespace App\Repositories\Order;

use App\Models\Order;

interface IOrderRepository
{
    public function createOrder(array $products): Order;
}
