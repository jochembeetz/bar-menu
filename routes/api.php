<?php

declare(strict_types=1);

use App\Http\Controllers\ListCategoriesController;
use App\Http\Controllers\ListCategoryProductsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Hello, World!']);
});

Route::get('/categories', ListCategoriesController::class);
Route::get('/categories/{category}/products', ListCategoryProductsController::class);
