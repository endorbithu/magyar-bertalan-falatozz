<?php

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

Route::get('/crud/{eloquentModelClass}', [\App\Http\Controllers\CrudController::class, 'list'])
    ->whereAlphaNumeric('eloquentModelClass');

Route::get('/crud/{eloquentModelClass}/{id}', [\App\Http\Controllers\CrudController::class, 'show'])
    ->whereAlphaNumeric('eloquentModelClass')
    ->whereNumber('id');

Route::post('/crud/{eloquentModelClass}', [\App\Http\Controllers\CrudController::class, 'save'])
    ->whereAlphaNumeric('eloquentModelClass');

Route::put('/crud/{eloquentModelClass}/{id}', [\App\Http\Controllers\CrudController::class, 'save'])
    ->whereAlphaNumeric('eloquentModelClass')
    ->whereNumber('id');

Route::delete('/crud/{eloquentModelClass}/{id}', [\App\Http\Controllers\CrudController::class, 'delete'])
    ->whereAlphaNumeric('eloquentModelClass')
    ->whereNumber('id');


