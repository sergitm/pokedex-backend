<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PokemonController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/pokemon/page/{page}', 'App\Http\Controllers\PokemonController@get20Pokemon');
Route::get('/pokemon/types', 'App\Http\Controllers\PokemonController@types');
Route::get('/pokemon/{id}', 'App\Http\Controllers\PokemonController@getPokemon');
Route::get('/pokemon/type/{type1}/{type2}/page/{pageQuery}', 'App\Http\Controllers\PokemonController@getPokemonByType');