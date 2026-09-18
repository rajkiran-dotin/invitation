<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EnquiryController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\TemplateCategoryController;
use App\Http\Controllers\Admin\TemplateController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\DashboardController as UserDashboardController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\TemplateLibraryController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->middleware('track.site.visit')->name('home');
Route::get('/templates', [TemplateLibraryController::class, 'index'])->name('templates.index');
Route::get('/templates/{template:slug}/preview', [TemplateLibraryController::class, 'preview'])->name('templates.preview');
Route::get('/templates/{template:slug}', [TemplateLibraryController::class, 'show'])->name('templates.show');
Route::get('/templates/{template:slug}/use', [TemplateLibraryController::class, 'select'])->name('templates.use');
Route::post('/enquiry', [LandingController::class, 'enquiry'])->name('enquiry.store');
Route::get('/invite/{slug}', [InvitationController::class, 'showPublic'])->name('invitations.public');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');
    Route::get('/auth/google/redirect', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::prefix('dashboard')->name('dashboard.')->group(function (): void {
        Route::get('/invitations', [UserDashboardController::class, 'invitations'])->name('invitations');
        Route::get('/create', fn () => redirect()->route('invitations.create'))->name('create');
        Route::get('/profile', [UserDashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [UserDashboardController::class, 'updateProfile'])->name('profile.update');
    });

    Route::get('/invitations/create', [InvitationController::class, 'create'])->name('invitations.create');
    Route::get('/invitations/{invitation}/edit', [InvitationController::class, 'edit'])->name('invitations.edit');
    Route::put('/invitations/{invitation}', [InvitationController::class, 'update'])->name('invitations.update');
    Route::get('/invitations/{invitation}/preview', [InvitationController::class, 'preview'])->name('invitations.preview');
    Route::post('/invitations/{invitation}/publish', [InvitationController::class, 'publish'])->name('invitations.publish');
    Route::post('/invitations/{invitation}/unpublish', [InvitationController::class, 'unpublish'])->name('invitations.unpublish');
    Route::get('/invitations/{invitation}/success', [InvitationController::class, 'success'])->name('invitations.success');
    Route::delete('/invitations/{invitation}/gallery/{image}', [InvitationController::class, 'destroyGalleryImage'])->name('invitations.gallery.destroy');
});

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.store');
    });

    Route::middleware(['auth', 'admin'])->group(function (): void {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::patch('template-categories/{template_category}/toggle', [TemplateCategoryController::class, 'toggle'])->name('template-categories.toggle');
        Route::resource('template-categories', TemplateCategoryController::class)->except('show');
        Route::patch('templates/{template}/status', [TemplateController::class, 'updateStatus'])->name('templates.status');
        Route::resource('templates', TemplateController::class)->except('show');
        Route::resource('plans', PlanController::class)->except('show');
        Route::get('enquiries', [EnquiryController::class, 'index'])->name('enquiries.index');
        Route::patch('enquiries/{enquiry}', [EnquiryController::class, 'update'])->name('enquiries.update');
        Route::delete('enquiries/{enquiry}', [EnquiryController::class, 'destroy'])->name('enquiries.destroy');
    });
});
