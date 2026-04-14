<?php

use Livewire\Component;

new class extends Component {
    // Layout component doesn't need additional logic
};

?>

    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>StagePhoto.ru — {{ __('Концертная и театральная фотография') }}</title>
    <meta name="description" content="{{ __('Откройте для себя потрясающую концертную и театральную фотографию.') }}">

    <!-- Tailwind CSS 4 -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        stage: {
                            50: '#fff8f0',
                            100: '#ffedd5',
                            500: '#f97316',
                            600: '#ea580c',
                            900: '#1e1b4b',
                        },
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @livewireStyles

    <style>
        .masonry-grid {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1rem;
        }
        @media (min-width: 640px) { .masonry-grid { gap: 1.25rem; } }
        @media (min-width: 1024px) { .masonry-grid { gap: 1.5rem; } }

        .album-card {
            break-inside: avoid;
        }

        .album-cover {
            transition: transform 0.5s ease;
        }
        .group:hover .album-cover {
            transform: scale(1.05);
        }

        .filter-pills::-webkit-scrollbar {
            height: 4px;
        }
        .filter-pills::-webkit-scrollbar-track {
            background: transparent;
        }
        .filter-pills::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 2px;
        }
        .dark .filter-pills::-webkit-scrollbar-thumb {
            background: #475569;
        }

        .lang-btn.active {
            background-color: rgb(234 88 12);
            color: white;
        }
        .dark .lang-btn.active {
            background-color: rgb(249 115 22);
            color: white;
        }

        [x-cloak] { display: none !important; }
    </style>
</head>
<body
    x-data="app()"
    x-init="init()"
    class="bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 transition-colors duration-300"
>
{{ $slot }}

@livewireScripts

<script>
    function app() {
        return {
            darkMode: false,
            language: '{{ app()->getLocale() }}',
            init() {
                // Check system preference for dark mode
                if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    this.darkMode = true;
                    document.documentElement.classList.add('dark');
                } else {
                    const stored = localStorage.getItem('darkMode');
                    if (stored !== null) {
                        this.darkMode = stored === 'true';
                        if (this.darkMode) document.documentElement.classList.add('dark');
                    }
                }

                // Listen for system changes
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                    if (localStorage.getItem('darkMode') === null) {
                        this.darkMode = e.matches;
                    }
                });

                // Watch for dark mode changes
                this.$watch('darkMode', val => {
                    if (val) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                    localStorage.setItem('darkMode', val);
                });
            },
            toggleDarkMode() {
                this.darkMode = !this.darkMode;
            },
            setLanguage(lang) {
                // Set cookie via fetch API
                fetch(`/lang/${lang}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(() => {
                    // Reload the page to apply new language
                    window.location.reload();
                }).catch(() => {
                    // Fallback: direct cookie set and reload
                    document.cookie = `language=${lang}; path=/; max-age=31536000`;
                    window.location.reload();
                });
            }
        }
    }
</script>
</body>
</html>
