<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation Activity & Device Audit Log</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5f7fb;
            font-family: Arial, Helvetica, sans-serif;
        }

        .header {
            background: linear-gradient(135deg, #4e73df, #1cc88a);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 25px;
        }

        .main-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.07);
        }

        .table {
            vertical-align: middle;
        }
    </style>
</head>

<body>
<div class="container py-5">
    <div class="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h2>🛡️ Invitation Activity & Device Audit Log</h2>
                <p class="mb-0">Complete security audit history, IP logs, device types, and link activity.</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('welcome.funnel') }}" class="btn btn-warning fw-bold">
                    📊 Funnel Analytics
                </a>
                <a href="{{ route('welcome.dashboard') }}" class="btn btn-light">
                    Dashboard
                </a>
                <a href="{{ route('welcome.users') }}" class="btn btn-light">
                    Users
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card main-card mb-4">
        <div class="card-body">
            <form method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Activity Action</label>
                        <select name="action" class="form-select">
                            <option value="all" {{ $action === 'all' ? 'selected' : '' }}>All Activities</option>
                            <option value="sent" {{ $action === 'sent' ? 'selected' : '' }}>Sent</option>
                            <option value="opened" {{ $action === 'opened' ? 'selected' : '' }}>Link Opened</option>
                            <option value="activated" {{ $action === 'activated' ? 'selected' : '' }}>Activated</option>
                            <option value="resent" {{ $action === 'resent' ? 'selected' : '' }}>Resent</option>
                            <option value="reminder_sent" {{ $action === 'reminder_sent' ? 'selected' : '' }}>Reminder Sent</option>
                            <option value="reactivated" {{ $action === 'reactivated' ? 'selected' : '' }}>Reactivated</option>
                            <option value="revoked" {{ $action === 'revoked' ? 'selected' : '' }}>Revoked</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">Filter Logs</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Activity & Audit Logs Table -->
    <div class="card main-card">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>IP Address</th>
                            <th>Device</th>
                            <th>Browser</th>
                            <th>Valid Until</th>
                            <th>Date / Time</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $activity)
                            <tr>
                                <td>{{ $activity->id }}</td>
                                <td>
                                    @if($activity->user)
                                        <strong>{{ $activity->user->name }}</strong><br>
                                        <small class="text-muted">{{ $activity->user->email }}</small>
                                    @else
                                        <span class="text-muted">Deleted User</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $badge = match ($activity->action) {
                                            'activated' => 'success',
                                            'opened' => 'warning text-dark',
                                            'resent' => 'info text-dark',
                                            'reminder_sent' => 'primary',
                                            'revoked' => 'danger',
                                            'reactivated' => 'primary',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $badge }} text-uppercase">
                                        {{ str_replace('_', ' ', $activity->action) }}
                                    </span>
                                </td>
                                <td><code>{{ $activity->ip_address ?? '127.0.0.1' }}</code></td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        @if($activity->device_type == 'Mobile') 📱 @elseif($activity->device_type == 'Tablet') 💻 @else 🖥️ @endif
                                        {{ $activity->device_type ?: 'Desktop' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">🌐 {{ $activity->browser ?: 'Chrome' }}</span>
                                </td>
                                <td>
                                    @if($activity->valid_until)
                                        <small>{{ $activity->valid_until->format('d M Y, h:i A') }}</small>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $activity->created_at ? $activity->created_at->format('d M Y, h:i A') : '-' }}</small>
                                </td>
                                <td>
                                    <small class="text-secondary">{{ $activity->details ?: '-' }}</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">No activity logs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $activities->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
</body>
</html>