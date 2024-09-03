<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UpdateProfileInformation;

/*
|--------------------------------------------------------------------------
| API Routesb
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    $user = $request->user();
    $role = $user->role;
    
    return response()->json([
        'userInfo' => [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $role->name
        ] 
    ]);
});


Route::POST('/login', [AuthenticationController::class, 'login'])->name('login');
Route::POST('/register', [AuthenticationController::class, 'register'])->name('register');

Route::middleware(['auth:api'])->group(function(){

    Route::get('/user-role', function (Request $request){
        return response()->json([
            'id' => $request->user()->id,
            'role' => $request->user()->role->name
        ]);
    });

    Route::POST('/logout', [AuthenticationController::class, 'logout']);

    Route::apiResource('tasks', TaskController::class);
    Route::put('/task-update/{id}', [TaskController::class, 'updateStatus']);
    Route::get('/user-task', [TaskController::class, 'userTasks']);
    Route::get('/status', [TaskController::class, 'status']);

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users-details', [UserController::class, 'homeInformations']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::delete('/users/{id}', [UserController::class, 'delete']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'delete']);

    Route::post('/update-information', [UpdateProfileInformation::class, 'update']);
    Route::post('/update-password', [UpdateProfileInformation::class, 'updateUserPassword']);

});
