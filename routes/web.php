<?php

use App\Http\Controllers\FileController;
use App\Http\Controllers\DownloadController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProcessFileController;

// Основные страницы
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/converter', function () {
    return view('converter');
})->name('converter')->middleware('auth');

Route::get('/merger', function () {
    return view('merger');
})->name('merger')->middleware('auth');

Route::get('/splitter', function () {
    return view('splitter');
})->name('splitter')->middleware('auth');

Route::get('/analyzer', function () {
    return view('analyzer');
})->name('analyzer')->middleware('auth');

// Dashboard route 
Route::get('/dashboard', [FileController::class, 'index'])->name('dashboard')->middleware('auth');

// File management 
Route::resource('files', FileController::class)->middleware('auth');

// Special route for uploading files during merge process
Route::post('/files/upload-merge', [FileController::class, 'uploadForMerge'])->name('files.upload.merge')->middleware('auth');

// Download route 
Route::post('/download/{id}', [DownloadController::class, 'download'])->name('download')->middleware('auth');

// New unified convert route
Route::post('/convert/{format}', [ProcessFileController::class, 'convert'])->name('convert.file')->middleware('auth');

Route::post('/merge-files', [ProcessFileController::class, 'mergeFiles'])->name('merge.files')->middleware('auth');

// Split routes
Route::post('/split-file', [ProcessFileController::class, 'splitFile'])->name('split.file')->middleware('auth');

// Analysis routes
Route::post('/analyze-file', [ProcessFileController::class, 'analyzeFile'])->name('analyze.file')->middleware('auth');

// File processing routes с группировкой как в старом файле (для работы с существующими файлами)
Route::middleware('auth')->controller(ProcessFileController::class)->group(function () {
    //routes for conversion files (работают с существующими файлами)
    Route::post('/file/{id}/xlsxToXls', 'xlsxToXls_v1')->name('xlsxToXls');
    Route::post('/file/{id}/xlsToXlsx', 'xlsToXlsx_v1')->name('xlsToXlsx');
    Route::post('/file/{id}/excelToOds', 'excelToOds_v1')->name('excelToOds');
    Route::post('/file/{id}/excelToCsv', 'excelToCsv_v1')->name('excelToCsv');
    Route::post('/file/{id}/excelToHtml', 'ExcelToHtml_v1')->name('excelToHtml');
    Route::post('/file/{id}/split', 'split')->name('split');
    Route::post('/file/{id}/merge', 'merge')->name('merge');
});

// Download routes 
Route::middleware('auth')->controller(DownloadController::class)->group(function () {
    //route to check conversion status in 5 sec
    Route::get('/convert/check/{id}', 'checkStatus')->name('convert.check');
    //route to download file in browser
    Route::get('/download/converted_file/{id}', 'downloadFile')->name('download.file');
    // Добавляем алиас для старого маршрута, который используется в коде
    Route::get('/download/converted/{id}', [DownloadController::class, 'downloadFile'])->name('download.converted');
    // Route for checking merge status
    Route::get('/check-status/{id}', [DownloadController::class, 'checkStatus'])->name('check.status');
});

// Auth routes
Route::middleware('guest')->controller(AuthController::class)->group(function () {
    Route::get('/register', 'showRegister')->name('show.register');
    Route::get('/login', 'showLogin')->name('show.login');
    Route::post('/register', 'register')->name('register');
    Route::post('/login', 'login')->name('login');
});

// Logout routes 
Route::get('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout.post')->middleware('auth');