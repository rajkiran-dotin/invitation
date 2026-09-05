<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - InviteCraft</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-login-page">
    <form class="admin-login-card" method="POST" action="{{ route('admin.login.store') }}">
        @csrf
        <a href="{{ route('home') }}" class="brand">
            <span class="brand-mark"><svg viewBox="0 0 24 24"><path d="M4 8.5 12 3l8 5.5v11H4v-11Zm0 0 8 6 8-6"/></svg></span>
            <span><strong>InviteCraft</strong><small>Admin Panel</small></span>
        </a>
        <h1>Login to Admin</h1>
        <label>Email<input type="email" name="email" value="{{ old('email') }}" required autofocus></label>
        <label>Password<input type="password" name="password" placeholder="password" required></label>
        <label class="check-row"><input type="checkbox" name="remember" value="1"> Remember me</label>
        @error('email')<p class="form-error">{{ $message }}</p>@enderror
        @error('password')<p class="form-error">{{ $message }}</p>@enderror
        <button type="submit" class="btn btn-primary">Login</button>
        <p class="hint"><a href="#">Forgot Password?</a></p>
    </form>
</body>
</html>
