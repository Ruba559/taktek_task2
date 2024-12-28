<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\ListAuthors;


Route::group([ 'prefix'=>'dashboard'],  function () {

    Route::get('/authors',ListAuthors::class);
});


Route::get('/', function () {
    return view('welcome');
});


