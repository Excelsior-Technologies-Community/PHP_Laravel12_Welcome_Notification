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

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;

            display: flex;
            justify-content: center;
            align-items: center;

            margin: 0;
            padding: 20px;

            background: linear-gradient(
                135deg,
                #4e73df 0%,
                #224abe 45%,
                #1cc88a 100%
            );

            font-family: Arial, Helvetica, sans-serif;
        }

        .activation-card {
            width: 100%;
            max-width: 470px;

            background: #ffffff;

            border-radius: 20px;

            padding: 38px;

            box-shadow:
                0 20px 50px rgba(0, 0, 0, 0.25);
        }

        .icon {
            width: 72px;
            height: 72px;

            display: flex;
            align-items: center;
            justify-content: center;

            margin: 0 auto 20px;

            border-radius: 50%;

            background: #e8f5e9;

            font-size: 34px;
        }

        .welcome-title {
            color: #2c3e50;

            font-weight: 700;
        }

        .subtitle {
            line-height: 1.6;
        }

        .form-label {
            font-weight: 600;

            color: #34495e;
        }

        .form-control {
            padding: 12px 14px;

            border-radius: 9px;

            border: 1px solid #ced4da;
        }

        .form-control:focus {
            border-color: #4e73df;

            box-shadow:
                0 0 0 0.2rem rgba(78, 115, 223, 0.15);
        }

        .activate-btn {
            padding: 13px;

            font-weight: 700;

            border-radius: 9px;

            background: #4e73df;

            border: none;
        }

        .activate-btn:hover {
            background: #224abe;
        }

        .info-box {
            background: #f8f9fa;

            border: 1px solid #e3e6f0;

            border-radius: 10px;

            padding: 13px 15px;
        }

        .password-hint {
            font-size: 13px;

            color: #6c757d;
        }

        .footer-text {
            margin-top: 22px;

            text-align: center;

            font-size: 12px;

            color: #8a8f98;
        }

        @media (max-width: 576px) {

            .activation-card {
                padding: 25px 20px;
            }

            .welcome-title {
                font-size: 24px;
            }

        }

    </style>

</head>

<body>

<div class="activation-card">

    {{-- Welcome Icon --}}
    <div class="icon">
        🔐
    </div>


    {{-- Welcome Message --}}
    <h2 class="text-center mb-2 welcome-title">
        Welcome {{ $user->name }}
    </h2>

    <p class="text-center text-muted subtitle mb-4">
        Set your password below to activate your account.
    </p>


    {{-- Validation Errors --}}
    @if($errors->any())

        <div class="alert alert-danger">

            <strong>
                Please fix the following errors:
            </strong>

            <ul class="mb-0 mt-2">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- Success Message --}}
    @if(session('success'))

        <div class="alert alert-success">

            {{ session('success') }}

        </div>

    @endif


    {{-- User Email --}}
    <div class="info-box mb-4">

        <div class="small text-muted">
            Account Email
        </div>

        <strong>
            {{ $user->email }}
        </strong>

    </div>


    {{-- 
        IMPORTANT:
        Use request()->fullUrl() so that the complete
        signed URL is submitted.

        This preserves:
        ?expires=...
        &signature=...

        Required by Laravel signed routes.
    --}}

    <form
        method="POST"
        action="{{ request()->fullUrl() }}"
    >

        @csrf


        {{-- Password --}}
        <div class="mb-3">

            <label
                for="password"
                class="form-label"
            >
                New Password
            </label>

            <input
                type="password"
                id="password"
                name="password"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="Enter new password"
                minlength="6"
                autocomplete="new-password"
                required
            >

            @error('password')

                <div class="invalid-feedback">
                    {{ $message }}
                </div>

            @enderror

            <div class="password-hint mt-1">
                Password must contain at least 6 characters.
            </div>

        </div>


        {{-- Confirm Password --}}
        <div class="mb-4">

            <label
                for="password_confirmation"
                class="form-label"
            >
                Confirm Password
            </label>

            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                class="form-control @error('password_confirmation') is-invalid @enderror"
                placeholder="Confirm your password"
                minlength="6"
                autocomplete="new-password"
                required
            >

            @error('password_confirmation')

                <div class="invalid-feedback">
                    {{ $message }}
                </div>

            @enderror

        </div>


        {{-- Invitation Information --}}
        <div class="info-box mb-4">

            <div class="d-flex align-items-start">

                <div class="me-2">
                    ⏱️
                </div>

                <div>

                    <strong>
                        Invitation Link
                    </strong>

                    <div class="small text-muted mt-1">

                        This activation link is temporary and can
                        only be used during its validity period.

                    </div>

                </div>

            </div>

        </div>


        {{-- Activate Button --}}
        <button
            type="submit"
            class="btn btn-primary w-100 activate-btn"
        >
            🔓 Activate Account
        </button>

    </form>


    <div class="footer-text">

        Welcome Notification System

    </div>

</div>


<script>

    document.addEventListener('DOMContentLoaded', function () {

        const password =
            document.getElementById('password');

        const confirmation =
            document.getElementById('password_confirmation');

        const form =
            document.querySelector('form');


        form.addEventListener('submit', function (event) {

            if (
                password.value !==
                confirmation.value
            ) {

                event.preventDefault();

                confirmation.classList.add('is-invalid');

                let error =
                    confirmation.parentElement.querySelector(
                        '.password-match-error'
                    );

                if (!error) {

                    error =
                        document.createElement('div');

                    error.className =
                        'invalid-feedback password-match-error';

                    error.textContent =
                        'Passwords do not match.';

                    confirmation.parentElement.appendChild(error);

                }

            } else {

                confirmation.classList.remove(
                    'is-invalid'
                );

                const error =
                    confirmation.parentElement.querySelector(
                        '.password-match-error'
                    );

                if (error) {
                    error.remove();
                }

            }

        });


        confirmation.addEventListener(
            'input',
            function () {

                if (
                    password.value ===
                    confirmation.value
                ) {

                    confirmation.classList.remove(
                        'is-invalid'
                    );

                    const error =
                        confirmation.parentElement.querySelector(
                            '.password-match-error'
                        );

                    if (error) {
                        error.remove();
                    }

                }

            }
        );

    });

</script>

</body>

</html>