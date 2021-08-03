<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserController;



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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);    
});

Route::group(['middleware' => ['jwt.auth']

], function() {
    Route::apiResource('tasks', TaskController::class);

    Route::put('/tasks/{task}/status', [TaskController::class, 'changeTaskStatus']);

    Route::post('/tasks/messages/add', [TaskController::class, 'addMessage']);
    Route::put('/tasks/messages/{message}', [MessageController::class, 'update']);
    Route::delete('/tasks/messages/{message}', [MessageController::class, 'destroy']);
    Route::get('/tasks/{task}/messages', [TaskController::class, 'getMessagesOfTask']);
    Route::get('/tasks/{task}/messages/{message}', [TaskController::class, 'getMessage']);

});



//sanity check
Route::get('/hello', function(){
    return ['message' => 'hello from task-msg-api'];
});


