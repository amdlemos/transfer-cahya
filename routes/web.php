<?php

use App\Livewire\DepositoForm;
use App\Livewire\UserTable;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home')->middleware('auth');

Route::get('/users', UserTable::class)->name('users')->middleware('auth');

Route::get('/deposit', DepositoForm::class)->name('deposit')->middleware('auth');
