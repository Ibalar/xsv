<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CatalogController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/katalog/{path}', [CatalogController::class, 'show'])
    ->where('path', '.*')
    ->name('catalog.show');
