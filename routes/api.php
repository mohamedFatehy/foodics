<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;


Route::resource('orders', OrderController::class)->only(['store']);
