<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>StagePhoto.ru — @lang('platform')</title>
    <meta name="description" content="@lang('platform_description')">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        stage: { 50: '#fff8f0', 100: '#ffedd5', 500: '#f97316', 600: '#ea580c', 900: '#1e1b4b' },
                    },
                }
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @livewireStyles

    <style>
        /* Global fix for sticky header overlap */
        .scroll-target {
            scroll-margin-top: 64px;
        }

        /* Apply to any element that might be scrolled to */
        [id] {
            scroll-margin-top: 64px;
        }
        .masonry-grid { width: 100%; display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; }
        .lang-btn.active { background-color: rgb(234 88 12); color: white; }
        .dark .lang-btn.active { background-color: rgb(249 115 22); color: white; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body x-data="app" x-init="init()" class="bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 transition-colors duration-300">

{{ $slot }}

<script>
    // Register global store directly (not inside alpine:init)
    if (typeof Alpine !== 'undefined') {
        Alpine.store('app', {
            language: '{{ app()->getLocale() }}',

            switchLanguage(lang) {
                if (this.language === lang) return;
                window.location.href = `/lang/${lang}`;
            }
        });
    }

    // Register component for main layout
    function app() {
        return {
            darkMode: false,

            init() {
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const storedDark = localStorage.getItem('darkMode');

                this.darkMode = storedDark !== null ? storedDark === 'true' : prefersDark;

                if (this.darkMode) {
                    document.documentElement.classList.add('dark');
                }

                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                    if (localStorage.getItem('darkMode') === null) {
                        this.darkMode = e.matches;
                        e.matches ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark');
                    }
                });

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
            }
        }
    }
</script>

@livewireScripts

</body>
</html>
