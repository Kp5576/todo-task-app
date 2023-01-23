<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Author\DashboardController as AuthorDashboard;
use App\Http\Controllers\TaskController;

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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/tasks', function () {
    return view('tasks');
})->middleware(['auth'])->name('tasks');

require __DIR__.'/auth.php';

Route::group(['as'=>'admin.', 'prefix'=>'admin', 'middleware'=>['auth', 'admin']], function() {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('tasks', [TaskController::class, 'index'])->name('tasks');
});

Route::group(['as'=>'author.', 'prefix'=>'author', 'middleware'=>['auth', 'author']], function() {
    Route::get('dashboard', [AuthorDashboard::class, 'index'])->name('dashboard');
});

Route::middleware(['auth'])->group(function() {
  
    
    Route::resource('tasks', TaskController::class);
    Route::get('task/edits', [TaskController::class, 'edits'])->name('task.edits');
    Route::post('task/updateedit', [TaskController::class, 'updateedit'])->name('task.updateedit');


});