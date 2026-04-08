@extends('emails.layouts.email_layout')

@section('title', $content['title'] ?? 'Notification')

@section('content')
    <h2 style="color: #42e13d; margin-top: 0; font-size: 20px; font-weight: 600;">{{ $content['title'] ?? '' }}</h2>

    <div style="color: #4a5568; font-size: 16px; line-height: 1.6;">
        {!! $content['body'] !!}
    </div>
@endsection

@section('footer')
    @if(!empty($content['footer']))
        <div style="color: #718096; font-size: 14px; line-height: 1.5;">
            {!! $content['footer'] !!}
        </div>
    @endif
@endsection