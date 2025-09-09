<?php

use Illuminate\Support\Facades\Route;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

Route::get('/', function () {
    return view('welcome');
});




