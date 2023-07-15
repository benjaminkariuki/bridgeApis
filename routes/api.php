<?php

use App\Http\Controllers\ActivitiesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\dummyApi;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\ForgotPassword;
use App\Http\Controllers\ManageUser;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UserController;

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





Route::post('register', [RegisterController::class, 'register']);

Route::post('login', [RegisterController::class, 'login']);

//send forgot password reset link

Route::post('pass_reset', [ForgotPassword::class, 'sendResetLink']);


Route::post('reset_password', [ForgotPassword::class, 'resetPassword']);

 Route::get('users/{id}', [ManageUser::class, 'getDetails']);

 Route::post('updateUsers/{id}', [ManageUser::class, 'updateUserDetails']);

 Route::post('changepassword/{id}', [ForgotPassword::class, 'changePassword']);

 Route::get('activitiesAll', [ActivitiesController::class, 'getActivities']);

 Route::post('create_role', [RolesController::class, 'createRoles']);

 Route::get('allRoles', [RolesController::class,'getRoles']);

 Route::get('allRolesWithId/{id}', [RolesController::class, 'getRolesWithId']);  

 Route::delete('deleteRoles/{id}', [RolesController::class, 'deleteRole']);  

 Route::put('updateRoles/{id}', [RolesController::class, 'updateRoles']);

 Route::get('allUsers', [UserController::class,'getAllUsers']);

 Route::get('searchUsers', [UserController::class,'search']);

 Route::delete('deleteUsers/{id}', [RegisterController::class, 'deleteUser']);  

 Route::put('updateUsers/{id}', [RegisterController::class, 'updateUser']);
 
 Route::delete('deleteImage/{id}', [ManageUser::class, 'deleteUserImage']);  

 Route::post('create_projects', [ProjectsController::class, 'createProjects']);