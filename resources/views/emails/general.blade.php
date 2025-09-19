<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $content['title'] ?? 'Notification' }}</title>
</head>
<body>
    <h5>{{ $content['title'] ?? '' }}</h5>
    <p>{{ $content['body'] ?? '' }}</p>

    @if(!empty($content['footer']))
        <hr>
        <p>{{ $content['footer'] }}</p>
    @endif
</body>
</html>
