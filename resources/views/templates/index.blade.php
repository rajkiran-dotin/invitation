@php
    $icons = [
        'arrow' => '<path d="M5 12h14m-5-5 5 5-5 5"/>',
        'search' => '<path d="m21 21-4.3-4.3M10.8 18a7.2 7.2 0 1 1 0-14.4 7.2 7.2 0 0 1 0 14.4Z"/>',
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Templates Library - InviteCraft</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="site-shell">
        <header class="topbar" id="home">
            <a href="{{ route('home') }}" class="brand" aria-label="InviteCraft home">
                <span class="brand-mark"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 8.5 12 3l8 5.5v11H4v-11Zm0 0 8 6 8-6"/></svg></span>
                <span><strong>InviteCraft</strong><small>Make Every Moment Memorable</small></span>
            </a>
            <nav class="nav-links" aria-label="Primary navigation">
                <a href="{{ route('home') }}">Home</a>
                <a class="active" href="{{ route('templates.index') }}">Templates</a>
                <a href="{{ route('home') }}#pricing">Pricing</a>
                <a href="{{ route('home') }}#process">How It Works</a>
                <a href="{{ route('home') }}#features">Features</a>
            </nav>
            <div class="nav-actions">
                @auth
                    <a class="btn btn-outline" href="{{ route('dashboard') }}">Dashboard</a>
                @else
                    <a class="btn btn-outline" href="{{ route('login') }}">Login</a>
                    <a class="btn btn-primary" href="{{ route('register') }}">Get Started</a>
                @endauth
            </div>
        </header>

        <main>
            <section class="templates-market section">
                <div class="templates-market-hero">
                    <p class="section-kicker">Templates library</p>
                    <h1>Find the Perfect Invitation Template</h1>
                    <p>Explore beautifully designed templates for weddings, birthdays, engagements and special celebrations.</p>
                </div>

                <form class="templates-toolbar" method="GET" action="{{ route('templates.index') }}">
                    @if ($selectedCategory)
                        <input type="hidden" name="category" value="{{ $selectedCategory->slug }}">
                    @endif
                    <label class="templates-search">
                        <span><svg viewBox="0 0 24 24">{!! $icons['search'] !!}</svg></span>
                        <input name="search" value="{{ $search }}" placeholder="Search templates...">
                    </label>
                    <label class="templates-sort">
                        <span>Sort by</span>
                        <select name="sort" onchange="this.form.submit()">
                            <option value="" @selected($sort === '')>Featured</option>
                            <option value="newest" @selected($sort === 'newest')>Newest</option>
                            <option value="name" @selected($sort === 'name')>Name A-Z</option>
                        </select>
                    </label>
                    <button class="btn btn-primary" type="submit">Search</button>
                </form>

                <nav class="template-category-pills" aria-label="Template categories">
                    <a @class(['active' => ! $selectedCategory]) href="{{ route('templates.index', array_filter(['search' => $search, 'sort' => $sort])) }}">All</a>
                    @foreach ($categories as $category)
                        <a @class(['active' => $selectedCategory?->is($category)]) href="{{ route('templates.index', array_filter(['category' => $category->slug, 'search' => $search, 'sort' => $sort])) }}">{{ $category->name }}</a>
                    @endforeach
                </nav>

                <div class="market-template-grid">
                    @forelse ($templates as $template)
                        <article class="market-template-card">
                            <a class="market-template-image" href="{{ route('templates.show', $template->slug) }}" aria-label="Preview {{ $template->name }}">
                                @if ($template->preview_image)
                                    <img src="{{ $template->preview_image }}" alt="{{ $template->name }} preview" loading="lazy">
                                @else
                                    <div class="template-preview {{ $template->theme_class }}">
                                        <small>{{ $template->templateCategory?->name ?? $template->category }}</small>
                                        <h3>Rahul<br>&<br>Priya</h3>
                                        <span>25 DEC 2036</span>
                                    </div>
                                @endif
                            </a>
                            <div class="market-template-body">
                                <div class="market-template-meta">
                                    <span>{{ $template->templateCategory?->name ?? $template->category }}</span>
                                    <b>{{ $template->is_premium ? 'Premium' : 'Free' }}</b>
                                </div>
                                <h2><a href="{{ route('templates.show', $template->slug) }}">{{ $template->name }}</a></h2>
                                <p>{{ $template->description ?: ucfirst($template->theme_class).' invitation template.' }}</p>
                                <div class="market-template-actions">
                                    <a class="btn btn-light" href="{{ route('templates.show', $template->slug) }}">Preview</a>
                                    <a class="btn btn-primary" href="{{ route('templates.use', $template->slug) }}">Use Template</a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="template-empty">
                            <strong>No templates found</strong>
                            <span>Try another category or search term.</span>
                        </div>
                    @endforelse
                </div>

                <div class="templates-pagination">
                    {{ $templates->links() }}
                </div>
            </section>
        </main>
    </div>
</body>
</html>