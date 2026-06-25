<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#faf6f0">
    <title>{{ $title }} — Links</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css'])
</head>
<body class="visit-page">
    <main class="visit-page__main">
        <div class="visit-page__card">
            <header class="visit-page__header">
                <img src="{{ $logoUrl }}" alt="{{ $title }}" class="visit-page__logo" width="134" height="134">
                <h1 class="visit-page__title">{{ $title }}</h1>
                @if($subtitle !== '')
                    <p class="visit-page__subtitle">{{ $subtitle }}</p>
                @endif
            </header>

            @if(count($links) === 0)
                <p class="visit-page__empty">No links configured yet.</p>
            @else
                <nav class="visit-page__links" aria-label="Quick links">
                    @foreach($links as $link)
                        <a href="{{ $link['url'] }}"
                           class="visit-page__link"
                           target="_blank"
                           rel="noopener noreferrer"
                           style="background: linear-gradient(135deg, {{ $link['color_from'] }} 0%, {{ $link['color_to'] }} 100%);">
                            <span class="visit-page__link-icon" aria-hidden="true">
                                <i class="{{ $link['icon'] }}"></i>
                            </span>
                            <span class="visit-page__link-text">
                                <span class="visit-page__link-title">{{ $link['title'] }}</span>
                                @if($link['subtitle'] !== '')
                                    <span class="visit-page__link-desc">{{ $link['subtitle'] }}</span>
                                @endif
                            </span>
                            <span class="visit-page__link-arrow" aria-hidden="true">
                                <i class="fas fa-arrow-up-right-from-square"></i>
                            </span>
                        </a>
                    @endforeach
                </nav>
            @endif
        </div>
    </main>
</body>
</html>
