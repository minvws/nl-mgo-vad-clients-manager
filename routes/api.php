<?php

declare(strict_types=1);

use App\Http\Controllers\Api\ClientController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(static function (): void {
    Route::get('/clients', [ClientController::class, 'index'])
        ->name('clients.index');
});
