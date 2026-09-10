@php
    $icons = [
        'arrow' => '<path d="M5 12h14m-5-5 5 5-5 5"/>',
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $template->name }} - InviteCraft</title>
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
            <section class="template-detail section">
                <div class="template-detail-preview">
                    @if ($template->preview_image)
                        <img src="{{ $template->preview_image }}" alt="{{ $template->name }} preview">
                    @else
                        <div class="template-preview {{ $template->theme_class }}">
                            <small>{{ $template->templateCategory?->name ?? $template->category }}</small>
                            <h3>Rahul<br>&<br>Priya</h3>
                            <span>25 DEC 2036</span>
                        </div>
                    @endif
                </div>
                <div class="template-detail-copy">
                    <a class="template-back-link" href="{{ route('templates.index') }}"><svg viewBox="0 0 24 24">{!! $icons['arrow'] !!}</svg> Back to templates</a>
                    <p class="section-kicker">{{ $template->templateCategory?->name ?? $template->category }}</p>
                    <h1>{{ $template->name }}</h1>
                    <div class="market-template-meta detail-meta">
                        <span>{{ $template->templateCategory?->name ?? $template->category }}</span>
                        <b>{{ $template->is_premium ? 'Premium' : 'Free' }}</b>
                    </div>
                    <p>{{ $template->description ?: 'Beautiful digital invitation template for your special celebration.' }}</p>
                    @if (! empty($template->features))
                        <ul class="template-feature-list">
                            @foreach ($template->features as $feature)
                                <li>{{ $feature }}</li>
                            @endforeach
                        </ul>
                    @endif
                    <div class="template-detail-actions">
                        <a class="btn btn-light" href="{{ $template->demo_url ?: route('templates.show', $template->slug) }}">Preview Live</a>
                        <a class="btn btn-primary" href="{{ route('templates.use', $template->slug) }}">Use This Template</a>
                    </div>
                </div>
            </section>

            @if ($relatedTemplates->isNotEmpty())
                <section class="section related-templates">
                    <div class="section-head">
                        <div>
                            <p class="section-kicker">More like this</p>
                            <h2>Related Templates</h2>
                        </div>
                    </div>
                    <div class="market-template-grid compact">
                        @foreach ($relatedTemplates as $relatedTemplate)
                            <article class="market-template-card">
                                <a class="market-template-image" href="{{ route('templates.show', $relatedTemplate->slug) }}">
                                    @if ($relatedTemplate->preview_image)
                                        <img src="{{ $relatedTemplate->preview_image }}" alt="{{ $relatedTemplate->name }} preview" loading="lazy">
                                    @else
                                        <div class="template-preview {{ $relatedTemplate->theme_class }}"></div>
                                    @endif
                                </a>
                                <div class="market-template-body">
                                    <div class="market-template-meta"><span>{{ $relatedTemplate->templateCategory?->name }}</span><b>{{ $relatedTemplate->is_premium ? 'Premium' : 'Free' }}</b></div>
                                    <h2><a href="{{ route('templates.show', $relatedTemplate->slug) }}">{{ $relatedTemplate->name }}</a></h2>
                                    <p>{{ $relatedTemplate->description }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif
        </main>
    </div>
</body>
</html>