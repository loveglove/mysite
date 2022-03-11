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

// Main View Routes
Route::get('/', function() { return view('landing'); });
Route::get('/music', function() { return view('music'); });
Route::get('/privacy', function() { return view('privacy'); });
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/apps/flybits/rules', 'FlybitsController@showRules')->name('rules');
Route::get('/apps/flybits/templates', 'FlybitsController@showTemplates')->name('templates');
Route::get('/apps/loftbot', 'LoftbotController@index')->middleware('auth');

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
Route::post('/apps/operator/save', 'LoftbotController@saveSettings');

// Mail Routes
Route::post('/contact', 'PublicController@contact');

// Flybits Webhooks
Route::any('/apps/flybits/sms', 'FlybitsController@flybitsSMS');
Route::any('/apps/flybits/email', 'FlybitsController@flybitsEmail');
Route::any('/apps/flybits/email2', 'FlybitsController@flybitsEmail2');
Route::any('/apps/flybits/emailcsv', 'FlybitsController@flybitsEmailCSV');
Route::any('/apps/flybits/oracle', 'FlybitsController@oracle');
// Flybits API 
Route::post('/apps/flybits/api/engagement', 'FlybitsController@flybitsCreateEngagement');
Route::get('/apps/flybits/api/getcontent', 'FlybitsController@flybitsGetContentAPI');
Route::get('/apps/flybits/api/setProject', 'FlybitsController@flybitsSetProject');
Route::get('/apps/flybits/api/setProjectJWT', 'FlybitsController@flybitsSetProjectJWT');
Route::post('/apps/flybits/api/templates/get', 'FlybitsController@flybitsGetTemplateAPI');
Route::post('/apps/flybits/api/templates/update', 'FlybitsController@flybitsUpdateTemplateAPI');
Route::post('/apps/flybits/api/templates/create', 'FlybitsController@flybitsCreateTemplateAPI');

Route::get('/apps/email/test', 'MailController@sendGridTest');

// Quest
Route::post('/apps/quest/sms', 'QuestController@sendSMS');
Route::get('/quest', 'QuestController@admin')->middleware('auth');
Route::get('/quest/compass', 'QuestController@compass');