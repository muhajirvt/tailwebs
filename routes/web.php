<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('add-edit-student', [App\Http\Controllers\HomeController::class, 'addEditStudent'])->name('add.edit.student');
Route::get('delete-student/{studentId}', [App\Http\Controllers\HomeController::class, 'deleteStudent'])->name('delete.student');
Route::patch('update-student', [App\Http\Controllers\HomeController::class, 'updateStudent'])->name('update.student');


