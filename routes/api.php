<?php



use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Donor\AuthController as DonorAuthController;
use App\Http\Controllers\Patient\AuthController as PatientAuthController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DonationsController;
use App\Http\Controllers\SearchController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
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


// * Admin Routes
Route::prefix("admin")->group(function () {
    Route::post("/login", [AdminAuthController::class, "login"]);
    Route::post("/register", [AdminAuthController::class, "register"]);
    Route::get("/search/{term}", [SearchController::class, "search"]);
    Route::middleware("auth.admin")->group(function () {

        // * authentication
        Route::post("/logout", [AdminAuthController::class, "logout"]);
        Route::get("/user", fn () => Auth::guard('admin')->user());

        // * Dashboard
        Route::get("/dashboard", [DashboardController::class, "getInfo"]);

        // * Donors
        Route::apiResource("donor", DonorController::class);
        Route::post("/donor/toggleActive", [DonorController::class, "toggleSelfActiveState"]);
        Route::post("/donor/toggleActive/{donor}", [DonorController::class, "toggleActiveState"]);
        Route::get("/requests", [DonorController::class, "getInactiveDonors"]);

        //* Patients
        Route::post("/patient/toggleActive/{patient}", [PatientController::class, "toggleActiveState"]);
        Route::apiResource("patient", PatientController::class);

        // * donations
        Route::apiResource("donation", DonationsController::class);

        // * Appointments
        Route::get("/appointment/requests", [AppointmentController::class, 'appointmentRequests']);
        Route::get("/appointment/today", [AppointmentController::class, 'todayAppointments']);
        Route::get("/appointment/scheduled", [AppointmentController::class, 'ScheduledAppointments']);
        Route::delete("/appointment/{appointment}", [AppointmentController::class, 'destroy']);
        Route::put("/appointment/{appointment}", [AppointmentController::class, 'rescheduleAppointment']);
        Route::post("/appointment/accept", [AppointmentController::class, 'acceptAppointment']);
    });
});

// * Donor Routes
Route::prefix("donor")->group(function () {
    Route::get("/user", fn () => Auth::guard('donor')->user() ? Auth::guard('donor')->user()  : response()->json(["error" => "unauthenticated"], 200));
    Route::post("/user-check/google", [DonorAuthController::class, "checkIfExistsByEmail"]);
    Route::post("/login", [DonorAuthController::class, "login"]);
    Route::post("/register", [DonorAuthController::class, "register"]);
    Route::middleware('auth:sanctum')->post("/logout", [DonorAuthController::class, "logout"]);
    Route::middleware('auth:sanctum')->get("/donations", [DonorController::class, "getDonations"]);
    Route::post("/register/google", [DonorAuthController::class, "googleRegister"]);
    Route::post("/login/google", [DonorAuthController::class, "googleLogin"]);
    Route::middleware("auth:sanctum")->get("/notifications", [DonorController::class, "getNotifications"]);
    Route::middleware("auth:sanctum")->post("/appointment/book", [AppointmentController::class, "book"]);
    Route::middleware("auth:sanctum")->get("/questions/appointment", [AppointmentController::class, "getAppointmentQuestions"]);
});

// * Patient Routes
Route::prefix("patient")->group(function () {
    Route::get("/user", fn () => Auth::guard('patient')->user() ? Auth::guard('patient')->user()  : response()->json(["error" => "unauthenticated"], 200));
    Route::get("/user-check/google", [PatientAuthController::class, "checkIfExistsByEmail"]);
    Route::post("/login", [PatientAuthController::class, "login"]);
    Route::post("/register", [PatientAuthController::class, "register"]);
    Route::middleware('auth:sanctum')->post("/logout", [PatientAuthController::class, "logout"]);
    Route::post("/register/google", [PatientAuthController::class, "googleRegister"]);
});


Route::delete("/{guard}/notification/{id}", function ($guard, $id) {

    $user = Auth::guard($guard)->user();

    $user->notifications()
        ->where('id', $id)
        ->get()
        ->first()
        ->delete();
});
