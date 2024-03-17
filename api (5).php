<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TwilioSMSController;
use App\Http\Controllers\SMSController;



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

Route::post('/add_property', [App\Http\Controllers\UserApiController::class, 'add_property'])->name('add_property');

Route::get('/get_property/{user_id?}', [App\Http\Controllers\UserApiController::class, 'get_property'])->name('get_property');   
Route::get('/get_property_new/{user_id}', [App\Http\Controllers\UserApiController::class, 'get_property_new']); 
Route::get('/all_property', [App\Http\Controllers\UserApiController::class, 'get_property_data'])->name('get_property_data');   
Route::get('/get_propertyV2/{user_id?}', [App\Http\Controllers\UserApiController::class, 'get_propertyV2'])->name('get_propertyV2');   
Route::post('/getPostByUsertoken', [App\Http\Controllers\UserApiController::class, 'getPostByUsertoken']); 
Route::post('/follow', [App\Http\Controllers\UserApiController::class, 'follow']); 


Route::post('/post_likes', [App\Http\Controllers\UserApiController::class, 'post_likes']);
Route::post('/delete_property', [App\Http\Controllers\UserApiController::class, 'delete_property']);
Route::post('/postViews', [App\Http\Controllers\UserApiController::class, 'post_views']);
Route::post('/add_news_post', [App\Http\Controllers\UserApiController::class, 'add_news_post'])->name('add_news_post');
Route::get('/get_news_post', [App\Http\Controllers\UserApiController::class, 'get_news_post'])->name('get_news_post');
Route::post('/delete_news_post', [App\Http\Controllers\UserApiController::class, 'delete_news_post']);
Route::post('/add/newsPost/permission', [App\Http\Controllers\UserApiController::class, 'allow_to_postNew']);


/**************************
 *******Favourite API *******
 **************************/
 
Route::post('/save/favourite_post', [App\Http\Controllers\UserApiController::class, 'save_favourite']);
Route::post('/remove/favourite_post', [App\Http\Controllers\UserApiController::class, 'remove_favourite']);
Route::post('/show/favourite_post', [App\Http\Controllers\UserApiController::class, 'show_favourite']);
Route::post('/show/favourite_postV2', [App\Http\Controllers\UserApiController::class, 'show_favouriteV2']);


/**************************
 *******User API *******
 **************************/
Route::post('/loginApi', [App\Http\Controllers\UserApiController::class, 'loginApi']);  
Route::post('/register/user', [App\Http\Controllers\UserApiController::class, 'signup_user']);   
Route::post('/estateAgent', [App\Http\Controllers\UserApiController::class, 'signup_estateAgent']); 
Route::post('/buyerSeller', [App\Http\Controllers\UserApiController::class, 'signupBuyerSeller']);
Route::post('/reset/password', [App\Http\Controllers\UserApiController::class, 'update_password']);  
Route::post('/update/user', [App\Http\Controllers\UserApiController::class, 'update_user']);
Route::get('/allUser/{user_id?}', [App\Http\Controllers\UserApiController::class, 'get_all_user']);


/**************************
 *******Content API *******
 **************************/
Route::get('/privacy_policy', [App\Http\Controllers\ContentController::class, 'privacy_policy']);

Route::get('/sendsms', [App\Http\Controllers\ContentController::class, 'sendsms']); 


