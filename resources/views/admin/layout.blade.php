<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - InviteCraft</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-body">
    <aside class="admin-sidebar">
        <a href="{{ route('admin.dashboard') }}" class="brand">
            <span class="brand-mark"><svg viewBox="0 0 24 24"><path d="M4 8.5 12 3l8 5.5v11H4v-11Zm0 0 8 6 8-6"/></svg></span>
            <span><strong>InviteCraft</strong><small>Admin Panel</small></span>
        </a>
        <nav>
            <a href="{{ route('admin.dashboard') }}" @class(['active' => request()->routeIs('admin.dashboard')])>Dashboard</a>
            <a href="{{ route('admin.users.index') }}" @class(['active' => request()->routeIs('admin.users.*')])>New Users</a>
            <a href="{{ route('admin.enquiries.index') }}" @class(['active' => request()->routeIs('admin.enquiries.*')])>Enquiries</a>
            <a href="{{ route('admin.templates.index') }}" @class(['active' => request()->routeIs('admin.templates.*')])>Templates</a>
            <a href="{{ route('admin.template-categories.index') }}" @class(['active' => request()->routeIs('admin.template-categories.*')])>Template Categories</a>
            <a href="{{ route('admin.plans.index') }}" @class(['active' => request()->routeIs('admin.plans.*')])>Plans</a>
            <a href="{{ route('home') }}">View Website</a>
        </nav>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </aside>

    <main class="admin-main">
        <header class="admin-header">
            <div>
                <p>InviteCraft Backend</p>
                <h1>@yield('title', 'Dashboard')</h1>
            </div>
            <span>{{ auth()->user()->name }}</span>
        </header>

        @if (session('status'))
            <div class="admin-alert">{{ session('status') }}</div>
        @endif

        @yield('content')
    </main>
    @stack('scripts')
</body>
</html>
