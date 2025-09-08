<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Semua route aplikasi Laravel kamu definisikan di sini.
|
*/

// Root diarahkan langsung ke projectmagang
Route::get('/', function () {
    return view('projectmagang');
});

// Halaman evidence (sama saja dengan root)
Route::get('/projectmagang', function () {
    return view('projectmagang');
});

// Halaman upload (kalau ada logic lain)
Route::get('/upload', function () {
    return view('upload');
});

// Halaman pdf report
Route::get('/pdf-report', function () {
    return view('pdf_report');
});
