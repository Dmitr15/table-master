<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MergerController extends Controller
{
    public function index()
    {
        return view('merger');
    }

    public function process(Request $request)
    {
        // Логика слияния таблиц
    }
}