<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// Routes for OAuth authentication via various providers
Route::get('auth/{providerName}', 'Auth\LoginController@redirectToProvider');
Route::get('auth/{providerName}/callback', 'Auth\LoginController@handleProviderCallback');
