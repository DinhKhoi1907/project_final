<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

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
//// TAG
Route::post('app/create_tag', 'AdminController@addTag');
Route::post('app/edit_tag', 'AdminController@editTag');
Route::get('app/get_tag', 'AdminController@getTag');
Route::post('app/delete_tag', 'AdminController@deleteTag');
Route::post('app/upload', 'AdminController@upload');
Route::post('app/delete_image', 'AdminController@deleteImage');

/// CATEGORY
Route::post('app/create_category', 'AdminController@addCategory');
Route::get('app/get_category', 'AdminController@getCategory');


Route::any('/', function () {
    return view('welcome');
});

Route::any('{slug}', function () {
    return view('welcome');
});