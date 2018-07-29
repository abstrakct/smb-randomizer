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

Route::get('/', 'HomeController@index');
// Route::get('/{any}', 'HomeController@index')->where('any', '.*');

Route::get('settings/base_rom', 'SettingsController@hash');
Route::get('randomizer/options', 'RandomizerController@options');
Route::get('randomizer/options/default', 'RandomizerController@defaultoptions');

Route::post('randomizer/generate', 'RandomizerController@generate');
