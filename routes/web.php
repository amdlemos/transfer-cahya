<?php

use App\Livewire\DepositoForm;
use App\Livewire\TransactionTable;
use App\Livewire\UserTable;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home')->middleware('auth');

Route::get('/users', UserTable::class)->name('users')->middleware('auth');

Route::get('/deposit', DepositoForm::class)->name('deposit')->middleware('auth');

Route::get('/transfer', \App\Livewire\TransferForm::class)->name('transfer')->middleware('auth');
