<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WelcomeInvitationLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WelcomeDashboardController extends Controller
{
    /**
     * Dashboard.
     */
    public function dashboard(Request $request): View
    {
        $range = $request->input('range', 'all');

        $dateFrom = match ($range) {
            'today' => now()->startOfDay(),
            '7days' => now()->subDays(6)->startOfDay(),
            '30days' => now()->subDays(29)->startOfDay(),
            default => null,
        };

        $userQuery = User::query();

        if ($dateFrom) {
            $userQuery->where('created_at', '>=', $dateFrom);
        }

        $totalUsers = (clone $userQuery)->count();

        $activatedUsers = (clone $userQuery)
            ->whereNull('welcome_valid_until')
            ->count();

        $pendingUsers = (clone $userQuery)
            ->whereNotNull('welcome_valid_until')
            ->where('welcome_valid_until', '>', now())
            ->count();

        $expiredUsers = (clone $userQuery)
            ->whereNotNull('welcome_valid_until')
            ->where('welcome_valid_until', '<=', now())
            ->count();

        $activationPercentage = $totalUsers > 0
            ? round(($activatedUsers / $totalUsers) * 100, 1)
            : 0;

        $logQuery = WelcomeInvitationLog::query();

        if ($dateFrom) {
            $logQuery->where('created_at', '>=', $dateFrom);
        }

        $totalInvitations = (clone $logQuery)
            ->whereIn('action', ['sent', 'resent', 'reactivated'])
            ->count();

        $totalResends = (clone $logQuery)
            ->where('action', 'resent')
            ->count();

        $totalRevoked = (clone $logQuery)
            ->where('action', 'revoked')
            ->count();

        $totalActivated = (clone $logQuery)
            ->where('action', 'activated')
            ->count();

        $recentUsers = User::oldest()
            ->take(5)
            ->get();

        $recentActivities = WelcomeInvitationLog::with('user')
            ->oldest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'range',
            'totalUsers',
            'activatedUsers',
            'pendingUsers',
            'expiredUsers',
            'activationPercentage',
            'totalInvitations',
            'totalResends',
            'totalRevoked',
            'totalActivated',
            'recentUsers',
            'recentActivities'
        ));
    }

    /**
     * Users page.
     */
    public function users(Request $request): View
    {
        $search = $request->input('search');
        $status = $request->input('status', 'all');
        $sort = $request->input('sort', 'oldest');
        $direction = $request->input('direction', 'asc');

        $allowedSorts = [
            'id',
            'name',
            'email',
            'created_at',
        ];

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        $users = User::query()

            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where(
                        'name',
                        'like',
                        '%' . $search . '%'
                    )->orWhere(
                        'email',
                        'like',
                        '%' . $search . '%'
                    );
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

            ->when($sort === 'latest', function ($query) {
                $query->latest();
            })

            ->when($sort !== 'latest', function ($query) use (
                $sort,
                $direction
            ) {
                $query->orderBy($sort, $direction);
            })

            ->paginate(5)
            ->withQueryString();

        return view('users', compact(
            'users',
            'search',
            'status',
            'sort',
            'direction'
        ));
    }

    /**
     * Resend invitation with custom validity.
     */
    public function resend(
        Request $request,
        User $user
    ): RedirectResponse {
        $hours = (int) $request->input('hours', 24);

        $allowedHours = [
            6,
            12,
            24,
            48,
            72,
        ];

        if (!in_array($hours, $allowedHours)) {
            $hours = 24;
        }

        if ($user->welcome_valid_until === null) {
            return back()->with(
                'error',
                'This user has already activated the account.'
            );
        }

        $expiresAt = now()->addHours($hours);

        $user->sendWelcomeNotification($expiresAt);

        $this->logActivity(
            $user,
            'resent',
            $expiresAt,
            "Welcome invitation resent for {$hours} hours."
        );

        return back()->with(
            'success',
            "Welcome invitation resent successfully. The new activation link is valid for {$hours} hours."
        );
    }

    /**
     * Bulk resend.
     */
    public function bulkResend(Request $request): RedirectResponse
    {
        $request->validate([
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
            'hours' => ['required', 'integer', 'in:6,12,24,48,72'],
        ]);

        $hours = (int) $request->input('hours');

        $users = User::whereIn(
            'id',
            $request->input('user_ids')
        )
            ->whereNotNull('welcome_valid_until')
            ->get();

        $count = 0;

        foreach ($users as $user) {
            $expiresAt = now()->addHours($hours);

            $user->sendWelcomeNotification($expiresAt);

            $this->logActivity(
                $user,
                'resent',
                $expiresAt,
                "Bulk invitation resend for {$hours} hours."
            );

            $count++;
        }

        return back()->with(
            'success',
            "{$count} invitation(s) resent successfully."
        );
    }

    /**
     * Revoke invitation.
     */
    public function revoke(User $user): RedirectResponse
    {
        if ($user->welcome_valid_until === null) {
            return back()->with(
                'error',
                'This user has already activated the account.'
            );
        }

        $user->welcome_valid_until = now()->subMinute();
        $user->save();

        $this->logActivity(
            $user,
            'revoked',
            $user->welcome_valid_until,
            'Welcome invitation revoked.'
        );

        return back()->with(
            'success',
            'Welcome invitation revoked successfully.'
        );
    }

    /**
     * Reactivate invitation.
     */
    public function reactivate(
        Request $request,
        User $user
    ): RedirectResponse {
        if ($user->welcome_valid_until === null) {
            return back()->with(
                'error',
                'This user has already activated the account.'
            );
        }

        $hours = (int) $request->input('hours', 24);

        if (!in_array($hours, [6, 12, 24, 48, 72])) {
            $hours = 24;
        }

        $expiresAt = now()->addHours($hours);

        $user->sendWelcomeNotification($expiresAt);

        $this->logActivity(
            $user,
            'reactivated',
            $expiresAt,
            "Invitation reactivated for {$hours} hours."
        );

        return back()->with(
            'success',
            "Invitation reactivated for {$hours} hours."
        );
    }

    /**
     * Delete user.
     */
    public function destroy(User $user): RedirectResponse
    {
        $email = $user->email;

        $user->delete();

        return back()->with(
            'success',
            "User {$email} deleted successfully."
        );
    }

    /**
     * Export users CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $search = $request->input('search');
        $status = $request->input('status', 'all');

        $users = User::query()

            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where(
                        'name',
                        'like',
                        '%' . $search . '%'
                    )->orWhere(
                        'email',
                        'like',
                        '%' . $search . '%'
                    );
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

            ->orderBy('id')
            ->get();

        return response()->streamDownload(
            function () use ($users) {
                $handle = fopen('php://output', 'w');

                fputcsv($handle, [
                    'ID',
                    'Name',
                    'Email',
                    'Status',
                    'Valid Until',
                    'Created At',
                ]);

                foreach ($users as $user) {
                    fputcsv($handle, [
                        $user->id,
                        $user->name,
                        $user->email,
                        $user->activationStatusLabel(),
                        $user->welcome_valid_until
                            ? $user->welcome_valid_until
                                ->format('Y-m-d H:i:s')
                            : '',
                        $user->created_at
                            ? $user->created_at
                                ->format('Y-m-d H:i:s')
                            : '',
                    ]);
                }

                fclose($handle);
            },
            'welcome-users-' . now()->format('Y-m-d-H-i-s') . '.csv',
            [
                'Content-Type' => 'text/csv',
            ]
        );
    }

    /**
     * Invitation activity history.
     */
    public function activity(Request $request): View
    {
        $action = $request->input('action', 'all');

        $activities = WelcomeInvitationLog::with('user')
            ->when(
                $action !== 'all',
                fn ($query) => $query->where('action', $action)
            )
            ->oldest()
            ->paginate(5)
            ->withQueryString();

        return view(
            'activity',
            compact(
                'activities',
                'action'
            )
        );
    }

    /**
     * Record activity.
     */
    private function logActivity(
        User $user,
        string $action,
        $validUntil = null,
        ?string $details = null
    ): void {
        WelcomeInvitationLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'ip_address' => request()->ip(),
            'valid_until' => $validUntil,
            'details' => $details,
        ]);
    }
}