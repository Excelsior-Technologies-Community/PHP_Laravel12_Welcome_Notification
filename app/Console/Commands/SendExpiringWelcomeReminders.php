<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\WelcomeInvitationLog;
use Illuminate\Console\Command;

class SendExpiringWelcomeReminders extends Command
{
    protected $signature = 'welcome:send-reminders {--hours=6 : Expiry threshold in hours}';

    protected $description = 'Send automated follow-up reminder emails for welcome notification links expiring soon';

    public function handle(): int
    {
        $thresholdHours = (int) $this->option('hours');

        $users = User::whereNotNull('welcome_valid_until')
            ->where('welcome_valid_until', '>', now())
            ->where('welcome_valid_until', '<=', now()->addHours($thresholdHours))
            ->get();

        $count = 0;

        foreach ($users as $user) {
            // Check if reminder was already sent in the last 12 hours
            $recentReminder = WelcomeInvitationLog::where('user_id', $user->id)
                ->where('action', 'reminder_sent')
                ->where('created_at', '>=', now()->subHours(12))
                ->exists();

            if ($recentReminder) {
                continue;
            }

            // Re-send notification keeping existing valid_until timestamp
            $user->sendWelcomeNotification($user->welcome_valid_until);

            WelcomeInvitationLog::create([
                'user_id' => $user->id,
                'action' => 'reminder_sent',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Console System Scheduler',
                'device_type' => 'Server Cron',
                'browser' => 'System Worker',
                'valid_until' => $user->welcome_valid_until,
                'details' => "Automated reminder sent for link expiring within {$thresholdHours} hours.",
            ]);

            $count++;
        }

        $this->info("Successfully dispatched {$count} expiring link reminder(s).");

        return Command::SUCCESS;
    }
}
