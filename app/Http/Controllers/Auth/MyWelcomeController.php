<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\RedirectResponse;
use Spatie\WelcomeNotification\WelcomeController;
use Symfony\Component\HttpFoundation\Response;

class MyWelcomeController extends WelcomeController
{
    /**
     * Response after the user successfully sets the password.
     */
    protected function sendPasswordSavedResponse(): Response
    {
        return redirect('/login')
            ->with(
                'success',
                'Password set successfully. You can login now!'
            );
    }
}