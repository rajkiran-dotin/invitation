<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $invitation->bride_name }} & {{ $invitation->groom_name }} - InviteCraft</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body class="preview-body">
    @include($publicView, ['invitation' => $invitation])
</body>
</html>
