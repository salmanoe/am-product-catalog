<?php

use Illuminate\Support\Facades\Route;

Route::apiResource('products', App\Http\Controllers\Api\ProductController::class);
