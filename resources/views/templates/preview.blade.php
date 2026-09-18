<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $template->name }} Live Preview - InviteCraft</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="template-preview-body">
    <div class="template-preview-bar">
        <div>
            <span>InviteCraft Preview</span>
            <strong>{{ $template->name }}</strong>
        </div>
        <nav aria-label="Preview actions">
            <a class="btn btn-light" href="{{ route('templates.show', $template->slug) }}">Back to Template</a>
            <a class="btn btn-primary" href="{{ route('templates.use', $template->slug) }}">Use This Template</a>
        </nav>
    </div>

    <div class="template-live-preview-shell">
        @include($publicView, ['invitation' => $invitation, 'isPreview' => true])
    </div>
</body>
</html>
