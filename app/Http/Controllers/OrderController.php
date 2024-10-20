<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Observability\LogEventIds;
use App\Services\OrderService;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

class OrderController extends ApiControllerController
{
    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Store new order with products
     */
    public function store(StoreOrderRequest $request)
    {
        try {
            $this->orderService->create($request->products);

            return self::SuccessResponse('order created', ResponseStatus::HTTP_CREATED);
        } catch (\Exception $e) {
            // log error for observability with event_id as identifier for the log searching
            Log::log('error', $e->getMessage(), ['event_id' => LogEventIds::LogEvent_In_Order_Creation]);

            return self::FailureResponse('failed to create an order');
        }
    }
}
