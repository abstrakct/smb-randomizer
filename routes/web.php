<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| "web" middleware group
|
 */

Route::get('/', 'HomeController@index');
Route::get('/about', 'HomeController@about');
// Route::get('/{any}', 'HomeController@index')->where('any', '.*');

Route::get('settings/base_rom', 'SettingsController@hash');
Route::get('randomizer/options', 'RandomizerController@options');
Route::get('randomizer/options/default', 'RandomizerController@defaultoptions');

Route::post('randomizer/generate', 'RandomizerController@generate');
//Route::post('randomizer/generate/mystery', 'RandomizerController@generateMysterySeed');

Route::post('randomizer/flags/get', 'RandomizerController@getflags');
Route::post('randomizer/flags/set', 'RandomizerController@setOptionsFromFlagstring');
