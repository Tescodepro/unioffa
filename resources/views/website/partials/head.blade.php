<head>
    <!-- meta tags -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @if(isset($news) && $news instanceof \App\Models\News)
        <meta name="description" content="{{ $news->short_title ?? \Illuminate\Support\Str::limit(strip_tags(html_entity_decode(html_entity_decode($news->content))), 150) }}">
        
        <!-- Open Graph / Facebook / WhatsApp -->
        <meta property="og:type" content="article">
        <meta property="og:title" content="{{ $news->title }}">
        <meta property="og:description" content="{{ $news->short_title ?? \Illuminate\Support\Str::limit(strip_tags(html_entity_decode(html_entity_decode($news->content))), 150) }}">
        <meta property="og:image" content="{{ asset('storage/' . $news->image) }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:site_name" content="{{ config('app.name') }}">
        
        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $news->title }}">
        <meta name="twitter:description" content="{{ $news->short_title ?? \Illuminate\Support\Str::limit(strip_tags(html_entity_decode(html_entity_decode($news->content))), 150) }}">
        <meta name="twitter:image" content="{{ asset('storage/' . $news->image) }}">
    @else
        <meta name="description" content="Welcome to {{ config('app.name') }}">
        
        <!-- Open Graph / Facebook / WhatsApp -->
        <meta property="og:type" content="website">
        <meta property="og:title" content="{{ $title ?? config('app.name') }}">
        <meta property="og:description" content="Welcome to {{ config('app.name') }}">
        <meta property="og:image" content="{{ asset('assets/img/logo/logo.jpeg') }}">
        <meta property="og:url" content="{{ url()->current() }}">
        
        <!-- Twitter -->
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="{{ $title ?? config('app.name') }}">
        <meta name="twitter:description" content="Welcome to {{ config('app.name') }}">
        <meta name="twitter:image" content="{{ asset('assets/img/logo/logo.jpeg') }}">
    @endif
    <meta name="keywords" content="">
    <!-- title -->
    <title> {{$title}} &amp;  {{ config('app.name') }}   </title>
    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/logo/logo.jpeg') }}">

<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/all-fontawesome.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/animate.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/magnific-popup.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/owl.carousel.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

</head>