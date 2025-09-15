<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    <title>{{ $title ?? 'EssyCoff' }}</title>
</head>
<body class="min-h-screen bg-transparent">
    {{ $slot }}

    @fluxScripts
</body>
</html>
