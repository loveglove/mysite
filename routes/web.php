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

app('debugbar')->disable();

// Main Routes
Route::get('/', function() { return view('landing'); });
Route::get('/home', 'HomeController@index')->name('home');

// Project Routes
Route::get('/projects/led-wall', function() { return view('projects.ledwall'); });
Route::get('/projects/fisher-classic', function() { return view('projects.fisherclassic'); });
Route::get('/projects/music', function() { return view('projects.music'); });

// Auth Routes
Auth::routes();
Route::get('auth/{provider}', 'Auth\RegisterController@redirectToProvider');
Route::get('auth/{provider}/callback', 'Auth\RegisterController@handleProviderCallback');

// Test Routes
Route::get('/welcome', function() { return view('welcome'); });
Route::get('/blocks', function() { return view('sandbox.blocks'); });
Route::get('/comingsoon', function() { return view('sandbox.comingsoon'); });

// Pinning Routes
Route::get('/bean', function() { return view('SM/georgia'); });
Route::get('/byejordan', function() { return view('SM/jordan'); });

// NXM Routes
Route::get('/nxm', function() { return view('nxm.nxm'); });
Route::get('/map.html', function() { return view('nxm.nxm'); });
Route::get('/trip.html', function() { return view('nxm.trip'); });






// Mail Routes
Route::post('/contact', 'PublicController@contact');
