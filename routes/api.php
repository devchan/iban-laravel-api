<?php

use App\Http\Controllers\IbanNumberController;
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

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['auth:sanctum', 'role:admin']], function () {
    Route::get('/iban-numbers', [IbanNumberController::class, 'index']);
});

Route::group(['middleware' => ['auth:sanctum', 'role:user']], function () {
    Route::post('/iban-number-checker', [IbanNumberController::class, 'store']);
});


