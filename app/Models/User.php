<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\WelcomeNotification\ReceivesWelcomeNotification;

class User extends Authenticatable
{
    use Notifiable, ReceivesWelcomeNotification;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'welcome_valid_until' => 'datetime',
        ];
    }

    public function welcomeInvitationLogs()
    {
        return $this->hasMany(WelcomeInvitationLog::class);
    }

    public function activationStatus(): string
    {
        if (is_null($this->welcome_valid_until)) {
            return 'activated';
        }

        if ($this->welcome_valid_until->isFuture()) {
            return 'pending';
        }

        return 'expired';
    }

    public function activationStatusLabel(): string
    {
        return match ($this->activationStatus()) {
            'activated' => 'Activated',
            'pending' => 'Pending',
            'expired' => 'Expired',
            default => 'Unknown',
        };
    }
}