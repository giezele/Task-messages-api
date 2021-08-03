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

Route::group(['middleware' => ['jwt.auth']], function() {
    Route::apiResource('tasks', TaskController::class);
    Route::apiResource('users', UserController::class);//nereikalingas?
    Route::apiResource('messages', MessageController::class);

    Route::put('/tasks/{task}/status', [TaskController::class, 'changeTaskStatus']);


    Route::get('/user-all',[UserController::class, 'usersAll']);
    Route::get('/user-tasks',[UserController::class, 'userOwnTasks']);
    Route::post('/add-task',[TaskController::class, 'addTask']);

    Route::post('/messages/add', [TaskController::class, 'addMessage']);
    Route::post('/add-message', [MessageController::class, 'addMessage']);
    Route::get('/tasks/{task}/messages', [MessageController::class, 'getMessages']);//blogai
    Route::get('/tasks/{task}/messages/{message}', [TaskController::class, 'getMessage']);
    Route::get('/tasks/{task}/get-messages', [TaskController::class, 'getMessagesOfTask']);//gerai

});


//no-auth
// Route::apiResource('tasks', TaskController::class);

//sanity check
Route::get('/hello', function(){
    return ['message' => 'hello from task-msg-api'];
});


