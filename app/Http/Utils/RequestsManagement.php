<?php

namespace App\Http\Utils;

use App\Models\Request;

class RequestsManagement
{
    /**
     * Saves a new request
     * @param $data array - Array containing the data for the request, orderId, requestId, processUrl
     * @return Request|null
     */
    public function saveRequest($data = [])
    {
        $result = null;
        if (!$data || !is_array($data) || count($data) !== 3) {
            return $result;
        }

        try {
            //checks request existence to update it
            $request = $this->getRequestByOrder($data['orderId']);
            if (!$request) {
                $request = new Request();
                $request->order_id = $data['orderId'];
            }
            $request->request_id = $data['requestId'];
            $request->process_url = $data['processUrl'];
            $request->status = Request::PENDING_STATE;

            if ($request->save()) {
                $result = $request;
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
        return $result;
    }

    /**
     * Returns the request of the order passed
     * @param $orderId
     * @return null
     */
    public function getRequestByOrder($orderId)
    {
        $request = null;
        if (!is_numeric($orderId)) {
            return $request;
        }

        try {
            $request = Request::where('order_id', $orderId)->first();
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
        return $request;
    }

    /**
     * Updates the request status
     * @param $id
     * @param $status
     * @return bool
     */
    public function updateRequestStatus($id, $status)
    {
        $result = false;
        try {
            $request = Request::find($id);
            if (!$request) {
                return $result;
            }

            $request->status = $status;
            $result = $request->save();
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
        return $result;
    }
}
