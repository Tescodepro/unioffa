<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Page Not Found</title>
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

    <meta http-equiv="refresh" content="6;url={{ $redirectUrl }}">

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
            max-width: 440px;
        }
        h1 {
            font-size: 2.25rem;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }
        p {
            color: #475569;
            margin-bottom: 1.75rem;
            line-height: 1.5;
        }
        a.button {
            display: inline-block;
            background-color: #10bf68;
            color: #fff;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: background 0.2s ease;
        }
        a.button:hover {
            background-color: #19822b;
        }
        .small {
            font-size: 0.85rem;
            color: #64748b;
            margin-top: 1rem;
        }
        .code {
            font-weight: bold;
            color: #ef4444;
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="code">Error 404</div>
        <h1>Page Not Found</h1>
        <p>The page you’re looking for doesn’t exist or may have been moved.<br>
           Redirecting you shortly...</p>

        <a href="{{ $redirectUrl }}" class="button">Go Back</a>

        <div class="small">(If you’re not redirected automatically, click above)</div>
    </div>
</body>
</html>
