<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Welcome Notification Dashboard</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            background: #f5f7fb;
            font-family: Arial, Helvetica, sans-serif;
        }

        .dashboard-header {
            background: linear-gradient(
                135deg,
                #4e73df,
                #1cc88a
            );

            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
        }

        .stat-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }

        .stat-number {
            font-size: 30px;
            font-weight: bold;
        }

        .stat-title {
            color: #6c757d;
            font-size: 14px;
        }

        .section-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.07);
        }

        .progress {
            height: 12px;
            border-radius: 10px;
        }

        .table {
            vertical-align: middle;
        }

    </style>

</head>

<body>

<div class="container py-5">

    <!-- Header -->

    <div class="dashboard-header">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

            <div>

                <h2 class="mb-2">
                    Welcome Notification Dashboard
                </h2>

                <p class="mb-0">
                    Monitor invitations and account activation.
                </p>

            </div>


            <div class="d-flex gap-2 flex-wrap">

                <a
                    href="{{ route('welcome.funnel') }}"
                    class="btn btn-warning fw-bold"
                >
                    📊 Funnel Analytics
                </a>

                <a
                    href="{{ route('welcome.users') }}"
                    class="btn btn-light"
                >
                    Manage Users
                </a>

                <a
                    href="{{ route('welcome.activity') }}"
                    class="btn btn-light"
                >
                    Activity Logs
                </a>

                <form action="{{ route('welcome.send-reminders') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="hours" value="6">
                    <button type="submit" class="btn btn-outline-light" onclick="return confirm('Send automated reminders for invitations expiring within 6 hours?')">
                        ⏰ Send Reminders
                    </button>
                </form>

            </div>

        </div>

    </div>


    <!-- Date filter -->

    <div class="card section-card mb-4">

        <div class="card-body">

            <form
                method="GET"
                action="{{ route('welcome.dashboard') }}"
            >

                <div class="row align-items-end g-3">

                    <div class="col-md-4">

                        <label class="form-label">
                            Dashboard Range
                        </label>

                        <select
                            name="range"
                            class="form-select"
                        >

                            <option
                                value="all"
                                {{ $range === 'all' ? 'selected' : '' }}
                            >
                                All Time
                            </option>

                            <option
                                value="today"
                                {{ $range === 'today' ? 'selected' : '' }}
                            >
                                Today
                            </option>

                            <option
                                value="7days"
                                {{ $range === '7days' ? 'selected' : '' }}
                            >
                                Last 7 Days
                            </option>

                            <option
                                value="30days"
                                {{ $range === '30days' ? 'selected' : '' }}
                            >
                                Last 30 Days
                            </option>

                        </select>

                    </div>


                    <div class="col-md-2">

                        <button
                            class="btn btn-primary w-100"
                            type="submit"
                        >
                            Apply
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>


    <!-- User statistics -->

    <div class="row g-4 mb-4">

        <div class="col-md-6 col-xl-3">

            <div class="card stat-card h-100">

                <div class="card-body">

                    <div class="stat-title">
                        Total Users
                    </div>

                    <div class="stat-number text-primary">
                        {{ $totalUsers }}
                    </div>

                </div>

            </div>

        </div>


        <div class="col-md-6 col-xl-3">

            <div class="card stat-card h-100">

                <div class="card-body">

                    <div class="stat-title">
                        Activated
                    </div>

                    <div class="stat-number text-success">
                        {{ $activatedUsers }}
                    </div>

                </div>

            </div>

        </div>


        <div class="col-md-6 col-xl-3">

            <div class="card stat-card h-100">

                <div class="card-body">

                    <div class="stat-title">
                        Pending
                    </div>

                    <div class="stat-number text-warning">
                        {{ $pendingUsers }}
                    </div>

                </div>

            </div>

        </div>


        <div class="col-md-6 col-xl-3">

            <div class="card stat-card h-100">

                <div class="card-body">

                    <div class="stat-title">
                        Expired
                    </div>

                    <div class="stat-number text-danger">
                        {{ $expiredUsers }}
                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- Invitation statistics -->

    <div class="row g-4 mb-4">

        <div class="col-md-3">

            <div class="card stat-card">

                <div class="card-body">

                    <div class="stat-title">
                        Invitations Sent
                    </div>

                    <div class="stat-number text-primary">
                        {{ $totalInvitations }}
                    </div>

                </div>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card stat-card">

                <div class="card-body">

                    <div class="stat-title">
                        Resent
                    </div>

                    <div class="stat-number text-warning">
                        {{ $totalResends }}
                    </div>

                </div>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card stat-card">

                <div class="card-body">

                    <div class="stat-title">
                        Revoked
                    </div>

                    <div class="stat-number text-danger">
                        {{ $totalRevoked }}
                    </div>

                </div>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card stat-card">

                <div class="card-body">

                    <div class="stat-title">
                        Activated
                    </div>

                    <div class="stat-number text-success">
                        {{ $totalActivated }}
                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- Activation -->

    <div class="card section-card mb-4">

        <div class="card-body p-4">

            <div class="d-flex justify-content-between mb-2">

                <h5>
                    Account Activation Rate
                </h5>

                <strong>
                    {{ $activationPercentage }}%
                </strong>

            </div>

            <div class="progress">

                <div
                    class="progress-bar bg-success"
                    style="width: {{ $activationPercentage }}%"
                ></div>

            </div>

        </div>

    </div>


    <!-- Recent users -->

    <div class="card section-card mb-4">

        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-3">

                <h5>
                    Recent Users
                </h5>

                <a
                    href="{{ route('welcome.users') }}"
                    class="btn btn-primary btn-sm"
                >
                    View Users
                </a>

            </div>


            <div class="table-responsive">

                <table class="table table-hover">

                    <thead>

                    <tr>

                        <th>
                            ID
                        </th>

                        <th>
                            Name
                        </th>

                        <th>
                            Email
                        </th>

                        <th>
                            Status
                        </th>

                    </tr>

                    </thead>

                    <tbody>

                    @forelse($recentUsers as $user)

                        <tr>

                            <td>
                                {{ $user->id }}
                            </td>

                            <td>
                                {{ $user->name }}
                            </td>

                            <td>
                                {{ $user->email }}
                            </td>

                            <td>

                                @if($user->activationStatus() === 'activated')

                                    <span class="badge bg-success">
                                        Activated
                                    </span>

                                @elseif($user->activationStatus() === 'pending')

                                    <span class="badge bg-warning text-dark">
                                        Pending
                                    </span>

                                @else

                                    <span class="badge bg-danger">
                                        Expired
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="4"
                                class="text-center text-muted"
                            >
                                No users found.
                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <!-- Recent activity -->

    <div class="card section-card">

        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-3">

                <h5>
                    Recent Invitation Activity
                </h5>

                <a
                    href="{{ route('welcome.activity') }}"
                    class="btn btn-outline-primary btn-sm"
                >
                    View All
                </a>

            </div>


            <div class="table-responsive">

                <table class="table table-hover">

                    <thead>

                    <tr>

                        <th>
                            User
                        </th>

                        <th>
                            Action
                        </th>

                        <th>
                            Details
                        </th>

                        <th>
                            Time
                        </th>

                    </tr>

                    </thead>

                    <tbody>

                    @forelse($recentActivities as $activity)

                        <tr>

                            <td>

                                {{ $activity->user?->email ?? 'Deleted User' }}

                            </td>

                            <td>

                                @if($activity->action === 'activated')

                                    <span class="badge bg-success">
                                        Activated
                                    </span>

                                @elseif($activity->action === 'resent')

                                    <span class="badge bg-warning text-dark">
                                        Resent
                                    </span>

                                @elseif($activity->action === 'revoked')

                                    <span class="badge bg-danger">
                                        Revoked
                                    </span>

                                @elseif($activity->action === 'reactivated')

                                    <span class="badge bg-primary">
                                        Reactivated
                                    </span>

                                @else

                                    <span class="badge bg-secondary">
                                        {{ ucfirst($activity->action) }}
                                    </span>

                                @endif

                            </td>

                            <td>
                                {{ $activity->details }}
                            </td>

                            <td>
                                {{ $activity->created_at->format('d M Y, h:i A') }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="4"
                                class="text-center text-muted"
                            >
                                No activity found.
                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

</body>

</html>