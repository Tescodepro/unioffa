<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Notification')</title>
</head>

<body style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f7f6;">

    <div style="max-width: 600px; margin: 20px auto; padding: 0; border-radius: 8px; overflow: hidden; background-color: #ffffff; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border: 1px solid #e1e8ed;">

        <!-- Header with School Color -->
        <div style="background-color: #42e13d; padding: 30px 20px; text-align: center;">
            <img src="{{ url('portal_assets/img/logo/logo.jpeg') }}" alt="{{ \App\Models\SystemSetting::get('school_name', 'University of Offa') }}" style="max-height: 80px; margin-bottom: 10px; border-radius: 4px;">
            <h1 style="color: #ffffff; margin: 0; font-size: 22px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
                {{ \App\Models\SystemSetting::get('school_name', 'University of Offa') }}
            </h1>
        </div>

        <!-- Content Area -->
        <div style="padding: 30px 40px;">
            @yield('content')
        </div>

        <!-- Footer Area -->
        <div style="background-color: #f9fafb; padding: 20px 40px; border-top: 1px solid #edf2f7; text-align: center;">
            <div style="margin-bottom: 15px;">
                @yield('footer')
            </div>
            
            <p style="font-size: 11px; color: #718096; margin: 0;">
                &copy; {{ date('Y') }} {{ \App\Models\SystemSetting::get('school_name', 'University of Offa') }}. All rights reserved.
            </p>
            <p style="font-size: 10px; color: #a0aec0; margin-top: 10px;">
                This is an automated notification. Please do not reply to this email.
            </p>
        </div>
    </div>

</body>

</html>
