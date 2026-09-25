<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Activate Your Account</title>

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

        .activation-card {
            width: 100%;
            max-width: 450px;

            background: white;

            border-radius: 18px;

            padding: 35px;

            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        .icon {
            width: 65px;
            height: 65px;

            display: flex;
            align-items: center;
            justify-content: center;

            margin: 0 auto 20px;

            border-radius: 50%;

            background: #e8f5e9;

            font-size: 30px;
        }

        .form-control {
            padding: 12px;
        }

        .activate-btn {
            padding: 12px;

            font-weight: bold;

            border-radius: 8px;
        }

    </style>

</head>

<body>

<div class="activation-card">

    <div class="icon">
        🔐
    </div>

    <h2 class="text-center mb-2">
        Welcome {{ $user->name }}
    </h2>

    <p class="text-center text-muted mb-4">
        Set your password to activate your account.
    </p>


    @if($errors->any())

        <div class="alert alert-danger">

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- 
        IMPORTANT:
        Keep the complete signed URL when submitting the form.
        This preserves the expires and signature parameters.
    --}}

    <form
        method="POST"
        action="{{ request()->fullUrl() }}"
    >

        @csrf


        <div class="mb-3">

            <label class="form-label">
                New Password
            </label>

            <input
                type="password"
                name="password"
                class="form-control"
                placeholder="Enter new password"
                required
                minlength="6"
            >

        </div>


        <div class="mb-3">

            <label class="form-label">
                Confirm Password
            </label>

            <input
                type="password"
                name="password_confirmation"
                class="form-control"
                placeholder="Confirm new password"
                required
                minlength="6"
            >

        </div>


        <div class="alert alert-light border">

            <small class="text-muted">

                Your activation link is temporary and can only
                be used within its validity period.

            </small>

        </div>


        <button
            type="submit"
            class="btn btn-primary w-100 activate-btn"
        >
            Activate Account
        </button>

    </form>

</div>

</body>

</html>

