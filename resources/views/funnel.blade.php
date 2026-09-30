<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onboarding Funnel & Security Audit Trail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5f7fb;
            font-family: Arial, Helvetica, sans-serif;
        }

        .dashboard-header {
            background: linear-gradient(135deg, #4e73df, #1cc88a);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
        }

        .section-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.07);
        }

        .stat-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }

        .funnel-stage {
            border-left: 5px solid #4e73df;
            padding: 15px;
            background: #ffffff;
            border-radius: 10px;
            margin-bottom: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.04);
        }
    </style>
</head>

<body>
<div class="container py-5">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h2 class="mb-2">📊 Onboarding Funnel Analytics & Audit Trail</h2>
                <p class="mb-0">Track invitation conversion rates, link engagement, and security device logs.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('welcome.dashboard') }}" class="btn btn-light btn-sm fw-bold">Dashboard</a>
                <a href="{{ route('welcome.users') }}" class="btn btn-outline-light btn-sm">Users</a>
                <a href="{{ route('welcome.activity') }}" class="btn btn-outline-light btn-sm">Activity Logs</a>
            </div>
        </div>
    </div>

    <!-- Metric Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card stat-card border-start border-primary border-4">
                <div class="card-body">
                    <span class="text-muted small fw-bold">TOTAL INVITED</span>
                    <h2 class="fw-bold text-dark mt-2 mb-0">{{ number_format($totalUsers) }}</h2>
                    <small class="text-muted">Total invitations</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card border-start border-success border-4">
                <div class="card-body">
                    <span class="text-muted small fw-bold">CONVERSION RATE</span>
                    <h2 class="fw-bold text-success mt-2 mb-0">{{ $conversionRate }}%</h2>
                    <small class="text-muted">{{ number_format($activatedUsers) }} accounts activated</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card border-start border-info border-4">
                <div class="card-body">
                    <span class="text-muted small fw-bold">OPEN RATE</span>
                    <h2 class="fw-bold text-info mt-2 mb-0">{{ $openRate }}%</h2>
                    <small class="text-muted">{{ number_format($openedCount) }} unique link opens</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card border-start border-danger border-4">
                <div class="card-body">
                    <span class="text-muted small fw-bold">DROPOFF (EXPIRED)</span>
                    <h2 class="fw-bold text-danger mt-2 mb-0">{{ $dropoffRate }}%</h2>
                    <small class="text-muted">{{ number_format($expiredUsers) }} expired links</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Interactive Funnel Stages -->
        <div class="col-lg-7">
            <div class="card section-card">
                <div class="card-header bg-dark text-white fw-bold py-3">
                    📈 Visual Onboarding Conversion Funnel
                </div>
                <div class="card-body">
                    <!-- Stage 1: Sent -->
                    <div class="funnel-stage border-start border-primary border-5">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold text-dark">1. Invitations Dispatched</span>
                            <span class="badge bg-primary fs-6">{{ number_format($totalUsers) }} (100%)</span>
                        </div>
                        <div class="progress" style="height: 14px;">
                            <div class="progress-bar bg-primary" style="width: 100%">100%</div>
                        </div>
                    </div>

                    <!-- Stage 2: Opened -->
                    <div class="funnel-stage border-start border-info border-5">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold text-dark">2. Welcome Link Opened</span>
                            <span class="badge bg-info text-dark fs-6">{{ number_format($openedCount) }} ({{ $openRate }}%)</span>
                        </div>
                        <div class="progress" style="height: 14px;">
                            <div class="progress-bar bg-info" style="width: {{ $openRate }}%">{{ $openRate }}%</div>
                        </div>
                    </div>

                    <!-- Stage 3: Activated -->
                    <div class="funnel-stage border-start border-success border-5">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold text-dark">3. Account Activated (Password Set)</span>
                            <span class="badge bg-success fs-6">{{ number_format($activatedUsers) }} ({{ $conversionRate }}%)</span>
                        </div>
                        <div class="progress" style="height: 14px;">
                            <div class="progress-bar bg-success" style="width: {{ $conversionRate }}%">{{ $conversionRate }}%</div>
                        </div>
                    </div>

                    <!-- Stage 4: Expired -->
                    <div class="funnel-stage border-start border-danger border-5">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold text-dark">4. Dropped Off / Expired Links</span>
                            <span class="badge bg-danger fs-6">{{ number_format($expiredUsers) }} ({{ $dropoffRate }}%)</span>
                        </div>
                        <div class="progress" style="height: 14px;">
                            <div class="progress-bar bg-danger" style="width: {{ $dropoffRate }}%">{{ $dropoffRate }}%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Device & Browser Distribution -->
        <div class="col-lg-5">
            <div class="card section-card mb-4">
                <div class="card-header bg-dark text-white fw-bold py-3">
                    📱 Device Type Breakdown
                </div>
                <div class="card-body">
                    @forelse($deviceStats as $d)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-secondary">
                                @if($d->device_type == 'Mobile') 📱 @elseif($d->device_type == 'Tablet') 💻 @else 🖥️ @endif
                                {{ $d->device_type }}
                            </span>
                            <span class="badge bg-secondary font-monospace">{{ number_format($d->count) }} logs</span>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">No device data logged yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="card section-card">
                <div class="card-header bg-dark text-white fw-bold py-3">
                    🌐 Browser Distribution
                </div>
                <div class="card-body">
                    @forelse($browserStats as $b)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-dark">🌐 {{ $b->browser }}</span>
                            <span class="badge bg-primary font-monospace">{{ number_format($b->count) }} logs</span>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">No browser data logged yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Security & Device Audit Trail Table -->
    <div class="card section-card">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
            <span class="fw-bold">🛡️ Security & Device Audit Trail</span>
            <span class="badge bg-light text-dark">{{ $auditTrail->total() }} Events</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Action</th>
                            <th>IP Address</th>
                            <th>Device</th>
                            <th>Browser</th>
                            <th>Date / Time</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($auditTrail as $log)
                            <tr>
                                <td>
                                    <span class="fw-bold text-dark">{{ $log->user ? $log->user->name : 'N/A' }}</span><br>
                                    <small class="text-muted">{{ $log->user ? $log->user->email : '-' }}</small>
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($log->action) {
                                            'sent' => 'bg-primary',
                                            'resent' => 'bg-info text-dark',
                                            'opened' => 'bg-warning text-dark',
                                            'activated' => 'bg-success',
                                            'reminder_sent' => 'bg-purple text-white',
                                            'revoked' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} text-uppercase">{{ str_replace('_', ' ', $log->action) }}</span>
                                </td>
                                <td>
                                    <code class="text-dark">{{ $log->ip_address ?: '127.0.0.1' }}</code>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        @if($log->device_type == 'Mobile') 📱 @elseif($log->device_type == 'Tablet') 💻 @else 🖥️ @endif
                                        {{ $log->device_type ?: 'Desktop' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">🌐 {{ $log->browser ?: 'Chrome' }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $log->created_at ? $log->created_at->format('d M Y, h:i A') : '-' }}</small>
                                </td>
                                <td>
                                    <small class="text-secondary">{{ $log->details ?: '-' }}</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No security audit logs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($auditTrail->hasPages())
            <div class="card-footer bg-white">
                {{ $auditTrail->links() }}
            </div>
        @endif
    </div>
</div>
</body>
</html>
