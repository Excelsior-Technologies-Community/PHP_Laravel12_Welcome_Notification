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

Route::get('/', function () {
    return redirect()->route('welcome.dashboard');
});

Route::get('/welcome-dashboard', [
    WelcomeDashboardController::class,
    'dashboard',
])->name('welcome.dashboard');

/*
|--------------------------------------------------------------------------
| Onboarding Funnel Analytics & Audit Trail
|--------------------------------------------------------------------------
*/

Route::get('/welcome-funnel', [
    WelcomeDashboardController::class,
    'funnel',
])->name('welcome.funnel');


/*
|--------------------------------------------------------------------------
| User Management
|--------------------------------------------------------------------------
*/

Route::get('/welcome-users', [
    WelcomeDashboardController::class,
    'users',
])->name('welcome.users');

Route::post('/welcome-users/create', [
    WelcomeDashboardController::class,
    'createUser',
])->name('welcome.create-user');


/*
|--------------------------------------------------------------------------
| Resend & Reminders
|--------------------------------------------------------------------------
*/

Route::post('/welcome-users/{user}/resend', [
    WelcomeDashboardController::class,
    'resend',
])->name('welcome.resend');

Route::post('/welcome-users/bulk-resend', [
    WelcomeDashboardController::class,
    'bulkResend',
])->name('welcome.bulk-resend');

Route::post('/welcome-users/send-reminders', [
    WelcomeDashboardController::class,
    'sendExpiringReminders',
])->name('welcome.send-reminders');


/*
|--------------------------------------------------------------------------
| Revoke & Reactivate
|--------------------------------------------------------------------------
*/

Route::post('/welcome-users/{user}/revoke', [
    WelcomeDashboardController::class,
    'revoke',
])->name('welcome.revoke');

Route::post('/welcome-users/{user}/reactivate', [
    WelcomeDashboardController::class,
    'reactivate',
])->name('welcome.reactivate');


/*
|--------------------------------------------------------------------------
| Delete User & Export
|--------------------------------------------------------------------------
*/

Route::delete('/welcome-users/{user}', [
    WelcomeDashboardController::class,
    'destroy',
])->name('welcome.destroy');

Route::get('/welcome-users-export', [
    WelcomeDashboardController::class,
    'export',
])->name('welcome.export');


/*
|--------------------------------------------------------------------------
| Security & Device Audit Activity
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

    $ua = request()->userAgent();
    $parsed = \App\Services\UserAgentParser::parse($ua);

    \App\Models\WelcomeInvitationLog::create([
        'user_id' => $user->id,
        'action' => 'sent',
        'ip_address' => request()->ip(),
        'user_agent' => $ua,
        'device_type' => $parsed['device_type'],
        'browser' => $parsed['browser'],
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