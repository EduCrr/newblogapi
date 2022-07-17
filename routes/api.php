<?php

use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/categories', [CategoriesController::class, 'index']);
Route::get('/categorie/{id}', [CategoriesController::class, 'findOne']);
Route::delete('/categorie/{id}', [CategoriesController::class, 'delete']);
Route::post('/categorie', [CategoriesController::class, 'create']);

Route::get('/posts', [PostsController::class, 'index']);
Route::get('/post/{id}', [PostsController::class, 'findOne']);
Route::delete('/post/{id}', [PostsController::class, 'delete']);
Route::post('/post', [PostsController::class, 'create']);
Route::put('/post/{id}', [PostController::class, 'update']); 

Route::post('post/images/{id}', [PostController::class, 'updateImages']); 
Route::delete('post/images/{id}', [PostController::class, 'deleteImage']); 