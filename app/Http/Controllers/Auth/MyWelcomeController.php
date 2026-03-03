<?php

namespace App\Http\Controllers\Auth;

use Spatie\WelcomeNotification\WelcomeController;
use Symfony\Component\HttpFoundation\Response;

class MyWelcomeController extends WelcomeController
{
    protected function sendPasswordSavedResponse(): Response
    {
        return redirect('/login')
            ->with('success', 'Password set successfully. You can login now!');
    }
}