<?php

use App\Filament\Panels\Auth\Controllers\EmailVerificationController;
use App\Filament\Panels\Auth\Pages\Approval;
use App\Filament\Panels\Auth\Pages\Deactivated;
use App\Filament\Panels\Auth\Pages\Verification;
use App\Http\Middleware\Approve;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\Verify;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'))->name('home');

Route::middleware(Authenticate::class)->group(function () {
    Route::get('/auth/email-verification/prompt', Verification::class)
        ->name('filament.auth.auth.email-verification.prompt');
    Route::get('/auth/account-approval/prompt', Approval::class)
        ->middleware(Verify::class)
        ->name('filament.auth.auth.account-approval.prompt');
    Route::get('/auth/deactivated-access/prompt', Deactivated::class)
        ->middleware([Approve::class, Verify::class])
        ->name('filament.auth.auth.deactivated-access.prompt');
    Route::get('/auth/email-verification/verify/{id}/{hash}', EmailVerificationController::class)
        ->name('filament.auth.auth.email-verification.verify');
});
