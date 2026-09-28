<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\WelcomeDashboardController;
use Spatie\WelcomeNotification\WelcomesNewUsers;
use App\Http\Controllers\Auth\MyWelcomeController;

/*
|--------------------------------------------------------------------------
| Welcome Notification Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/welcome-dashboard', [
    WelcomeDashboardController::class,
    'dashboard',
])->name('welcome.dashboard');


/*
|--------------------------------------------------------------------------
| User Management
|--------------------------------------------------------------------------
*/

Route::get('/welcome-users', [
    WelcomeDashboardController::class,
    'users',
])->name('welcome.users');


/*
|--------------------------------------------------------------------------
| Resend Invitation
|--------------------------------------------------------------------------
*/

Route::post('/welcome-users/{user}/resend', [
    WelcomeDashboardController::class,
    'resend',
])->name('welcome.resend');


/*
|--------------------------------------------------------------------------
| Bulk Resend
|--------------------------------------------------------------------------
*/

Route::post('/welcome-users/bulk-resend', [
    WelcomeDashboardController::class,
    'bulkResend',
])->name('welcome.bulk-resend');


/*
|--------------------------------------------------------------------------
| Revoke Invitation
|--------------------------------------------------------------------------
*/

Route::post('/welcome-users/{user}/revoke', [
    WelcomeDashboardController::class,
    'revoke',
])->name('welcome.revoke');


/*
|--------------------------------------------------------------------------
| Reactivate Invitation
|--------------------------------------------------------------------------
*/

Route::post('/welcome-users/{user}/reactivate', [
    WelcomeDashboardController::class,
    'reactivate',
])->name('welcome.reactivate');


/*
|--------------------------------------------------------------------------
| Delete User
|--------------------------------------------------------------------------
*/

Route::delete('/welcome-users/{user}', [
    WelcomeDashboardController::class,
    'destroy',
])->name('welcome.destroy');


/*
|--------------------------------------------------------------------------
| CSV Export
|--------------------------------------------------------------------------
*/

Route::get('/welcome-users-export', [
    WelcomeDashboardController::class,
    'export',
])->name('welcome.export');


/*
|--------------------------------------------------------------------------
| Invitation Activity
|--------------------------------------------------------------------------
*/

Route::get('/welcome-activity', [
    WelcomeDashboardController::class,
    'activity',
])->name('welcome.activity');


/*
|--------------------------------------------------------------------------
| Test Route To Create User & Send Welcome Email
|--------------------------------------------------------------------------
*/

Route::get('/create-user', function () {

    $email = 'demo-' . time() . '@gmail.com';

    $user = User::create([
        'name' => 'Demo User',
        'email' => $email,
        'password' => bcrypt('Demo@123'),
    ]);

    $expiresAt = now()->addDay();

    $user->sendWelcomeNotification($expiresAt);

    \App\Models\WelcomeInvitationLog::create([
        'user_id' => $user->id,
        'action' => 'sent',
        'ip_address' => request()->ip(),
        'valid_until' => $expiresAt,
        'details' => 'Welcome invitation sent when user was created.',
    ]);

    return view('success', compact('user'));
});


/*
|--------------------------------------------------------------------------
| Welcome Routes
|--------------------------------------------------------------------------
*/

Route::group([
    'middleware' => [
        'web',
        WelcomesNewUsers::class,
    ],
], function () {

    Route::get(
        'welcome/{user}',
        [MyWelcomeController::class, 'showWelcomeForm']
    )->name('welcome');

    Route::post(
        'welcome/{user}',
        [MyWelcomeController::class, 'savePassword']
    );
});