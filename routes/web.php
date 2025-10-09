<?php

use App\Http\Controllers\ProcessFileController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ConverterController;
use App\Http\Controllers\MergerController;
use App\Http\Controllers\SplitterController;
use App\Http\Controllers\AnalyzerController;
use App\Http\Controllers\DownloadController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'index')->name('home');

Route::view('/info', 'app')->name('info');

Route::post('/download/{id}', [DownloadController::class, 'download'])->name('download');

Route::get('/dashboard', [FileController::class, 'index'])->name('dashboard');

Route::resource('files', FileController::class);

Route::post('/process', [ProcessFileController::class, 'viewFile'])->name('process.form');

Route::post('/file/{id}/xlsxToXls', [ProcessFileController::class, 'xlsxToXls_v1'])->name('xlsxToXls');

Route::post('/file/{id}/xlsToXlsx', [ProcessFileController::class, 'xlsToXlsx'])->name('xlsToXlsx');

Route::post('/file/{id}/excelToOds', [ProcessFileController::class, 'excelToOds'])->name('excelToOds');

Route::post('/file/{id}/excelToCsv', [ProcessFileController::class, 'excelToCsv'])->name('excelToCsv');

Route::post('/file/{id}/excelToHtml', [ProcessFileController::class, 'convertExcelToHtmlViaSpout'])->name('excelToHtml');

// Маршруты для функциональности Table Master
// Route::get('/converter', [ConverterController::class, 'index'])->name('converter');
// Route::post('/converter/process', [ConverterController::class, 'process'])->name('converter.process');

// Route::get('/merger', [MergerController::class, 'index'])->name('merger');
// Route::post('/merger/process', [MergerController::class, 'process'])->name('merger.process');

// Route::get('/splitter', [SplitterController::class, 'index'])->name('splitter');
// Route::post('/splitter/process', [SplitterController::class, 'process'])->name('splitter.process');

Route::get('/analyzer', [AnalyzerController::class, 'index'])->name('analyzer');
Route::post('/analyzer/process', [AnalyzerController::class, 'process'])->name('analyzer.process');
