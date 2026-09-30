<?php

namespace App\Http\Controllers\Auth;

use App\Models\WelcomeInvitationLog;
use App\Services\UserAgentParser;
use Illuminate\Http\Request;
use Spatie\WelcomeNotification\WelcomeController;
use Symfony\Component\HttpFoundation\Response;

class MyWelcomeController extends WelcomeController
{
    /**
     * Show the welcome form and log security audit.
     */
    public function showWelcomeForm(Request $request, $user)
    {
        $targetUser = is_object($user) ? $user : \App\Models\User::find($user);

        if ($targetUser) {
            $ua = $request->userAgent();
            $parsed = UserAgentParser::parse($ua);

            WelcomeInvitationLog::create([
                'user_id' => $targetUser->id,
                'action' => 'opened',
                'ip_address' => $request->ip(),
                'user_agent' => $ua,
                'device_type' => $parsed['device_type'],
                'browser' => $parsed['browser'],
                'valid_until' => $targetUser->welcome_valid_until,
                'details' => "Welcome invitation link opened on {$parsed['device_type']} ({$parsed['browser']}).",
            ]);
        }

        return parent::showWelcomeForm($request, $user);
    }

    /**
     * Response after the user successfully sets the password.
     */
    protected function sendPasswordSavedResponse(): Response
    {
        $user = request()->route('user');
        $ua = request()->userAgent();
        $parsed = UserAgentParser::parse($ua);

        if ($user) {
            WelcomeInvitationLog::create([
                'user_id' => $user->id,
                'action' => 'activated',
                'ip_address' => request()->ip(),
                'user_agent' => $ua,
                'device_type' => $parsed['device_type'],
                'browser' => $parsed['browser'],
                'valid_until' => null,
                'details' => "User activated account and set password on {$parsed['device_type']} ({$parsed['browser']}).",
            ]);
        }

        return redirect('/login')
            ->with(
                'success',
                'Password set successfully. You can login now!'
            );
    }
}