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
    'dashboard'
])->name('welcome.dashboard');


/*
|--------------------------------------------------------------------------
| User Activation Management
|--------------------------------------------------------------------------
*/

Route::get('/welcome-users', [
    WelcomeDashboardController::class,
    'users'
])->name('welcome.users');


/*
|--------------------------------------------------------------------------
| Resend Welcome Invitation
|--------------------------------------------------------------------------
*/

Route::post('/welcome-users/{user}/resend', [
    WelcomeDashboardController::class,
    'resend'
])->name('welcome.resend');


/*
|--------------------------------------------------------------------------
| Test Route To Create User & Send Welcome Email
|--------------------------------------------------------------------------
*/

Route::get('/create-user', function () {

    $user = User::create([
        'name' => 'Demo',
        'email' => 'demo@gmail.com',
        'password' => bcrypt('Demo@123'),
    ]);

    $user->sendWelcomeNotification(
        now()->addDay()
    );

    return view('success');
});


/*
|--------------------------------------------------------------------------
| Welcome Routes
|--------------------------------------------------------------------------
*/

Route::group([
    'middleware' => [
        'web',
        WelcomesNewUsers::class
    ]
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