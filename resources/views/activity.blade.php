<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Invitation Activity</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            background: #f5f7fb;
            font-family: Arial, Helvetica, sans-serif;
        }

        .header {
            background: linear-gradient(
                135deg,
                #4e73df,
                #1cc88a
            );

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

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <h2>
                    Invitation Activity
                </h2>

                <p class="mb-0">
                    Complete welcome invitation history.
                </p>

            </div>

            <div class="d-flex gap-2">

                <a
                    href="{{ route('welcome.dashboard') }}"
                    class="btn btn-light"
                >
                    Dashboard
                </a>

                <a
                    href="{{ route('welcome.users') }}"
                    class="btn btn-light"
                >
                    Users
                </a>

            </div>

        </div>

    </div>


    <div class="card main-card mb-4">

        <div class="card-body">

            <form method="GET">

                <div class="row align-items-end">

                    <div class="col-md-4">

                        <label class="form-label">
                            Activity Type
                        </label>

                        <select
                            name="action"
                            class="form-select"
                        >

                            <option
                                value="all"
                                {{ $action === 'all' ? 'selected' : '' }}
                            >
                                All Activities
                            </option>

                            <option
                                value="sent"
                                {{ $action === 'sent' ? 'selected' : '' }}
                            >
                                Sent
                            </option>

                            <option
                                value="resent"
                                {{ $action === 'resent' ? 'selected' : '' }}
                            >
                                Resent
                            </option>

                            <option
                                value="reactivated"
                                {{ $action === 'reactivated' ? 'selected' : '' }}
                            >
                                Reactivated
                            </option>

                            <option
                                value="revoked"
                                {{ $action === 'revoked' ? 'selected' : '' }}
                            >
                                Revoked
                            </option>

                            <option
                                value="activated"
                                {{ $action === 'activated' ? 'selected' : '' }}
                            >
                                Activated
                            </option>

                        </select>

                    </div>


                    <div class="col-md-2">

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Filter
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>


    <div class="card main-card">

        <div class="card-body p-4">

            <div class="table-responsive">

                <table class="table table-hover">

                    <thead>

                    <tr>

                        <th>
                            ID
                        </th>

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
                            IP Address
                        </th>

                        <th>
                            Valid Until
                        </th>

                        <th>
                            Date
                        </th>

                    </tr>

                    </thead>

                    <tbody>

                    @forelse($activities as $activity)

                        <tr>

                            <td>
                                {{ $activity->id }}
                            </td>

                            <td>

                                @if($activity->user)

                                    <strong>
                                        {{ $activity->user->name }}
                                    </strong>

                                    <br>

                                    <small class="text-muted">
                                        {{ $activity->user->email }}
                                    </small>

                                @else

                                    <span class="text-muted">
                                        Deleted User
                                    </span>

                                @endif

                            </td>


                            <td>

                                @php

                                    $badge = match ($activity->action) {
                                        'activated' => 'success',
                                        'resent' => 'warning',
                                        'revoked' => 'danger',
                                        'reactivated' => 'primary',
                                        default => 'secondary',
                                    };

                                @endphp

                                <span
                                    class="badge bg-{{ $badge }}"
                                >
                                    {{ ucfirst($activity->action) }}
                                </span>

                            </td>


                            <td>
                                {{ $activity->details }}
                            </td>


                            <td>
                                {{ $activity->ip_address ?? '—' }}
                            </td>


                            <td>

                                @if($activity->valid_until)

                                    {{ $activity->valid_until->format('d M Y, h:i A') }}

                                @else

                                    —

                                @endif

                            </td>


                            <td>
                                {{ $activity->created_at->format('d M Y, h:i A') }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="text-center text-muted py-5"
                            >
                                No activity found.
                            </td>

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