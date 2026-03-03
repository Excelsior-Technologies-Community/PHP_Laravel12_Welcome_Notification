#  PHP_Laravel12_Welcome_Notification

![Laravel](https://img.shields.io/badge/Laravel-12-red)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue)
![Package](https://img.shields.io/badge/Spatie-Welcome%20Notification-green)

---

##  Overview

This project demonstrates how to integrate **Spatie Welcome Notification** in a Laravel 12 application.

When a new user is created, the system sends a secure welcome email with a temporary activation link. The user can set their password using that link and activate their account.

This project includes:

* User creation route
* Welcome email sending
* Custom welcome controller
* Custom welcome page UI
* Gmail SMTP configuration example

---

##  Features

*  Laravel 12 setup
*  Spatie Welcome Notification integration
*  Secure password activation link
*  Custom Welcome Controller
*  Custom Welcome Blade UI
*  Gmail SMTP configuration

---

##  Folder Structure

```
app/
 ├── Http/
 │    └── Controllers/
 │         └── Auth/
 │              └── MyWelcomeController.php
 │
 ├── Models/
 │    └── User.php
 │
routes/
 └── web.php

resources/
 └── views/
      ├── success.blade.php
      └── vendor/
           └── welcome-notification/
                └── welcome.blade.php
```

---


## 1. Project Installation

### Step 1 – Create New Laravel Project

```bash
composer create-project laravel/laravel laravel-welcome-app
```

### Step 2 – Run Server (Optional Check)

```bash
php artisan serve
```

Open:

```
http://127.0.0.1:8000
```

---

## 2. Database Configuration

Open `.env` file and configure:

```env
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

Run Migration:

```bash
php artisan migrate
```

---

## 3. Install Spatie Welcome Notification Package

```bash
composer require spatie/laravel-welcome-notification
```

### Publish Migrations

```bash
php artisan vendor:publish --provider="Spatie\WelcomeNotification\WelcomeNotificationServiceProvider" --tag="migrations"
```

### Publish Views

```bash
php artisan vendor:publish --provider="Spatie\WelcomeNotification\WelcomeNotificationServiceProvider" --tag="views"
```

### Run Migration Again

```bash
php artisan migrate
```

This adds:

```
welcome_valid_until
```

column in users table.

---

## 4. Mail Configuration (Gmail Example)

Open `.env`

```env
MAIL_MAILER=smtp 
MAIL_HOST=smtp.gmail.com 
MAIL_PORT=587 
MAIL_USERNAME=yourgmail@gmail.com 
MAIL_PASSWORD=your_16_char_app_password 
MAIL_ENCRYPTION=tls 
MAIL_FROM_ADDRESS=yourgmail@gmail.com 
MAIL_FROM_NAME="Welcome App" 
 
SESSION_DRIVER=file 
QUEUE_CONNECTION=sync
```

⚠ Use Gmail App Password (not normal password).

Clear config:

```bash
php artisan config:clear

php artisan cache:clear
```

---

## 5. Update User Model

**File:** `app/Models/User.php`

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\WelcomeNotification\ReceivesWelcomeNotification;

class User extends Authenticatable
{
    use Notifiable, ReceivesWelcomeNotification;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
```

---

## 6. Add Routes

**File:** `routes/web.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use Spatie\WelcomeNotification\WelcomesNewUsers;
use App\Http\Controllers\Auth\MyWelcomeController;

/*
|--------------------------------------------------------------------------
| Test Route To Create User & Send Welcome Email
|--------------------------------------------------------------------------
*/

Route::get('/create-user', function () {

    $user = \App\Models\User::create([
        'name' => 'Harry',
        'email' => 'Your_email@gmail.com',
        'password' => bcrypt('temporary123'),
    ]);

    $user->sendWelcomeNotification(now()->addDay());

    return view('success'); 
});

/*
|--------------------------------------------------------------------------
| Welcome Routes (IMPORTANT)
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => ['web', WelcomesNewUsers::class]], function () {

    Route::get('welcome/{user}', [MyWelcomeController::class, 'showWelcomeForm'])
        ->name('welcome');

    Route::post('welcome/{user}', [MyWelcomeController::class, 'savePassword']);
});
```

---

## 7. Controller

Create Controller:

```bash
php artisan make:controller Auth/MyWelcomeController
```

Edit:
`app/Http/Controllers/Auth/MyWelcomeController.php`

```php
<?php

namespace App\Http\Controllers\Auth;

use Spatie\WelcomeNotification\WelcomeController;
use Symfony\Component\HttpFoundation\Response;

class MyWelcomeController extends WelcomeController
{
    protected function sendPasswordSavedResponse(): Response
    {
        return redirect('/login')
            ->with('success', 'Password set successfully. You can login now!');
    }
}
```

---

## 8. Create Success Page

**File:** `resources/views/success.blade.php`

```html
<!DOCTYPE html>
<html>
<head>
    <title>Success</title>
    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #4e73df, #1cc88a);
            font-family: Arial, Helvetica, sans-serif;
        }
        .card {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.2);
            text-align: center;
            width: 400px;
        }
        h2 {
            color: #28a745;
        }
        p {
            color: #555;
        }
    </style>
</head>
<body>

<div class="card">
    <h2> User Created!</h2>
    <p>Welcome email sent successfully.</p>
    <p>Please check your inbox.</p>
</div>

</body>
</html>
```

---

## 9. Welcome Page

**File:** `resources/views/vendor/welcome-notification/welcome.blade.php`

```html
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

        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="password_confirmation" placeholder="Confirm Password" required>

        <button type="submit">Activate Account</button>
    </form>
</div>

</body>
</html>
```

---

## 10. Testing

### Step 1 – Run Server

```bash
php artisan serve
```

### Step 2 – Open

```
http://127.0.0.1:8000/create-user
```
<img width="531" height="255" alt="Screenshot 2026-03-03 122633" src="https://github.com/user-attachments/assets/20c16562-865b-4d77-8e85-4113a989832d" />

---
### Step 3 – Check email inbox

<img width="1607" height="822" alt="Screenshot 2026-03-03 122655" src="https://github.com/user-attachments/assets/a75a2bfe-c987-4d44-803a-be3ec0262803" />

---
### Step 4 – Welcome Page

<img width="563" height="359" alt="Screenshot 2026-03-03 134658" src="https://github.com/user-attachments/assets/e8bcc682-5962-4f78-baac-6fa0c186f04f" />

---
