<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\DonorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix("admin")->group(function () {

    Route::post("/login", [AuthController::class, "login"]);

    Route::middleware('auth:sanctum')->post("/logout", [AuthController::class, "logout"]);


    Route::prefix("donors")->group(function () {
        Route::get("/", [DonorController::class, "index"]);
        Route::post("/{donor}", [DonorController::class, "update"]);
        Route::post("/", [DonorController::class, "store"]);
        Route::delete("/{donor}",  [DonorController::class, "destroy"]);
    })->middleware("auth:sanctum");
});
