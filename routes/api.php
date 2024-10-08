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
    Route::get('/{id}', [AccountBankController::class, 'show'])->name('account.show');
    Route::put('/account/{id}', [AccountBankController::class, 'update'])->name('account.update');
    Route::delete('/delete/{id}', [AccountBankController::class, 'destroy'])->name('accountBank.destroy');
    Route::get('/', [AccountBankController::class, 'listAccounts'])->name('accountBank.list');
});
