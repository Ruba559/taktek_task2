<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\ListAuthors;
use App\Livewire\Counter;
Route::get('/counter', Counter::class);
Route::group([ 'prefix'=>'dashboard'],  function () {

    Route::get('/authors',ListAuthors::class);
});

Route::get('/', function () {
    return view('welcome');
});


