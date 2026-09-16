<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? 'Dashboard' }} - InviteCraft</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>
    <div class="dashboard-shell">
        <aside class="dashboard-sidebar" id="dashboardSidebar">
            <a class="dashboard-logo" href="{{ route('dashboard') }}">
                <span>IC</span>
                <strong>InviteCraft</strong>
            </a>
            <nav class="dashboard-nav" aria-label="Dashboard navigation">
                <a class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                <a class="{{ request()->routeIs('dashboard.invitations') ? 'active' : '' }}" href="{{ route('dashboard.invitations') }}">My Invitations</a>
                <a class="{{ request()->routeIs('dashboard.create') ? 'active' : '' }}" href="{{ route('dashboard.create') }}">Create New</a>
                <a class="{{ request()->routeIs('dashboard.profile') ? 'active' : '' }}" href="{{ route('dashboard.profile') }}">Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </nav>
        </aside>

        <div class="dashboard-main">
            <header class="dashboard-header">
                <button class="sidebar-toggle" type="button" aria-label="Toggle menu" data-sidebar-toggle>Menu</button>
                <h1>@yield('title', $pageTitle ?? 'Dashboard')</h1>
                <div class="header-user">
                    <span>{{ auth()->user()->name }}</span>
                    <b>{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</b>
                </div>
            </header>

            <main class="dashboard-content">
                @if (session('status'))
                    <div class="flash-message">{{ session('status') }}</div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        document.querySelector('[data-sidebar-toggle]')?.addEventListener('click', function () {
            document.getElementById('dashboardSidebar')?.classList.toggle('open');
        });
    </script>
    @stack('scripts')
</body>
</html>

