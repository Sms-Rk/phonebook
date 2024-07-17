<?php

// routes/api.php

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

Route::prefix('contacts')->group(function () {
    Route::get('/', [ContactController::class, 'index']);
    Route::get('/{phone}', [ContactController::class, 'show']);
    Route::post('/', [ContactController::class, 'store']);
    Route::put('/{phone}', [ContactController::class, 'update']);
    Route::delete('/{phone}', [ContactController::class, 'destroy']);
});
