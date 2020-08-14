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
Route::get('/music', function() { return view('music'); });
Route::get('/privacy', function() { return view('privacy'); });
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/apps/loftbot', 'LoftbotController@index')->name('loftbot');
Route::get('/apps/flybits', 'FlybitsController@index')->name('flybits');

Route::get('/loftbot', 'LoftbotController@index')->name('loftbot');

Route::get('/mail', 'PublicController@mail')->name('mail');

// Project Routes
Route::get('/projects/led-wall', function() { return view('projects.ledwall'); });
Route::get('/projects/fisher-classic', function() { return view('projects.fisherclassic'); });


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



// TWILIO
Route::get('/apps/operator/test', 'TwilController@twilTest');
Route::get('/apps/operator/voice/inbound', 'TwilController@voiceInbound');
Route::any('/apps/operator/messaging/inbound', 'TwilController@messageInbound');
Route::get('/apps/operator/password/process', 'TwilController@processPassword');
Route::post('/apps/operator/save', 'HomeController@saveSettings');

// Mail Routes
Route::post('/contact', 'PublicController@contact');

// Flybits
Route::any('/apps/flybits/sms', 'FlybitsController@flybitsSMS');
Route::any('/apps/flybits/email', 'FlybitsController@flybitsEmail');
Route::any('/apps/flybits/email2', 'FlybitsController@flybitsEmail2');
Route::any('/apps/flybits/emailcsv', 'FlybitsController@flybitsEmailCSV');
Route::get('/apps/flybits/api', 'FlybitsController@flybitsAPI');


Route::get('/apps/email/test', 'MailController@sendGridTest');

// Quest
Route::post('/apps/quest/sms', 'QuestController@sendSMS');
Route::get('/quest', 'QuestController@admin')->middleware('auth');
Route::get('/quest/compass', 'QuestController@compass');