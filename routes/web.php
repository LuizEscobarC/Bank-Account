<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => "Bem Vindo ao Bank Account Sem Autenticação, Usuário e nem front hehe"];
});
