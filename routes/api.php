<?php

use App\Http\Controllers\AutenticationController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\ResetPasswordController;
use Illuminate\Support\Facades\Log;

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

Route::middleware('guest')->group(function() {
    Route::post('myregister', [AutenticationController::class, 'store']);
    Route::post('mylogin', [AutenticationController::class, 'login']);
    Route::get('email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify']);
});

Route::middleware('api')->group(function () {
    Route::post('reset-password-request', [ResetPasswordController::class, 'create']);
    Route::get('reset-password-confirm/{token}', [ResetPasswordController::class, 'find']);
    Route::post('reset-password-accept', [ResetPasswordController::class, 'reset']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AutenticationController::class, 'logout']);
    
    Route::get('allusers', [UserController::class, 'index'])->name('users');
    Route::get('getUser/{user}',[UserController::class,'show'])->name('user');
    Route::patch('updateUser/{user}',[UserController::class,'update'])->name('user.update');
    Route::delete('deleteUser/{user}',[UserController::class,'destroy'])->name('user.destroy');
    
    Route::post('email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Email di verifica inviata!');
    })->name('verification.send');

    Route::patch('updatePassword/{user}', [UserController::class, 'updatePassword'])->name('user.updatePassword');
});









