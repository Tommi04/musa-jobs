<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/*il middleware certifica che quella richiesta sia
    effettivamente una richiesta api e gestisce le richieste per minuto ed il
    trotting delle richieste */
/* NON CI SERVE
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

//qua serve auth:api perchè ci serve anche l'autenticazione dell'utente loggato
Route::group(['middleware' => 'api'], function(){
    Route::get('users', 'UsersController@index');
    Route::get('users/{user}', 'UsersController@show');
    
    //se uso delete la richiesta di cancellazione sarà auto cancellante
    // Route::delete('delete-users/{user}', 'UsersController@destroy');
    Route::get('delete-user/{user}', 'UsersController@destroy');
    Route::get('restore-user/{user}', 'UsersController@restore');
    
    Route::get('companies', 'CompaniesController@index');
    Route::get('companies/{company}', 'CompaniesController@show');

    Route::post('job-offers', 'JobOffersController@store');
    
    // Route::get('users-by-skill/{skill}', 'UsersController@userSBySkill');
    //trasformiamola in post
    Route::post('users-by-skill', 'UsersController@usersBySkill');


    // Route::get('job-offers-by-skill/{skill}', 'JobOffersController@jobOffersBySkill');
    //trasformiamola in post
    Route::post('job-offers-by-skill', 'JobOffersController@jobOffersBySkill');

    //da implementare
    // Route::post('company-categories', 'CompaniesController@allCategories');
    
    Route::get('my-profile', 'AuthController@myProfile');
    
    Route::post('logout', 'AuthController@logout');

    Route::post('user-skill-add', 'UsersController@addSkill');
    Route::post('user-skill-remove', 'UsersController@removeSkill');
    
});


//auth:api significa che ha il doppio middleware per le richieste api e per il token di autenticazione, il login
//api ha solo il middleware delle api
Route::group(['middleware' => ['api']], function(){
    Route::post('register-user', 'AuthController@registerUser' );
    Route::post('user-login', 'AuthController@userLogin');
    Route::get('company-categories', 'CompaniesController@allCategories');
});

// Quando creiamo la rotta, laravel ci aggiunge un pezzo all'url dell'api
// dovremo richiamarla non con per esempio /users ses abbiamo dato users alla rotta dell'api
// ma dovremo richiamarla con /api/users