<?php

namespace App\Http\Utils;

use App\Models\Customer;

class CustomersManagement
{
    /**
     * Saves a new customer
     * @param $data array - Array with the customer data, name, email and mobile
     * @return Customer|null
     */
    public function saveCustomer($data = [])
    {
        $result = null;
        if (!$data || !is_array($data) || count($data) !== 3) {
            return $result;
        }

        try {
            //checks customer existence to update it
            $customer = $this->getCustomerByEmail($data['email']);
            if (!$customer) {
                $customer = new Customer();
                $customer->email = $data['email'];
            }
            $customer->name = $data['name'];
            $customer->mobile = $data['mobile'];

            if ($customer->save()) {
                $result = $customer;
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
        return $result;
    }

    /**
     * Finds and returns the customer by its email
     * @param $email
     * @return null
     */
    public function getCustomerByEmail($email)
    {
        $customer = null;
        if (!$email) {
            return $customer;
        }

        try {
            $customer = Customer::where('email', $email)->first();
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
        return $customer;
    }
}
