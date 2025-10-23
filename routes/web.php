<?php

use App\Http\Controllers\ProcessFileController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ConverterController;
use App\Http\Controllers\MergerController;
use App\Http\Controllers\SplitterController;
use App\Http\Controllers\AnalyzerController;
use App\Http\Controllers\DownloadController;
use Illuminate\Support\Facades\Route;


//main view
Route::view('/', 'index')->name('home');

//download route
Route::post('/download/{id}', [DownloadController::class, 'download'])->name('download');

//view dashboard route
Route::get('/dashboard', [FileController::class, 'index'])->name('dashboard');

//route for store, show, delete file
Route::resource('files', FileController::class);

//routes for conversion files
Route::post('/file/{id}/xlsxToXls', [ProcessFileController::class, 'xlsxToXls_v1'])->name('xlsxToXls');

Route::post('/file/{id}/xlsToXlsx', [ProcessFileController::class, 'xlsToXlsx_v1'])->name('xlsToXlsx');

Route::post('/file/{id}/excelToOds', [ProcessFileController::class, 'excelToOds_v1'])->name('excelToOds');

Route::post('/file/{id}/excelToCsv', [ProcessFileController::class, 'excelToCsv_v1'])->name('excelToCsv');

Route::post('/file/{id}/excelToHtml', [ProcessFileController::class, 'ExcelToHtml_v1'])->name('excelToHtml');

Route::post('/file/{id}/split', [ProcessFileController::class, 'split'])->name('split');

//route to check conversion status in 5 sec
Route::get('/convert/check/{id}', [DownloadController::class, 'checkStatus'])->name('convert.check');

//route to download file in browser
Route::get('/download/converted_file/{id}', [DownloadController::class, 'downloadFile'])->name('download.file');
