<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomer;

class FormController extends Controller
{
    public function shopping()
    {
        return view('forms.shopping');
    }

    public function preview(StoreCustomer $request)
    {
        return view('forms.preview', ['data' => $request->validated()]);
    }
}
