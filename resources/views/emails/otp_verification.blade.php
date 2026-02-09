@extends('email') <!-- Extends your base email layout -->

@section('email-body')
    <h2 style="text-align: center; color: #333;">Your OTP Code</h2>
    <p style="text-align: center; font-size: 16px; color: #555;">
        Your One-Time Password (OTP) for login is:
    </p>

    <div style="text-align: center; margin: 20px 0;">
        <span style="display: inline-block; padding: 10px 20px; background-color: #f0f0f0; border-radius: 5px; font-size: 24px; font-weight: bold; color: #333;">
            {{ $otp }}
        </span>
    </div>

    <p style="text-align: center; font-size: 16px; color: #555;">
        This OTP will expire in {{ $expiration_time ?? 2 }} minutes. If you did not request this OTP, please contact support.
    </p>

    <p style="text-align: center; font-size: 14px; color: #999;">
        Thank you for using our services.
    </p>
@endsection
