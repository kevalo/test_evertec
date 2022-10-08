<?php

namespace App\Http\Utils;

use App\Models\Order;

class OrdersManagment
{
    /**
     * Saves a new order for a customer
     * @param $customerId int
     * @return Order|null
     */
    public function saveOrder($customerId)
    {
        $result = null;
        try {
            $order = new Order();
            $order->status = Order::CREATED_STATE;
            $order->customer_id = $customerId;

            if ($order->save()) {
                $result = $order;
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
        return $result;
    }
}
