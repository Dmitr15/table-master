<?php

use App\Http\Controllers\ProcessFileController;
use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;


Route::view('/', 'index')->name('index');

Route::resource('files', FileController::class);

Route::post('/process', [ProcessFileController::class, 'viewFile'])->name('process.form');