<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>User Activation Management</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            background: #f5f7fb;
            font-family: Arial, Helvetica, sans-serif;
        }

        .page-header {
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

    <!-- Header -->

    <div class="page-header">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

            <div>

                <h2 class="mb-2">
                    User Activation Management
                </h2>

                <p class="mb-0">
                    Search, filter and manage welcome invitations.
                </p>

            </div>

            <a
                href="{{ route('welcome.dashboard') }}"
                class="btn btn-light"
            >
                Dashboard
            </a>

        </div>

    </div>


    <!-- Messages -->

    @if(session('success'))

        <div class="alert alert-success">

            {{ session('success') }}

        </div>

    @endif


    @if(session('error'))

        <div class="alert alert-danger">

            {{ session('error') }}

        </div>

    @endif


    <!-- Search and Filter -->

    <div class="card main-card mb-4">

        <div class="card-body p-4">

            <form
                method="GET"
                action="{{ route('welcome.users') }}"
            >

                <div class="row g-3 align-items-end">

                    <div class="col-md-6">

                        <label class="form-label">
                            Search User
                        </label>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search by name or email..."
                            value="{{ $search }}"
                        >

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Activation Status
                        </label>

                        <select
                            name="status"
                            class="form-select"
                        >

                            <option
                                value="all"
                                {{ $status === 'all' ? 'selected' : '' }}
                            >
                                All Users
                            </option>

                            <option
                                value="activated"
                                {{ $status === 'activated' ? 'selected' : '' }}
                            >
                                Activated
                            </option>

                            <option
                                value="pending"
                                {{ $status === 'pending' ? 'selected' : '' }}
                            >
                                Pending
                            </option>

                            <option
                                value="expired"
                                {{ $status === 'expired' ? 'selected' : '' }}
                            >
                                Expired
                            </option>

                        </select>

                    </div>


                    <div class="col-md-2 d-grid">

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Search
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>


    <!-- Users -->

    <div class="card main-card">

        <div class="card-body p-4">

            <div class="table-responsive">

                <table class="table table-hover">

                    <thead>

                    <tr>

                        <th>
                            #
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

                        <th>
                            Valid Until
                        </th>

                        <th>
                            Action
                        </th>

                    </tr>

                    </thead>


                    <tbody>

                    @forelse($users as $user)

                        <tr>

                            <td>
                                {{ $user->id }}
                            </td>

                            <td>
                                <strong>
                                    {{ $user->name }}
                                </strong>
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


                            <td>

                                @if($user->welcome_valid_until)

                                    <form
                                        method="POST"
                                        action="{{ route('welcome.resend', $user) }}"
                                        onsubmit="return confirm('Resend welcome invitation to {{ $user->email }}?');"
                                    >

                                        @csrf

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-primary"
                                        >
                                            Resend Welcome
                                        </button>

                                    </form>

                                @else

                                    <span class="text-success">
                                        Account Active
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="text-center text-muted py-5"
                            >
                                No users found.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>


            <!-- Pagination -->

            <div class="mt-4">

                {{ $users->links('pagination::bootstrap-5') }}

            </div>

        </div>

    </div>

</div>

</body>

</html>