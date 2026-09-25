<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WelcomeDashboardController extends Controller
{
    /**
     * Welcome notification dashboard.
     */
    public function dashboard(): View
    {
        $totalUsers = User::count();

        $activatedUsers = User::whereNull('welcome_valid_until')->count();

        $pendingUsers = User::whereNotNull('welcome_valid_until')
            ->where('welcome_valid_until', '>', now())
            ->count();

        $expiredUsers = User::whereNotNull('welcome_valid_until')
            ->where('welcome_valid_until', '<=', now())
            ->count();

        $recentUsers = User::latest()
            ->take(10)
            ->get();

        $activationPercentage = $totalUsers > 0
            ? round(($activatedUsers / $totalUsers) * 100, 1)
            : 0;

        return view('dashboard', compact(
            'totalUsers',
            'activatedUsers',
            'pendingUsers',
            'expiredUsers',
            'recentUsers',
            'activationPercentage'
        ));
    }

    /**
     * User activation management with search,
     * status filtering and pagination.
     */
    public function users(Request $request): View
    {
        $search = $request->input('search');
        $status = $request->input('status', 'all');

        $users = User::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->when($status === 'activated', function ($query) {
                $query->whereNull('welcome_valid_until');
            })
            ->when($status === 'pending', function ($query) {
                $query->whereNotNull('welcome_valid_until')
                    ->where('welcome_valid_until', '>', now());
            })
            ->when($status === 'expired', function ($query) {
                $query->whereNotNull('welcome_valid_until')
                    ->where('welcome_valid_until', '<=', now());
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('users', compact(
            'users',
            'search',
            'status'
        ));
    }

    /**
     * Resend welcome notification.
     */
    public function resend(User $user): RedirectResponse
    {
        if ($user->welcome_valid_until === null) {
            return back()->with(
                'error',
                'This user has already activated the account.'
            );
        }

        $expiresAt = now()->addDay();

        $user->sendWelcomeNotification($expiresAt);

        return back()->with(
            'success',
            'Welcome invitation resent successfully. The new activation link is valid for 24 hours.'
        );
    }
}