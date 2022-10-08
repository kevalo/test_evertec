<?php

namespace App\Http\Controllers;

use App\Http\Utils\CustomersManagment;
use App\Http\Utils\OrdersManagment;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use App\Http\Utils\WebCheckout;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @param CustomersManagment $customersManagment
     * @param OrdersManagment $ordersManagment
     * @param WebCheckout $webCheckout
     * @return Application|RedirectResponse|Redirector
     */
    public function store(Request $request, CustomersManagment $customersManagment, OrdersManagment $ordersManagment, WebCheckout $webCheckout)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:80',
            'email' => 'required|max:120|email',
            'mobile' => 'required|max_digits:40|numeric',
        ]);

        if ($validator->fails()) {
            return redirect(route('forms.shopping'))
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $customer = $customersManagment->saveCustomer($validator->validated());

            if (!$customer) {
                $this->redirectWithError('forms.shopping', Config::get('constants.order_creation_error'));
            }

            $order = $ordersManagment->saveOrder($customer->id);
            if (!$order) {
                $this->redirectWithError('forms.shopping', Config::get('constants.order_creation_error'));
            }

            $fields = $webCheckout->prepareSessionRequest();
            $sessionResponse = $webCheckout->makeRequest('/api/session', $fields);
            //TODO: validate $sessionResponse

        } catch (\Exception $e) {
            error_log($e->getMessage());
            return redirect(route('forms.shopping'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Redirects to the passed route, with the error message
     * @param $route
     * @param $error
     * @return Application|RedirectResponse|Redirector
     */
    private function redirectWithError($route, $error)
    {
        $errors = new \Illuminate\Support\MessageBag;
        $errors->add('custom-error', $error);
        return redirect(route($route))->withErrors($errors);
    }

}
