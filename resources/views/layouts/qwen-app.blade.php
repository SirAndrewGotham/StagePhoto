<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'StagePhoto.ru — Концертная и театральная фотография' }}</title>
    <meta name="description" content="{{ $description ?? 'Профессиональная фотография концертов и театральных постановок в России' }}">

    {{-- ✅ Vite: Single source of truth for CSS/JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- ✅ Livewire 4 Styles (required) --}}
    @livewireStyles

    {{-- ✅ Preload critical fonts (optional but recommended) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- ✅ Favicon --}}
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/icon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    {{-- ✅ Open Graph / Twitter Cards --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="StagePhoto.ru">
    <meta property="og:title" content="{{ $title ?? 'StagePhoto.ru' }}">
    <meta property="og:description" content="{{ $description ?? 'Концертная и театральная фотография' }}">
    <meta property="og:image" content="{{ $ogImage ?? '/og-image.jpg' }}">
    <meta name="twitter:card" content="summary_large_image">

    {{-- ✅ Structured Data (JSON-LD) --}}
    <script type="application/ld+json">
        {
          "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "StagePhoto.ru",
    "url": "https://stagephoto.ru",
    "inLanguage": "ru",
    "potentialAction": {
      "@type": "SearchAction",
      "target": "https://stagephoto.ru/search?q={search_term_string}",
      "query-input": "required name=search_term_string"
    }
  }
    </script>

    {{-- ✅ Translation Data for Alpine t() helper --}}
    <script>
        window.translations = @json([
      // Header / Nav
      'platform' => __('Platform'),
      'submitWorkLink' => __('Submit your work'),
      'forBands' => __('For bands'),
      'forTheaters' => __('For theaters'),
      'photographerGuide' => __('Photographer guide'),

      // Footer Sections
      'community' => __('Community'),
      'featuredArtists' => __('Featured artists'),
      'monthlyContest' => __('Monthly contest'),
      'workshops' => __('Workshops'),
      'blog' => __('Blog'),

      'legal' => __('Legal'),
      'privacyPolicy' => __('Privacy policy'),
      'termsOfService' => __('Terms of service'),
      'copyright' => __('Copyright'),
      'cookieSettings' => __('Cookie settings'),

      'connect' => __('Connect'),
      'telegram' => __('Telegram'),
      'vkontakte' => __('VKontakte'),
      'instagram' => __('Instagram'),
      'emailSupport' => __('Email support'),

      'madeIn' => __('Made in'),
      'light' => __('Light'),
      'dark' => __('Dark'),

      // UI Elements
      'views' => __('views'),
      'photos' => __('photos'),
      'rating' => __('rating'),
      'new' => __('NEW'),
      'featured' => __('FEATURED'),
      'yourWork' => __('YOUR WORK'),

      // Actions
      'viewAlbum' => __('View album'),
      'like' => __('Like'),
      'share' => __('Share'),
      'download' => __('Download'),

      // Filters
      'allGenres' => __('All genres'),
      'rock' => __('Rock'),
      'classical' => __('Classical'),
      'jazz' => __('Jazz'),
      'electronic' => __('Electronic'),
      'folk' => __('Folk'),

      'sortBy' => __('Sort by'),
      'mostRecent' => __('Most recent'),
      'mostViewed' => __('Most viewed'),
      'topRated' => __('Top rated'),

      // Pagination
      'loadMore' => __('Load more'),
      'page' => __('Page'),
      'of' => __('of'),
    ]);
    </script>
</head>

<body class="min-h-full bg-[var(--color-stage-50)] dark:bg-[var(--color-stage-900)] text-gray-900 dark:text-gray-100 transition-colors duration-200">

{{-- ✅ Skip to content link (accessibility) --}}
<a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-stage-600 text-white px-4 py-2 rounded-lg z-50">
    {{ __('Skip to content') }}
</a>

{{-- ✅ Main slot for page content --}}
{{ $slot }}

{{-- ✅ Livewire 4 Script Config (REQUIRED when using Vite) --}}
@livewireScriptConfig

{{-- ✅ Browser logger for development (remove in production) --}}
@if(app()->environment('local'))
    <script>
        // Log browser events to Laravel for debugging
        const logToServer = (type, message, data = null) => {
            fetch('/_boost/browser-logs', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: JSON.stringify({ type, message, data, url: window.location.href })
            }).catch(() => {}); // Fail silently
        };

        // Override console methods
        ['log', 'warn', 'error'].forEach(method => {
            const original = console[method];
            console[method] = function(...args) {
                logToServer(method, args.join(' '), { stack: new Error().stack });
                original.apply(console, args);
            };
        });

        // Log Alpine/Livewire errors
        window.addEventListener('alpine:error', e => {
            logToServer('alpine:error', e.detail.message, { expression: e.detail.expression });
        });

        Livewire.hook('message.failed', (message, respond) => {
            logToServer('livewire:error', message.message, { payload: message.payload });
        });
    </script>
@endif

</body>
</html>
