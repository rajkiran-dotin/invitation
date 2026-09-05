<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - InviteCraft</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="site-shell">
        <header class="topbar" id="home">
            <a href="{{ route('home') }}" class="brand" aria-label="InviteCraft home">
                <span class="brand-mark">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 8.5 12 3l8 5.5v11H4v-11Zm0 0 8 6 8-6"/></svg>
                </span>
                <span><strong>InviteCraft</strong><small>Make Every Moment Memorable</small></span>
            </a>
            <div class="nav-actions">
                <a class="btn btn-light" href="{{ route('home') }}">Home</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-primary" type="submit">Logout</button>
                </form>
            </div>
        </header>

        <main>
            <section class="section customer-dashboard">
                <p class="section-kicker">Your account</p>
                <h1>Welcome, {{ auth()->user()->name }}</h1>
                <p>Manage your InviteCraft account and start creating beautiful digital invitations.</p>
                <a class="btn btn-primary btn-lg" href="{{ route('home') }}#pricing">Create Invitation</a>
            </section>
        </main>
    </div>
</body>
</html>
