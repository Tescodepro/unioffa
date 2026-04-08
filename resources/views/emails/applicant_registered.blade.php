@extends('emails.layouts.email_layout')

@section('title', 'Your Application Number')

@section('content')
    <h2 style="color: #42e13d; margin-top: 0; font-size: 20px; font-weight: 600;">Hello {{ $user->first_name }},</h2>

    <p style="color: #4a5568; font-size: 16px; line-height: 1.6;">
        Thank you for registering on the <strong>{{ config('app.name') }} Application Portal</strong>.
    </p>

    <div style="background-color: #f8fafc; border-radius: 6px; padding: 20px; text-align: center; margin: 25px 0; border: 1px dashed #42e13d;">
        <p style="color: #718096; font-size: 14px; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px;">Your Application Number</p>
        <h3 style="color: #42e13d; margin: 0; font-size: 32px; font-weight: 800; letter-spacing: 2px;">{{ $applicationNumber }}</h3>
    </div>

    <p style="color: #4a5568; font-size: 16px; line-height: 1.6;">
        Please keep this number safe. You will need it to log in and track your application progress.
    </p>
@endsection

@section('footer')
    <div style="color: #718096; font-size: 14px; line-height: 1.5;">
        Best regards,<br>
        <strong>{{ config('app.name') }} Admissions Team</strong>
    </div>
@endsection
