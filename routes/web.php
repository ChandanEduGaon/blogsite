<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', [UserController::class, 'index'])->name('index');
Route::get('/check', function () {
    dd(phpinfo());
});


Route::get('/getSection', [UserController::class, 'getSection'])->name('getSection');


Route::get('/city', [UserController::class, 'city'])->name('city');
Route::get('/city/list', [UserController::class, 'city_list'])->name('city.list');
Route::post('/city/save', [UserController::class, 'city_save'])->name('city.save');


Route::get('/my_file', [UserController::class, 'my_file'])->name('my_file');
Route::get('/my_file/list', [UserController::class, 'my_file_list'])->name('my_file.list');
Route::post('/my_file/save', [UserController::class, 'my_file_save'])->name('my_file.save');
