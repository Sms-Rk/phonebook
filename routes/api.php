<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;



Route::get('/contacts', [ContactController::class, 'index']);
Route::get('/contacts/show', [ContactController::class, 'show']);
Route::post('/contacts', [ContactController::class, 'store']);
Route::delete('/contacts', [ContactController::class, 'destroy']);
