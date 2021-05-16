<?php

use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfilesController;
use App\Http\Controllers\RepliesController;
use App\Http\Controllers\ThreadsController;
use App\Http\Controllers\ThreadSubscriptionsController;
use App\Http\Controllers\UserAvatarController;
use App\Http\Controllers\UserNotificationsController;
use App\Models\ThreadSubscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
Auth::routes();

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', [HomeController::class,'index']);
Route::get('threads/create', [ThreadsController::class,'create']);
Route::get('threads/{channel}/{thread}', [ThreadsController::class,'show']);
Route::delete('threads/{channel}/{thread}', [ThreadsController::class,'destroy']);
Route::post('threads', [ThreadsController::class,'store'])->name('threads');
Route::post('/threads/{channel}/{thread}/replies', [RepliesController::class,'store']);
Route::get('/threads/{channel}/{thread}/replies', [RepliesController::class,'index']);
Route::get('threads/{channel?}', [ThreadsController::class,'index']);
Route::post('/replies/{reply}/favorites',[FavoritesController::class,'store']);
Route::delete('/replies/{reply}/favorites',[FavoritesController::class,'destroy']);
Route::patch('/replies/{reply}',[RepliesController::class,'update']);
Route::delete('/replies/{reply}',[RepliesController::class,'destroy']);
Route::post('threads/{channel}/{thread}/subscriptions',[ThreadSubscriptionsController::class,'store'])->middleware('auth');
Route::delete('threads/{channel}/{thread}/subscriptions',[ThreadSubscriptionsController::class,'destroy'])->middleware('auth');

Route::get('/profiles/{user}/notifications',[UserNotificationsController::class,'index']);
Route::delete('/profiles/{user}/notifications/{notification}',[UserNotificationsController::class,'destroy']);
Route::get('/profiles/{user}',[ProfilesController::class,'show'])->name('profile');
Route::post('/api/users/{user}/avatar',[UserAvatarController::class,'store'])->middleware('auth')->name('avatar');
