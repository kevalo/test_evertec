<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomer;
use App\Http\Utils\CustomersManagement;
use App\Http\Utils\OrdersManagement;
use App\Http\Utils\RequestsManagement;
use App\Models\Order;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Http\Utils\WebCheckout;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class OrderController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(OrdersManagement $ordersManagement)
    {
        if (!Auth::id()) {
            return redirect(route('login'));
        }

        $orders = $ordersManagement->getAllOrders();
        return view('dashboard', ['orders' => $orders]);
    }

    /**
     * Creates a new order
     * @param Request $request
     * @param CustomersManagement $customersManagement
     * @param OrdersManagement $ordersManagement
     * @param RequestsManagement $requestsManagement
     * @param WebCheckout $webCheckout
     * @return Application|RedirectResponse|Redirector|void
     */
    public function store(
        StoreCustomer       $request,
        CustomersManagement $customersManagement,
        OrdersManagement    $ordersManagement,
        RequestsManagement  $requestsManagement,
        WebCheckout         $webCheckout
    )
    {
        try {
            $customer = $customersManagement->saveCustomer($request->validated());

            if (!$customer) {
                $this->redirectWithError('forms.shopping', Config::get('constants.order_creation_error'));
            }

            $order = $ordersManagement->saveOrder($customer->id);

            if (!$order) {
                $this->redirectWithError('forms.shopping', Config::get('constants.order_creation_error'));
            }

            $placeToPlayURL = $this->initPayment($order->id, $webCheckout, $requestsManagement);

            // if the request data is stored, redirect the user to place to play, if not redirect to the first form
            if ($placeToPlayURL) {
                return redirect($placeToPlayURL);
            }

            $this->redirectWithError('forms.shopping', Config::get('constants.order_creation_error'));

        } catch (\Exception $e) {
            error_log($e->getMessage());
            return redirect(route('forms.shopping'));
        }
    }

    /**
     * Shows the order information
     * @param $id
     * @param OrdersManagement $ordersManagement
     * @param RequestsManagement $requestsManagement
     * @param WebCheckout $webCheckout
     * @return Application|\Illuminate\Contracts\View\Factory
     * |\Illuminate\Contracts\View\View|RedirectResponse|Redirector
     */
    public function show(
        $id,
        OrdersManagement $ordersManagement,
        RequestsManagement $requestsManagement,
        WebCheckout $webCheckout
    )
    {
        $order = $ordersManagement->getOrder($id);

        if (!$order) {
            return $this->redirectWithError('forms.shopping', Config::get('constants.order_not_found'));
        }

        $showPaymentButton = false;
        $showNewPaymentButton = false;

        try {

            // if the state is equals to created, request the place to play endpoint to retrieve the new state
            // and update the request and the order.
            $originalOrderStatus = $order->getRawOriginal('status');
            if ($originalOrderStatus === Order::CREATED_STATE || $originalOrderStatus === Order::REJECTED_STATE) {

                $auth = ['auth' => $webCheckout->getAuthData(new \DateTime())];

                $sessionResponse = $webCheckout->makeRequest(
                    Config::get('constants.ptp_session_endpoint') . '/' . $order->request->request_id,
                    json_encode($auth, JSON_THROW_ON_ERROR)
                );

                $newStatus = $webCheckout->parseSessionStatusResponse($sessionResponse);

                if ($newStatus) {
                    // update the request status
                    $requestsManagement->updateRequestStatus($order->request->id, $newStatus);
                    // get the new order status based on the request
                    $orderStatus = $ordersManagement->getOrderStatus($newStatus);
                    // update the order status
                    $ordersManagement->updateOrdertStatus($order->id, $orderStatus);

                    // shows the payment button if the order state is equals to CREATED
                    $showPaymentButton = $ordersManagement->showPaymentButton($orderStatus);

                    // shows the new payment button if the order state is equals to CREATED
                    $showNewPaymentButton = $ordersManagement->showNewPaymentButton($orderStatus);
                }
            } else {
                // shows the button to start a new payment if the order state is equals to CREATED
                $showNewPaymentButton = $ordersManagement->showNewPaymentButton($originalOrderStatus);
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

        $order = $ordersManagement->getOrder($id);

        return view('orders.show', [
            'order' => $order,
            'showPaymentButton' => $showPaymentButton,
            'showNewPaymentButton' => $showNewPaymentButton
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @param OrdersManagement $ordersManagement
     * @param RequestsManagement $requestsManagement
     * @param WebCheckout $webCheckout
     * @return Application|Redirector|RedirectResponse
     */
    public function newPayment(
        $id,
        OrdersManagement $ordersManagement,
        RequestsManagement $requestsManagement,
        WebCheckout $webCheckout
    )
    {
        $order = $ordersManagement->getOrder($id);

        if (!$order) {
            return $this->redirectWithError('forms.shopping', Config::get('constants.order_not_found'));
        }

        $placeToPlayURL = $this->initPayment($order->id, $webCheckout, $requestsManagement);

        // if the request data is stored, redirect the user to place to play, if not redirect to the first form
        if ($placeToPlayURL) {
            return redirect($placeToPlayURL);
        }
        $this->redirectWithError('forms.shopping', Config::get('constants.order_creation_error'));
    }

    /**
     * Init the payment process
     * @param $orderId
     * @param WebCheckout $webCheckout
     * @param RequestsManagement $requestsManagement
     * @return string|null
     */
    private function initPayment($orderId, WebCheckout $webCheckout, RequestsManagement $requestsManagement)
    {
        try {
            // get the fields for the session endpoint
            $fields = $webCheckout->prepareSessionRequest($orderId);
            // make the request to the session endpoint
            $sessionResponse = $webCheckout->makeRequest(Config::get('constants.ptp_session_endpoint'), $fields);
            // get the response data from the request
            $responseData = $webCheckout->parseSessionResponse($sessionResponse);

            if (!$responseData['requestId'] || !$responseData['processUrl']) {
                $this->redirectWithError('forms.shopping', Config::get('constants.order_creation_error'));
            }

            $responseData['orderId'] = $orderId;

            // if the request data is stored, redirect the user to place to play, if not redirect to the first form
            if ($requestsManagement->saveRequest($responseData)) {
                return $responseData['processUrl'];
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

        return null;
    }

    /**
     * Redirects to the passed route, with the passed error message
     * @param $route
     * @param $error
     * @return Application|RedirectResponse|Redirector
     */
    private function redirectWithError($route, $error)
    {
        $errors = new MessageBag;
        $errors->add('custom-error', $error);
        return redirect(route($route))->withErrors($errors);
    }

}
