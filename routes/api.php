<?php

use App\Http\Controllers\{
    AccountBankController,
    AccountBankTransactionController
};
use Illuminate\Support\Facades\Route;

// Define a rota para criar uma nova conta bancária
Route::group(['as' => 'account-banks.', 'prefix' => 'account-banks'], function () {
    Route::post('/store', [AccountBankController::class, 'store'])->name('create');
    Route::post('/transfer', [AccountBankTransactionController::class, 'transferAmount'])->name("transaction");

    Route::get('/', fn () => response()->json([
        'success' => true
    ]));

    Route::get('/{id}', [AccountBankController::class, 'show'])->name('account.show');
});
