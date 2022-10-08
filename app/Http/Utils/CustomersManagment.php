<?php

namespace App\Http\Utils;

use App\Models\Customer;

class CustomersManagment
{
    /**
     * Saves a new customer
     * @param $data array - Array with the customer data, name, email and mobile
     * @return Customer|null
     */
    public function saveCustomer($data = [])
    {
        $result = null;
        if (!$data || count($data) !== 3) {
            return $result;
        }

        try {
            $customer = new Customer();
            $customer->name = $data['name'];
            $customer->email = $data['email'];
            $customer->mobile = $data['mobile'];

            if ($customer->save()) {
                $result = $customer;
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
        return $result;
    }
}
