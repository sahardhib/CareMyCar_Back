<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\VoitureController;
use App\Http\Controllers\API\AdminController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Route::get('users','App\Http\Controllers\APIcontroller@getUsers');  // get users data from database
Route::namespace ('App\Http\Controllers\API')->group(function(){
   Route::post('register', 'APIController@create'); 

   Route ::post('login', 'APIController@authenticate');  

   Route :: post('update-user' , 'APIController@updateUser');
   Route::post('upload-photo', 'APIController@uploadPhoto');
   Route::post('/reset-password', [APIController::class, 'sendResetEmail']); // Add this route for password reset
   Route::get('/get-users','APIController@getUsers');
   Route::delete('delete-user/{id}', 'APIController@deleteUser');

   

Route::post('admin-register', [AdminController::class, 'create']);
Route::post('admin-login', [AdminController::class, 'authenticate']);
Route::post('admin-update-user', [AdminController::class, 'updateUser']);
Route::post('admin-reset-password', [AdminController::class, 'sendResetEmail']);


});     

Route::get('voitures', [VoitureController::class, 'index']);
Route::get('voitures/{id}', [VoitureController::class, 'show']);
Route::post('voitures', [VoitureController::class, 'store']);
Route::put('voituresupdate/{id}', [VoitureController::class, 'update']);
Route::delete('voituresdelete/{id}', [VoitureController::class, 'destroy']);



