<?php



use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BloodRequestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Donor\AuthController as DonorAuthController;
use App\Http\Controllers\Patient\AuthController as PatientAuthController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DonationsController;
use App\Http\Controllers\SearchController;
use Google\Service\Compute\Router;
use Illuminate\Http\Request;
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

    // * Auth Routes
    Route::get("/user", fn () => Auth::guard('donor')->user() ? Auth::guard('admin')->user()  : response()->json(["error" => "Unauthenticated"], 401));

    Route::post("/login", [AdminAuthController::class, "login"]);
    Route::post("/register", [AdminAuthController::class, "register"]);
    Route::get("/search/{term}", [SearchController::class, "search"]);

    // * Protected Routes
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
        Route::get("/appointment/accepted", [AppointmentController::class, 'acceptedAppointments']);
        Route::get("/appointment/cancelled", [AppointmentController::class, 'cancelledAppointments']);
        Route::delete("/appointment/{appointment}", [AppointmentController::class, 'destroy']);
        Route::put("/appointment/{appointment}", [AppointmentController::class, 'rescheduleAppointment']);
        Route::post("/appointment/accept", [AppointmentController::class, 'acceptAppointment']);
        Route::get("/appointment/free-hours/{date}", [AppointmentController::class, 'getFreeHours']);


        // * Blood Requests     
        Route::get("/blood-requests/accepted", [BloodRequestController::class, "getAcceptedRequests"]);
        Route::get("/blood-requests/pending", [BloodRequestController::class, "getPendingRequests"]);

        Route::post("/blood-request/reject/{id}", [BloodRequestController::class, "reject"]);
        Route::post("/blood-request/accept/{id}", [BloodRequestController::class, "accept"]);
    });
});

// * Donor Routes
Route::prefix("donor")->group(function () {

    // * auth Routes
    Route::get("/user", fn () => Auth::guard('donor')->user() ? Auth::guard('donor')->user()  : response()->json(["error" => "Unauthenticated"], 401));
    Route::post("/user-check/google", [DonorAuthController::class, "checkIfExistsByEmail"]);
    Route::post("/login", [DonorAuthController::class, "login"]);
    Route::post("/register", [DonorAuthController::class, "register"]);
    Route::middleware('auth:sanctum')->post("/logout", [DonorAuthController::class, "logout"]);
    Route::middleware('auth:sanctum')->get("/donations", [DonorController::class, "getDonations"]);
    Route::post("/register/google", [DonorAuthController::class, "googleRegister"]);
    Route::post("/login/google", [DonorAuthController::class, "googleLogin"]);

    // * appointments routes
    Route::middleware("auth:sanctum")->post("/appointment/book", [AppointmentController::class, "book"]);
    Route::middleware("auth:sanctum")->get("/questions/appointment", [AppointmentController::class, "getAppointmentQuestions"]);
    Route::middleware("auth:sanctum")->get("/appointment/state", [AppointmentController::class, 'state']);
    Route::middleware("auth:sanctum")->delete("/appointment/cancel", [AppointmentController::class, 'cancel']);
    Route::middleware("auth:sanctum")->get("/appointment/free-hours/{date}", [AppointmentController::class, 'getFreeHours']);

    // * requests
    Route::middleware("auth:sanctum")->get("/requests", [BloodRequestController::class, 'getAcceptedRequests']);
});

// * Patient Routes
Route::prefix("patient")->group(function () {

    // * auth Routes
    Route::get("/user", fn () => Auth::guard('patient')->user() ? Auth::guard('patient')->user()  : response()->json(["error" => "unauthenticated"], 200));
    Route::get("/user-check/google", [PatientAuthController::class, "checkIfExistsByEmail"]);
    Route::post("/login", [PatientAuthController::class, "login"]);
    Route::post("/register", [PatientAuthController::class, "register"]);
    Route::middleware('auth.patient')->post("/logout", [PatientAuthController::class, "logout"]);
    Route::post("/register/google", [PatientAuthController::class, "googleRegister"]);
    Route::post("/login/google", [PatientAuthController::class, "googleLogin"]);
    Route::middleware('auth.patient')->post("/make-request", [BloodRequestController::class, "store"]);
    Route::middleware('auth.patient')->get("/requests", [PatientController::class, "getRequests"]);
    Route::middleware('auth.patient')->get("/last-request", [PatientController::class, "getLastRequestState"]);
    Route::middleware('auth.patient')->delete("/request/{id}", [BloodRequestController::class, "destroy"]);
});


Route::delete("/{guard}/notification/{id}", function ($guard, $id) {

    $user = Auth::guard($guard)->user();
    $user->notifications()
        ->where('id', $id)
        ->get()
        ->first()
        ->delete();
});

Route::get("/{guard}/notifications", function ($guard) {

    $user = Auth::guard($guard)->user();
    return $user->notifications;
});
