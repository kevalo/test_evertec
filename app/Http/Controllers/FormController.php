<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FormController extends Controller
{
    public function shopping()
    {
        return view('forms.shopping');
    }

    public function preview(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:80',
            'email' => 'required|max:120|email',
            'mobile' => 'required|max_digits:40|numeric',
        ]);

        return view('forms.preview', ['data' => $validated]);
    }
}
