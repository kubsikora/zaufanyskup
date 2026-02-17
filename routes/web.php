<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::fallback(function () {
    $path = public_path('spa/index.html');
    
    if (!file_exists($path)) {
        return "Błąd: Plik Quasara nie został jeszcze zbudowany w public/spa!";
    }

    return file_get_contents($path);
});
