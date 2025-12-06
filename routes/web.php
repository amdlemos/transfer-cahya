<?php

use App\Livewire\UserTable;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/users', UserTable::class)->name('users');
