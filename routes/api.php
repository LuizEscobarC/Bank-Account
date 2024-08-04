<?php

use App\Http\Controllers\AccountBankController;
use Illuminate\Support\Facades\Route;

// Define a rota para criar uma nova conta bancária
Route::post('/account-banks', [AccountBankController::class, 'store']);
