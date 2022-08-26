<?php

use App\Http\Controllers\Admin\FacultyController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
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

Route::prefix('/', function () {

});
Route::get('/', [HomeController::class, 'index'])->name('home');

Auth::routes();

Route::group(['middleware' => ['role:admin']], function () {
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('faculties', FacultyController::class);
    Route::resource('subjects', SubjectController::class);
    Route::resource('students', StudentController::class);
});
Route::middleware('permission:list')->group(function () {
    Route::resource('faculties', FacultyController::class)->only('index');
//    Route::resource('students/{students}', StudentController::class)->only(['edit' => 'update']);
});


Route::get('client', function () {
    return view('layouts.client');
});

Auth::routes();

