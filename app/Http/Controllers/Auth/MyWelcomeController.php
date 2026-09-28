<?php

namespace App\Http\Controllers\Auth;

use App\Models\WelcomeInvitationLog;
use Illuminate\Http\Request;
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
        $user = request()->route('user');

        if ($user) {
            WelcomeInvitationLog::create([
                'user_id' => $user->id,
                'action' => 'activated',
                'ip_address' => request()->ip(),
                'valid_until' => null,
                'details' => 'User activated the account successfully.',
            ]);
        }

        return redirect('/login')
            ->with(
                'success',
                'Password set successfully. You can login now!'
            );
    }
}