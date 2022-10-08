<?php

namespace App\Http\Utils;

use App\Models\Order;
use App\Models\Request;

class OrdersManagement
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

    /**
     * Returns an order
     * @param $id
     * @return null
     */
    public function getOrder($id)
    {
        $result = null;
        try {
            $result = Order::find($id);
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
        return $result;
    }

    /**
     * Updates the order status
     * @param $id
     * @param $status
     * @return bool
     */
    public function updateOrdertStatus($id, $status)
    {
        $result = false;
        try {
            $order = Order::find($id);
            if (!$order) {
                return $result;
            }

            $order->status = $status;
            $result = $order->save();
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
        return $result;
    }

    /**
     * Returns the new order status based on the request(payment) status
     * @param $requestStatus
     * @return mixed
     */
    public function getOrderStatus($requestStatus)
    {
        $status = [
            Request::PENDING_STATE => Order::CREATED_STATE,
            Request::APPROVED_STATE => Order::PAYED_STATE,
            Request::REJECTED_STATE => Order::REJECTED_STATE,
        ];

        return $status[$requestStatus];
    }

    /**
     * Determinate if the button that redirects to the processUrl is shown
     * @param $orderStatus
     * @return bool
     */
    public function showPaymentButton($orderStatus)
    {
        return $orderStatus === Order::CREATED_STATE;
    }

    /**
     * Determinate if the button that start a new payment requests is shown
     * @param $orderStatus
     * @return bool
     */
    public function showNewPaymentButton($orderStatus)
    {
        return $orderStatus === Order::REJECTED_STATE;
    }
}
