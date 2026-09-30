<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Activation Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5f7fb;
            font-family: Arial, Helvetica, sans-serif;
        }

        .page-header {
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

        .action-buttons form {
            display: inline-block;
        }
    </style>
</head>

<body>
<div class="container py-5">
    <!-- Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h2 class="mb-2">User Activation Management</h2>
                <p class="mb-0">Search, filter, send custom TTL invitations, revoke and manage users.</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button type="button" class="btn btn-light fw-bold" data-bs-toggle="modal" data-bs-target="#createUserModal">
                    ➕ New Invitation
                </button>
                <a href="{{ route('welcome.funnel') }}" class="btn btn-warning fw-bold">
                    📊 Funnel Analytics
                </a>
                <a href="{{ route('welcome.dashboard') }}" class="btn btn-light">
                    Dashboard
                </a>
                <a href="{{ route('welcome.activity') }}" class="btn btn-light">
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

    <!-- Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <!-- Search & Filter -->
    <div class="card main-card mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('welcome.users') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="{{ $search }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Statuses</option>
                            <option value="activated" {{ $status === 'activated' ? 'selected' : '' }}>Activated</option>
                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="expired" {{ $status === 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sort</label>
                        <select name="sort" class="form-select">
                            <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>Latest</option>
                            <option value="id" {{ $sort === 'id' ? 'selected' : '' }}>ID</option>
                            <option value="name" {{ $sort === 'name' ? 'selected' : '' }}>Name</option>
                            <option value="email" {{ $sort === 'email' ? 'selected' : '' }}>Email</option>
                            <option value="created_at" {{ $sort === 'created_at' ? 'selected' : '' }}>Created Date</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk actions -->
    <form method="POST" action="{{ route('welcome.bulk-resend') }}" id="bulkForm">
        @csrf
        <div class="card main-card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Custom Invitation Validity (TTL)</label>
                        <select name="hours" class="form-select">
                            <option value="1">1 Hour (Urgent)</option>
                            <option value="6">6 Hours</option>
                            <option value="12">12 Hours</option>
                            <option value="24" selected>24 Hours (1 Day)</option>
                            <option value="48">48 Hours (2 Days)</option>
                            <option value="72">72 Hours (3 Days)</option>
                            <option value="168">168 Hours (7 Days)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-warning" onclick="return confirm('Resend invitations to selected users with chosen TTL?')">
                            Bulk Resend Selected
                        </button>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="{{ route('welcome.export', request()->query()) }}" class="btn btn-success">
                            Export CSV
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="card main-card">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Valid Until</th>
                                <th>Actions (Custom TTL)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>
                                        @if($user->welcome_valid_until)
                                            <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="user-checkbox">
                                        @endif
                                    </td>
                                    <td>{{ $user->id }}</td>
                                    <td><strong>{{ $user->name }}</strong></td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->activationStatus() === 'activated')
                                            <span class="badge bg-success">Activated</span>
                                        @elseif($user->activationStatus() === 'pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @else
                                            <span class="badge bg-danger">Expired</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->welcome_valid_until)
                                            {{ $user->welcome_valid_until->format('d M Y, h:i A') }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="action-buttons">
                                        @if($user->welcome_valid_until)
                                            @if($user->welcome_valid_until->isFuture())
                                                <div class="btn-group btn-group-sm me-1">
                                                    <form method="POST" action="{{ route('welcome.resend', $user) }}" class="d-inline">
                                                        @csrf
                                                        <select name="hours" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                                            <option value="">Resend...</option>
                                                            <option value="1">1 Hour</option>
                                                            <option value="6">6 Hours</option>
                                                            <option value="12">12 Hours</option>
                                                            <option value="24">24 Hours</option>
                                                            <option value="72">3 Days</option>
                                                            <option value="168">7 Days</option>
                                                        </select>
                                                    </form>
                                                </div>

                                                <form method="POST" action="{{ route('welcome.revoke', $user) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Revoke this invitation?')">
                                                        Revoke
                                                    </button>
                                                </form>
                                            @else
                                                <div class="btn-group btn-group-sm me-1">
                                                    <form method="POST" action="{{ route('welcome.reactivate', $user) }}" class="d-inline">
                                                        @csrf
                                                        <select name="hours" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                                            <option value="">Reactivate...</option>
                                                            <option value="1">1 Hour</option>
                                                            <option value="6">6 Hours</option>
                                                            <option value="12">12 Hours</option>
                                                            <option value="24">24 Hours</option>
                                                            <option value="72">3 Days</option>
                                                            <option value="168">7 Days</option>
                                                        </select>
                                                    </form>
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-success small fw-bold me-2">Account Active</span>
                                        @endif

                                        <form method="POST" action="{{ route('welcome.destroy', $user) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-dark" onclick="return confirm('Delete {{ $user->email }}?')">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $users->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal: Create New User Invitation with Custom TTL -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('welcome.create-user') }}">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="createUserModalLabel">➕ Send New Welcome Invitation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="userName" class="form-label fw-bold">Full Name</label>
                        <input type="text" class="form-control" id="userName" name="name" required placeholder="e.g. Rahul Sharma">
                    </div>
                    <div class="mb-3">
                        <label for="userEmail" class="form-label fw-bold">Email Address</label>
                        <input type="email" class="form-control" id="userEmail" name="email" required placeholder="e.g. rahul@example.com">
                    </div>
                    <div class="mb-3">
                        <label for="linkValidity" class="form-label fw-bold">Custom Link Validity (Dynamic TTL)</label>
                        <select class="form-select" id="linkValidity" name="hours" required>
                            <option value="1">1 Hour (Urgent Expiry)</option>
                            <option value="6">6 Hours</option>
                            <option value="12">12 Hours</option>
                            <option value="24" selected>24 Hours (1 Day - Default)</option>
                            <option value="48">48 Hours (2 Days)</option>
                            <option value="72">72 Hours (3 Days)</option>
                            <option value="168">168 Hours (7 Days)</option>
                        </select>
                        <small class="text-muted">The user will receive an email invitation with a secure link valid for the selected time.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Send Welcome Invitation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('selectAll')?.addEventListener('change', function () {
        document.querySelectorAll('.user-checkbox').forEach(function (checkbox) {
            checkbox.checked = this.checked;
        }, this);
    });

    document.getElementById('bulkForm')?.addEventListener('submit', function (event) {
        const selected = document.querySelectorAll('.user-checkbox:checked');
        if (selected.length === 0) {
            event.preventDefault();
            alert('Please select at least one pending or expired user.');
        }
    });
</script>
</body>
</html>