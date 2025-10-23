<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Session Expired</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
        $path = request()->path();
        if (str_contains($path, 'admission') || str_contains($path, 'application')) {
            $redirectUrl = route('application.login');
        } elseif (str_contains($path, 'student') || str_contains($path, 'students')) {
            $redirectUrl = route('student.login');
        } elseif (str_contains($path, 'staff') || str_contains($path, 'burser') || str_contains($path, 'bursary') || str_contains($path, 'ict') || str_contains($path, 'admin')) {
            $redirectUrl = route('staff.login');
        } else {
            $redirectUrl = route('home');
        }
    @endphp

    <meta http-equiv="refresh" content="4;url={{ $redirectUrl }}">

    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            padding: 2rem 3rem;
            text-align: center;
            max-width: 420px;
        }
        h1 {
            color: #1e293b;
            margin-bottom: 0.75rem;
        }
        p {
            color: #475569;
            margin-bottom: 1.5rem;
        }
        a.button {
            display: inline-block;
            background-color: #2563eb;
            color: #fff;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: background 0.2s ease;
        }
        a.button:hover {
            background-color: #1e40af;
        }
        .small {
            font-size: 0.85rem;
            color: #64748b;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Session Expired</h1>
        <p>Your session has expired or you’ve been inactive for a while.<br>
           Redirecting you to the login page...</p>

        <a href="{{ $redirectUrl }}" class="button">Go to Login</a>

        <div class="small">(If you’re not redirected automatically, click above)</div>
    </div>
</body>
</html>
