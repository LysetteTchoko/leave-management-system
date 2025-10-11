<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\RetardController;
use App\Http\Controllers\CongeController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\ResearchController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

/*
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/login',[AuthController::class, 'login']);
Route::post('/registerUser',[UserController::class, 'register']);
 
Route::middleware("auth:sanctum")->group(function(){
    Route::post('/createEmploye',[EmployerController::class, 'createEmploye']);
    Route::delete('/deleteEmploye/{id}',[EmployerController::class, 'deleteEmploye']);
    Route::put('/updateEmploye/{id}',[EmployerController::class, 'updateEmploye']);
     Route::get('/getEmployeId/{id}',[EmployerController::class, 'getEmployeId']);
    Route::Get('/viewEmployer',[EmployerController::class, 'viewEmployer']);

    Route::get('/getUserId/{id}',[UserController::class, 'getUserId']);
    Route::delete('/deleteUser/{id}',[UserController::class, 'deleteUser']);
    Route::put('/updateUser',[UserController::class, 'updateUser']);
    Route::put('/updateUsers/{id}',[UserController::class, 'updateUsers']);
    Route::put('/updateRole/{id}',[UserController::class, 'updateRole']);
    Route::put('/updatePhoto',[UserController::class, 'updatePhoto']);
    Route::get('/viewUser',[UserController::class, 'viewUser']);
    Route::get('/getRoles',[UserController::class, 'getRoles']);
    Route::get('/getType',[UserController::class, 'getType']);
    Route::post('/logout',[UserController::class, 'logout']);
    Route::get('/getUser',[UserController::class, 'getUser']);
    Route::get('/getStatut',[UserController::class, 'getStatutPresence']);

    Route::get('/getRetard',[RetardController::class, 'getRetard']);
    Route::get('/getUserRetard',[RetardController::class, 'getUserRetard']);
    Route::put('/addMotifRetard/{id}',[RetardController::class, 'addMotifRetard']);
    Route::put('/validerRetard/{id}',[RetardController::class, 'validerRetard']);

    Route::get('/getConge',[CongeController::class, 'getConge']);
    Route::post('/addConge',[CongeController::class, 'addConge']);
    Route::get('/getUserConge',[CongeController::class, 'getUserConge']);
    Route::put('/valideConge/{id}',[CongeController::class, 'valideConge']);

    Route::get('/getUserPresence',[PresenceController::class, 'getUserPresence']);
    Route::get('/getAllUserPresence',[PresenceController::class, 'getAllUserPresence']);

    Route::get('/statUser',[ResearchController::class, 'statUser']);
    Route::get('/statGlobal',[ResearchController::class, 'statGlobal']);

});
