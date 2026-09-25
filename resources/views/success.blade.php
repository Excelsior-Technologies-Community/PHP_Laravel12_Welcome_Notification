<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>User Created</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            min-height: 100vh;

            display: flex;
            justify-content: center;
            align-items: center;

            background: linear-gradient(
                135deg,
                #4e73df,
                #1cc88a
            );

            font-family: Arial, Helvetica, sans-serif;
        }

        .success-card {
            width: 400px;
            max-width: 90%;

            background: white;

            padding: 40px;

            border-radius: 18px;

            text-align: center;

            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        .success-icon {
            font-size: 50px;
            margin-bottom: 15px;
        }

    </style>

</head>

<body>

<div class="success-card">

    <div class="success-icon">
        ✅
    </div>

    <h2 class="text-success">
        User Created!
    </h2>

    <p class="text-muted">
        Welcome email sent successfully.
    </p>

    <p class="text-muted">
        Please check the user's inbox.
    </p>


    <div class="d-grid gap-2 mt-4">

        <a
            href="{{ route('welcome.dashboard') }}"
            class="btn btn-primary"
        >
            Open Dashboard
        </a>

        <a
            href="{{ route('welcome.users') }}"
            class="btn btn-outline-secondary"
        >
            Manage Users
        </a>

    </div>

</div>

</body>

</html>