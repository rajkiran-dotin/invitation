<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - InviteCraft</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-login-page">
    <form class="admin-login-card" method="POST" action="{{ route('login.store') }}">
        @csrf
        <a href="{{ route('home') }}" class="brand">
            <span class="brand-mark"><svg viewBox="0 0 24 24"><path d="M4 8.5 12 3l8 5.5v11H4v-11Zm0 0 8 6 8-6"/></svg></span>
            <span><strong>InviteCraft</strong><small>Make Every Moment Memorable</small></span>
        </a>
        <h1>Login to InviteCraft</h1>
        <label>Email<input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"></label>
        <label>Password<input type="password" name="password" required autocomplete="current-password"></label>
        <label class="check-row"><input type="checkbox" name="remember" value="1"> Remember me</label>
        @if (session('status'))<p class="form-status">{{ session('status') }}</p>@endif
        @error('email')<p class="form-error">{{ $message }}</p>@enderror
        @error('password')<p class="form-error">{{ $message }}</p>@enderror
        <button type="submit" class="btn btn-primary">Login</button>
        <p class="hint"><a href="{{ route('password.request') }}">Forgot Password?</a></p>

        <div class="auth-divider"><span>OR</span></div>

        <div class="social-auth">
            <a class="social-button" href="{{ route('auth.google.redirect') }}" aria-label="Continue with Google">
                <svg class="social-icon" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill="#4285F4" d="M22.6 12.23c0-.78-.07-1.53-.2-2.23H12v4.22h5.94a5.08 5.08 0 0 1-2.2 3.33v2.72h3.56c2.08-1.9 3.3-4.7 3.3-8.04Z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.97 7.28-2.64l-3.56-2.72c-.98.65-2.24 1.03-3.72 1.03-2.86 0-5.29-1.9-6.16-4.48H2.18v2.8A11 11 0 0 0 12 23Z"/>
                    <path fill="#FBBC05" d="M5.84 14.2a6.55 6.55 0 0 1 0-4.18V7.2H2.18a11 11 0 0 0 0 9.78l3.66-2.78Z"/>
                    <path fill="#EA4335" d="M12 5.44c1.62 0 3.07.55 4.22 1.63l3.15-3.1A10.63 10.63 0 0 0 12 1 11 11 0 0 0 2.18 7.2l3.66 2.82C6.71 7.34 9.14 5.44 12 5.44Z"/>
                </svg>
                Continue with Google
            </a>
        </div>

        <p class="auth-switch">New to InviteCraft? <a href="{{ route('register') }}">Create Account</a></p>
    </form>
</body>
</html>
