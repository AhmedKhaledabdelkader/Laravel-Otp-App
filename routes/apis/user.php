<?php



use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GoogleAuthController;

Route::post('/register', [AuthController::class, 'register'])->middleware(["validate.user"]);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->middleware(["validate.otp"]);
Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->middleware(["validate.resend"]);
Route::post('/login', [AuthController::class, 'login'])->middleware(['validate.auth']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware(['auth.user']);
Route::post('/logoutAll', [AuthController::class, 'logoutAll'])->middleware(['auth.user']);
Route::post('/forget', [AuthController::class, 'forgetPassword'])->middleware(["validate.resend"]);
Route::post('/reset', [AuthController::class, 'resetPassword'])->middleware(["validate.reset"]);


// Google Authentication

Route::middleware(['web'])->group(function(){


    Route::get('/google/redirect', [GoogleAuthController::class, 'redirectToGoogle']);

    Route::get('/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);


});




