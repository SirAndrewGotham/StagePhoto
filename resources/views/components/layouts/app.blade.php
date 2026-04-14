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
    <title>StagePhoto.ru — Концертная и театральная фотография</title>
    <meta name="description" content="Откройте для себя потрясающую концертную и театральную фотографию.">

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
            language: 'ru',
            init() {
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

                const storedLang = localStorage.getItem('language');
                if (storedLang) this.language = storedLang;

                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                    this.darkMode = e.matches;
                    if (e.matches) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                    localStorage.setItem('darkMode', e.matches);
                });

                this.$watch('darkMode', val => {
                    val ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark');
                    localStorage.setItem('darkMode', val);
                });

                this.$watch('language', val => {
                    localStorage.setItem('language', val);
                    document.documentElement.lang = val;
                    window.dispatchEvent(new CustomEvent('language-changed', { detail: { language: val } }));
                });
            },
            toggleDarkMode() {
                this.darkMode = !this.darkMode;
            },
            setLanguage(lang) {
                this.language = lang;
            },
            t(key) {
                const translations = {
                    ru: {
                        search: 'Поиск групп, площадок...',
                        signIn: 'Войти',
                        submitWork: 'Добавить фото',
                        all: 'Все',
                        rock: 'Рок',
                        metal: 'Метал',
                        theater: 'Театр',
                        festivals: 'Фестивали',
                        jazz: 'Джаз',
                        classical: 'Классика',
                        electronic: 'Электроника',
                        folk: 'Фолк',
                        mostRecent: '📅 Недавние',
                        mostViewed: '🔥 Популярные',
                        topRated: '⭐ Лучшие',
                        newPhotographers: '👥 Новые авторы',
                        latestAlbums: 'Последние альбомы',
                        albums: 'альбомов',
                        photos: 'фото',
                        viewAlbum: 'Смотреть альбом',
                        request: 'Заказать',
                        loadMore: 'Загрузить ещё',
                        showing: 'Показано',
                        of: 'из',
                        platform: 'Платформа',
                        community: 'Сообщество',
                        legal: 'Правовая информация',
                        connect: 'Контакты',
                        submitWorkLink: 'Добавить фото',
                        forBands: 'Для групп',
                        forTheaters: 'Для театров',
                        photographerGuide: 'Гид фотографа',
                        featuredArtists: 'Избранные авторы',
                        monthlyContest: 'Ежемесячный конкурс',
                        workshops: 'Мастер-классы',
                        blog: 'Блог',
                        privacyPolicy: 'Политика конфиденциальности',
                        termsOfService: 'Условия использования',
                        copyright: 'Авторские права',
                        cookieSettings: 'Настройки cookie',
                        telegram: 'Telegram',
                        vkontakte: 'ВКонтакте',
                        instagram: 'Instagram',
                        emailSupport: 'Email поддержка',
                        madeIn: 'Сделано с ❤️ в Москве',
                        light: '☀️ Светлая',
                        dark: '🌙 Тёмная',
                    },
                    en: {
                        search: 'Search bands, venues...',
                        signIn: 'Sign In',
                        submitWork: 'Submit Photo',
                        all: 'All',
                        rock: 'Rock',
                        metal: 'Metal',
                        theater: 'Theater',
                        festivals: 'Festivals',
                        jazz: 'Jazz',
                        classical: 'Classical',
                        electronic: 'Electronic',
                        folk: 'Folk',
                        mostRecent: '📅 Most Recent',
                        mostViewed: '🔥 Most Viewed',
                        topRated: '⭐ Top Rated',
                        newPhotographers: '👥 New Photographers',
                        latestAlbums: 'Latest Albums',
                        albums: 'albums',
                        photos: 'photos',
                        viewAlbum: 'View Album',
                        request: 'Request',
                        loadMore: 'Load More',
                        showing: 'Showing',
                        of: 'of',
                        platform: 'Platform',
                        community: 'Community',
                        legal: 'Legal',
                        connect: 'Connect',
                        submitWorkLink: 'Submit Photo',
                        forBands: 'For Bands',
                        forTheaters: 'For Theaters',
                        photographerGuide: 'Photographer Guide',
                        featuredArtists: 'Featured Artists',
                        monthlyContest: 'Monthly Contest',
                        workshops: 'Workshops',
                        blog: 'Blog',
                        privacyPolicy: 'Privacy Policy',
                        termsOfService: 'Terms of Service',
                        copyright: 'Copyright',
                        cookieSettings: 'Cookie Settings',
                        telegram: 'Telegram',
                        vkontakte: 'VKontakte',
                        instagram: 'Instagram',
                        emailSupport: 'Email Support',
                        madeIn: 'Made with ❤️ in Moscow',
                        light: '☀️ Light',
                        dark: '🌙 Dark',
                    },
                    eo: {
                        search: 'Serĉi bandojn, venuejojn...',
                        signIn: 'Ensaluti',
                        submitWork: 'Sendi Foton',
                        all: 'Ĉiuj',
                        rock: 'Roko',
                        metal: 'Metalo',
                        theater: 'Teatro',
                        festivals: 'Festivaloj',
                        jazz: 'Ĵazo',
                        classical: 'Klasika',
                        electronic: 'Elektronika',
                        folk: 'Folko',
                        mostRecent: '📅 Plej Novaj',
                        mostViewed: '🔥 Plej Viditaj',
                        topRated: '⭐ Plej Bonaj',
                        newPhotographers: '👥 Novaj Fotistoj',
                        latestAlbums: 'Plej Novaj Albumoj',
                        albums: 'albumoj',
                        photos: 'fotoj',
                        viewAlbum: 'Vidi Albumon',
                        request: 'Peti',
                        loadMore: 'Ŝargi Pli',
                        showing: 'Montrataj',
                        of: 'de',
                        platform: 'Platformo',
                        community: 'Komunumo',
                        legal: 'Jura',
                        connect: 'Konekti',
                        submitWorkLink: 'Sendi Foton',
                        forBands: 'Por Bandoj',
                        forTheaters: 'Por Teatroj',
                        photographerGuide: 'Gvidilo por Fotistoj',
                        featuredArtists: 'Elstaraj Artistoj',
                        monthlyContest: 'Monata Konkurso',
                        workshops: 'Laborejoj',
                        blog: 'Blogo',
                        privacyPolicy: 'Privateca Politiko',
                        termsOfService: 'Kondiĉoj de Servo',
                        copyright: 'Aŭtorrajto',
                        cookieSettings: 'Kuketaj Agordoj',
                        telegram: 'Telegram',
                        vkontakte: 'VKontakte',
                        instagram: 'Instagram',
                        emailSupport: 'Retpoŝta Subteno',
                        madeIn: 'Farita kun ❤️ en Moskvo',
                        light: '☀️ Hela',
                        dark: '🌙 Malhela',
                    }
                };
                return translations[this.language]?.[key] || translations.ru[key] || key;
            }
        }
    }
</script>
</body>
</html>
