<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SplitterController extends Controller
{
    public function index()
    {
        return view('splitter');
    }

    public function process(Request $request)
    {
        // Логика разделения таблиц
    }
}