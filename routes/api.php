<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\Authentication\authController;
use App\Http\Controllers\api\admin\adminController;
use App\Http\Controllers\api\livestreamController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\admin\Tags;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
//Authentication Group api/auth/
Route::group(['prefix'=>'auth'],function (){
    Route::post('login',[authController::class, 'userLogin']);
    Route::post('signup',[authController::class, 'userRegistration']);
    Route::post('validate',[authController::class, 'doesuserExists']);
    Route::get('verify',[authController::class, 'verifyUser'])->middleware('auth:sanctum');
});
// Admin/Moderator only Group api/admin/
Route::group(['prefix' => 'admin', 'middleware' => ['auth:sanctum']], function () {
    Route::get('getUsers', [adminController::class, 'getAllUsers']);
    Route::post('create-user', [adminController::class, 'createUser']);
    Route::post('schedule', [livestreamController::class, 'store']);
    Route::get('getStreams',[livestreamController::class, 'index']);
    Route::post('banUser',[UserController::class,'banUser']);
        Route::get('tags', [Tags::class, 'getTags']);
    
        Route::get('tags/search', [Tags::class, 'searchTags']);

        Route::post('tags', [Tags::class, 'addTag']);
        
        Route::post('users/{userId}/tags', [Tags::class, 'addTagsToUser']);
        Route::put('updateUser/{userId}', [adminController::class, 'updateUser']);
        // In routes/api.php
        Route::post('startStream/{id}', [adminController::class, 'startStream']);
        Route::post('endStream/{id}', [adminController::class, 'endStream']);
    });
//General Routes but the user has to be authenticated to use them
Route::get('getNextStream',[livestreamController::class, 'getNextStream']);
Route::group(['prefix' => 'user', 'middleware' => ['auth:sanctum']], function () {
Route::get('get',[UserController::class, 'getUserDetails']);
Route::post('checkpassword',[UserController::class,'checkUserPassword']);
Route::put('update',[UserController::class,'updateUser']);
}); 

