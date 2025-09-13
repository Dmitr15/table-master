<?php

use App\Http\Controllers\ProcessFileController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ConverterController;
use App\Http\Controllers\MergerController;
use App\Http\Controllers\SplitterController;
use App\Http\Controllers\AnalyzerController;
use Illuminate\Support\Facades\Route;


Route::view('/', 'index')->name('index');

Route::get('/dashboard', [FileController::class, 'index'])->name('dashboard');

Route::resource('files', FileController::class);

Route::post('/process', [ProcessFileController::class, 'viewFile'])->name('process.form');

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Маршруты для функциональности Table Master
Route::get('/converter', [ConverterController::class, 'index'])->name('converter');
Route::post('/converter/process', [ConverterController::class, 'process'])->name('converter.process');

Route::get('/merger', [MergerController::class, 'index'])->name('merger');
Route::post('/merger/process', [MergerController::class, 'process'])->name('merger.process');

Route::get('/splitter', [SplitterController::class, 'index'])->name('splitter');
Route::post('/splitter/process', [SplitterController::class, 'process'])->name('splitter.process');

Route::get('/analyzer', [AnalyzerController::class, 'index'])->name('analyzer');
Route::post('/analyzer/process', [AnalyzerController::class, 'process'])->name('analyzer.process');

// Для Single Page Application (если используете React как SPA)
Route::get('/{any}', function () {
    return view('app'); // Базовый шаблон, где монтируется React
})->where('any', '.*');