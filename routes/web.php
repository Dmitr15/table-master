<?php

use App\Http\Controllers\FileController;
use App\Http\Controllers\DownloadController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProcessFileController;


Route::view('/', 'index')->name('index');

//download route
Route::post('/download/{id}', [DownloadController::class, 'download'])->name('download')->middleware('auth');

//view dashboard route
Route::get('/dashboard', [FileController::class, 'index'])->name('dashboard')->middleware('auth');

//route for store, show, delete file
Route::resource('files', FileController::class)->middleware('auth');


Route::middleware('auth')->controller(ProcessFileController::class)->group(function () {
    //routes for conversion files
    Route::post('/file/{id}/xlsxToXls', 'xlsxToXls_v1')->name('xlsxToXls');

    Route::post('/file/{id}/xlsToXlsx', 'xlsToXlsx_v1')->name('xlsToXlsx');

    Route::post('/file/{id}/excelToOds', 'excelToOds_v1')->name('excelToOds');

    Route::post('/file/{id}/excelToCsv', 'excelToCsv_v1')->name('excelToCsv');

    Route::post('/file/{id}/excelToHtml', 'ExcelToHtml_v1')->name('excelToHtml');

    Route::post('/file/{id}/split', 'split')->name('split');

    Route::post('/file/{id}/merge', 'merge')->name('merge');
});


Route::middleware('auth')->controller(DownloadController::class)->group(function () {
    //route to check conversion status in 5 sec
    Route::get('/convert/check/{id}', 'checkStatus')->name('convert.check')->middleware('auth');

    //route to download file in browser
    Route::get('/download/converted_file/{id}', 'downloadFile')->name('download.file')->middleware('auth');
});


//auth routes
Route::middleware('guest')->controller(AuthController::class)->group(function () {
    Route::get('/register', 'showRegister')->name('show.register');
    Route::get('/login', 'showLogin')->name('show.login');

    Route::post('/register', 'register')->name('register');
    Route::post('/login', 'login')->name('login');
});


//logout routes
Route::get('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');