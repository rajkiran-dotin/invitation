<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EnquiryController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\TemplateController;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('home');
Route::post('/enquiry', [LandingController::class, 'enquiry'])->name('enquiry.store');

Route::get('/login', fn () => redirect()->route('admin.login'))->name('login');

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    });

    Route::middleware(['auth', 'admin'])->group(function (): void {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::resource('templates', TemplateController::class)->except('show');
        Route::resource('plans', PlanController::class)->except('show');
        Route::get('enquiries', [EnquiryController::class, 'index'])->name('enquiries.index');
        Route::patch('enquiries/{enquiry}', [EnquiryController::class, 'update'])->name('enquiries.update');
        Route::delete('enquiries/{enquiry}', [EnquiryController::class, 'destroy'])->name('enquiries.destroy');
    });
});
