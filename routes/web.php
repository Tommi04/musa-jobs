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

Auth::routes([
    //modifichiamo le rotte, ho eliminato la registrazione e la verifica
    'register'  => false,
    'reset'     => true,
    'verify'    => false
]);

Route::get('/home', 'HomeController@index')->name('home');

Route::prefix('admin')
    ->namespace('Admin')
    ->name('admin.')
    ->middleware('can:can-admin') //così scatta in automatico il Gate
    ->group(function(){
        Route::resource('user', 'UsersController')->except(['create', 'store']);
    });

Route::prefix('admin')
    ->namespace('Admin')
    ->name('admin.')
    ->middleware('can:can-admin') //così scatta in automatico il Gate
    ->group(function(){
        Route::resource('companies', 'CompaniesController')->except(['create', 'store']);
    });

Route::prefix('admin')
    ->namespace('Admin')
    ->name('admin.')
    ->middleware('can:can-admin') //così scatta in automatico il Gate
    ->group(function(){
        Route::resource('categories', 'CategoriesController');
    });

Route::prefix('admin')
    ->namespace('Admin')
    ->name('admin.')
    ->middleware('can:can-admin') //così scatta in automatico il Gate
    ->group(function(){
        Route::resource('skills', 'SkillsController');
    });

Route::prefix('admin')
    ->namespace('Admin')
    ->name('admin.')
    ->middleware('can:can-admin') //così scatta in automatico il Gate
    ->group(function(){
        /*
        Route::resource('job-offers', 'JobOffersController')->except([
            'create', 'store'
        ]);
        */
        Route::resource('job-offers', 'JobOffersController');
    });

