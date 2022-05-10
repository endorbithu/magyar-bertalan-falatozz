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
    return view('index');
});

Route::get('/crud/form/{eloquentModelClass}/{id?}', [\App\Http\Controllers\CrudController::class, 'form'])->name('crud.form');

Route::match(['post', 'put'], '/crud/form/{eloquentModelClass}/{id?}', [\App\Http\Controllers\CrudController::class, 'save'])->name('crud.save');
Route::match(['post', 'delete'], '/crud/delete/{eloquentModelClass}', [\App\Http\Controllers\CrudController::class, 'delete'])->name('crud.delete');
Route::get('/crud/{eloquentModelClass}', [\App\Http\Controllers\CrudController::class, 'list'])->name('crud.list');
Route::get('/crud/{eloquentModelClass}/{id}', [\App\Http\Controllers\CrudController::class, 'show'])->name('crud.show');
