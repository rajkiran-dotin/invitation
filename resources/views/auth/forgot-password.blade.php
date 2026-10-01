<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password - InviteCraft</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-login-page">
    <form class="admin-login-card" method="POST" action="{{ route('password.email') }}">
        @csrf
        <a href="{{ route('home') }}" class="brand">
            <span class="brand-mark"><svg viewBox="0 0 24 24"><path d="M4 8.5 12 3l8 5.5v11H4v-11Zm0 0 8 6 8-6"/></svg></span>
            <span><strong>InviteCraft</strong><small>Make Every Moment Memorable</small></span>
        </a>
        <h1>Forgot Password</h1>
        <p class="hint">Enter your email and we will send you a password reset link.</p>
        <label>Email Address<input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"></label>
        @if (session('status'))<p class="form-status">{{ session('status') }}</p>@endif
        @error('email')<p class="form-error">{{ $message }}</p>@enderror
        <button type="submit" class="btn btn-primary">Send Password Reset Link</button>
        <p class="auth-switch">Remembered your password? <a href="{{ route('login') }}">Login</a></p>
    </form>
</body>
</html>
