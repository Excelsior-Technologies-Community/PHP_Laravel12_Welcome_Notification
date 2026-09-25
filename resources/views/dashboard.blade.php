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
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
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
                    Monitor welcome invitations and account activation status.
                </p>

            </div>

            <div>

                <a
                    href="{{ route('welcome.users') }}"
                    class="btn btn-light"
                >
                    Manage Users
                </a>

            </div>

        </div>

    </div>


    <!-- Statistics -->

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
                        Activated Accounts
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
                        Pending Invitations
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
                        Expired Invitations
                    </div>

                    <div class="stat-number text-danger">
                        {{ $expiredUsers }}
                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- Activation Progress -->

    <div class="card section-card mb-4">

        <div class="card-body p-4">

            <div class="d-flex justify-content-between mb-2">

                <h5 class="mb-0">
                    Account Activation Rate
                </h5>

                <strong>
                    {{ $activationPercentage }}%
                </strong>

            </div>

            <div class="progress">

                <div
                    class="progress-bar bg-success"
                    role="progressbar"
                    style="width: {{ $activationPercentage }}%"
                    aria-valuenow="{{ $activationPercentage }}"
                    aria-valuemin="0"
                    aria-valuemax="100"
                ></div>

            </div>

        </div>

    </div>


    <!-- Recent Users -->

    <div class="card section-card">

        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-3">

                <h5 class="mb-0">
                    Recent Users
                </h5>

                <a
                    href="{{ route('welcome.users') }}"
                    class="btn btn-primary btn-sm"
                >
                    View All Users
                </a>

            </div>


            <div class="table-responsive">

                <table class="table table-hover">

                    <thead>

                    <tr>

                        <th>
                            Name
                        </th>

                        <th>
                            Email
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Valid Until
                        </th>

                    </tr>

                    </thead>

                    <tbody>

                    @forelse($recentUsers as $user)

                        <tr>

                            <td>
                                {{ $user->name }}
                            </td>

                            <td>
                                {{ $user->email }}
                            </td>

                            <td>

                                @if(is_null($user->welcome_valid_until))

                                    <span class="badge bg-success">
                                        Activated
                                    </span>

                                @elseif($user->welcome_valid_until->isFuture())

                                    <span class="badge bg-warning text-dark">
                                        Pending
                                    </span>

                                @else

                                    <span class="badge bg-danger">
                                        Expired
                                    </span>

                                @endif

                            </td>

                            <td>

                                @if($user->welcome_valid_until)

                                    {{ $user->welcome_valid_until->format('d M Y, h:i A') }}

                                @else

                                    —

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="4"
                                class="text-center text-muted py-4"
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

</div>

</body>

</html>