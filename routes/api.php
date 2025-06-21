<?php

use App\Http\Controllers\ListCategoriesController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Hello, World!']);
});

Route::get('/categories', ListCategoriesController::class);