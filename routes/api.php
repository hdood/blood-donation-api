<?php



use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Donor\AuthController as DonorAuthController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\SearchController;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Auth;
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
    Route::post("/register", [AuthController::class, "register"]);

    Route::middleware('auth.admin')->post("/logout", [AuthController::class, "logout"]);

    Route::middleware('auth.admin')->get("/user", fn () => Auth::guard('admin')->user());

    Route::get("/search/{term}", [SearchController::class, "search"]);

    Route::middleware("auth.admin")->apiResource("donor", DonorController::class);

    Route::middleware("auth.admin")->apiResource("patient", PatientController::class);
});

Route::prefix("donor")->group(function () {
    Route::post("/login", [DonorAuthController::class, "login"]);
    Route::post("/register", [DonorAuthController::class, "register"]);
    Route::middleware('auth:sanctum')->post("/logout", [DonorAuthController::class, "logout"]);
    Route::middleware('auth:sanctum')->get("/donations", [DonorController::class, "getDonations"]);
});
