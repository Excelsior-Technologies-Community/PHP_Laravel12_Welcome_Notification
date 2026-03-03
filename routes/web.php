<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use Spatie\WelcomeNotification\WelcomesNewUsers;
use App\Http\Controllers\Auth\MyWelcomeController;

/*
|--------------------------------------------------------------------------
| Test Route To Create User & Send Welcome Email
|--------------------------------------------------------------------------
*/

Route::get('/create-user', function () {

    $user = \App\Models\User::create([
        'name' => 'Harry',
        'email' => 'Your_email@gmail.com',
        'password' => bcrypt('temporary123'),
    ]);

    $user->sendWelcomeNotification(now()->addDay());

    return view('success'); 
});


/*
|--------------------------------------------------------------------------
| Welcome Routes (IMPORTANT)
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => ['web', WelcomesNewUsers::class]], function () {

    Route::get('welcome/{user}', [MyWelcomeController::class, 'showWelcomeForm'])
        ->name('welcome');

    Route::post('welcome/{user}', [MyWelcomeController::class, 'savePassword']);
});