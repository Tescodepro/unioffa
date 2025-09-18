<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your Application Number</title>
</head>
<body>
    <h2>Hello {{ $user->first_name }},</h2>

    <p>Thank you for registering on the <strong>{{ config('app.name') }} Application Portal</strong>.</p>

    <p>Your <strong>Application Number</strong> is:</p>
    <h3 style="color: #2d3748;">{{ $applicationNumber }}</h3>

    <p>
        Please keep this number safe.  
        You will need it to log in and track your application progress.
    </p>

    <p>Best regards,<br>
    {{ config('app.name') }} Admissions Team</p>
</body>
</html>
