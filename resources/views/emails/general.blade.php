<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $content['title'] ?? 'Notification' }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0;">

    <div style="max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #ffffff;">

        <div style="text-align: center; padding-bottom: 15px; border-bottom: 2px solid #00b324;">
            <h1 style="color: #00b324; margin: 0; font-size: 24px;">University of Offa</h1>
        </div>

        <div style="padding: 20px 0;">
            <h5 style="color: #00b324; margin-top: 0; font-size: 18px;">{{ $content['title'] ?? '' }}</h5>
            
            <div style="margin-bottom: 20px;">
                {!! $content['body'] !!}
            </div>
        </div>

        @if(!empty($content['footer']))
            <hr style="border: 0; height: 1px; background: #eee; margin: 15px 0;">
            {!! $content['footer'] !!}
        @endif
        
        <p style="font-size: 10px; color: #000000; text-align: center; margin-top: 15px;">
            This email was sent by University Of Offa. Please do not reply to this message.
        </p>
    </div>

</body>
</html>