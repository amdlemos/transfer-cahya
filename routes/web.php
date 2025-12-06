<?php

use App\Livewire\DepositoForm;
use App\Livewire\UserTable;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->middleware('auth');

Route::get('/users', UserTable::class)->name('users')->middleware('auth');

Route::get('/profile/deposit', DepositoForm::class)->name('users')->middleware('auth');
