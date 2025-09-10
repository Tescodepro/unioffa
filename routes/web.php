<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\website\GeneralController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Here is where you can register web routes for your application. These
| routes are loaded by the          
RouteServiceProvider within a group which contains the "web" middleware group. Now create something great!
|           

*/  
Route::controller(GeneralController::class)->group(function(){
    Route::get('/', 'home')->name('home');
    Route::get('/contact', 'contact')->name('contact');
});


