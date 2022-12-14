<?php

use App\Http\Controllers\Admin\FacultyController;
use App\Http\Controllers\Admin\Student\StudentRegitationController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InformationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

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

Route::group(['middleware' => ['auth', 'role:admin']], function () {
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('faculties', FacultyController::class);
    Route::resource('subjects', SubjectController::class);
    Route::resource('students', StudentController::class);
//    Route::resource('marks',MarkController::class);
});
Route::middleware(['auth', 'permission:list'])->group(function () {
    Route::resource('faculties', FacultyController::class)->only('index');
    Route::resource('subjects', SubjectController::class)->only('index');
});
Route::get('information/{student}/', [InformationController::class, 'index'])->name('information');
Route::post('information/uploadImage/{student}', [InformationController::class, 'uploadImage'])->name('uploadImage');
Route::post('student/registerSubject', [StudentController::class, 'registerSubject'])->name('resgistation');

Route::get('list-student-deleted', [StudentController::class, 'getListDeleted'])
    ->name('student-list-deleted');

Route::post('student/{student}/restore', [StudentController::class, 'restore'])->name('student-restore');
Route::get('send-mail-student', [SubjectController::class, 'sendMailStudent'])->name('mail_subjects_all');

Route::prefix('student')->group(function () {
    Route::get('show-point/{student}/update', [StudentController::class, 'updatePoint'])->name('updatePoint');
    Route::post('{student}', [StudentController::class, 'handleUpdate']);
    Route::put('register/{id}/faculty', [StudentController::class, 'registerFaculty'])->name('registerFaculty');
});

Route::get('subject/{subject}/export', [SubjectController::class, 'export'])->name('export');

Route::post('subject/{subject}/import', [SubjectController::class, 'import'])->name('import');

Route::get('show-subject/{id}', [StudentController::class, 'showSubject'])->name('show-subject');

Route::post('updateMark/{student}',[SubjectController::class, 'updateMark'])->name('updateMark');
Route::get('client', function () {
    return view('layouts.client');
});

Route::get('/greeting/{locale}', function ($locale) {
    if (! in_array($locale, ['en', 'vn'])) {
        abort(401);
    }
    App::setLocale($locale);
    Session::put('locale', $locale);
    return redirect()->back();
    //
});

Auth::routes();
