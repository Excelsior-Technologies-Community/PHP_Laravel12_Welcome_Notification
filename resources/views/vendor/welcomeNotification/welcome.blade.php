<!DOCTYPE html>
<html>
<head>
    <title>Set Password</title>
    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #4e73df, #1cc88a);
            font-family: Arial;
        }
        .card {
            background: white;
            padding: 35px;
            border-radius: 12px;
            width: 400px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.2);
        }
        h2 {
            text-align: center;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 6px;
            border: 1px solid #ddd;
        }
        button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            border: none;
            border-radius: 6px;
            background: #4e73df;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>Welcome {{ $user->name }}</h2>

    <form method="POST" action="{{ route('welcome', $user) }}">
        @csrf

        <input type="password" name="password" placeholder="Enter New Password" required>
        <input type="password" name="password_confirmation" placeholder="Confirm Password" required>

        <button type="submit">Activate Account</button>
    </form>
</div>

</body>
</html>