# 🧩 Component Specifications

### `<x-album-card>`
- **Props**: `$album`, `$showRequestBtn = true`
- **Structure**: Entire card is clickable (no separate buttons)
- **Navigation**: `wire:navigate` for SPA-style transitions
- **Image**: Album cover square (800x800 WebP)
- **Hover**: Scale cover 1.05x, fade overlay
- **No Buttons on Card** - Request button moved to album page sidebar

## Filter Bar (`<x-filter-bar>`)
- **Layout**: Flex wrap, pills + dropdown + search
- **State**: Synced with Livewire `AlbumFilters` component
- **Responsive**: Pills scroll, search collapses on mobile

## Header (`<x-header>`)
- **Logo**: `StagePhoto.ru` with gradient icon
- **Right**: Search (desktop) → Lang Switcher → Dark Toggle → Auth Buttons
- **Sticky**: `top-0 z-50 backdrop-blur-sm`

## Language Switcher (`<x-lang-switcher>`)
- **Structure**: 3 buttons in pill container
- **Active**: `bg-stage-600 text-white`
- **Inactive**: `bg-gray-100 dark:bg-gray-800 hover:bg-white/50`
- **Alpine**: `x-data="{ lang: localStorage.getItem('language') || 'ru' }"`

## Request Modal (`<x-request-modal>`)
- **Trigger**: `@click="$dispatch('open-modal', { id: 'request', photographerId })`
- **Fields**: Message, date range, venue, budget (optional)
- **Validation**: Alpine + Livewire `wire:submit`
- **Feedback**: Success toast, email notification

### Album Show Page (`⚡album-show.blade.php`)
- **Header Image**: Hero cover (2000x800 WebP)
- **Photo Grid**: Thumbnails (600x600 WebP, square crop)
- **Photo Modal**: Full-size photo (1600px max side, WebP with watermark)
- **Request Button Location**:
    - Sidebar (general album requests)
    - Photo modal (specific photo requests)
- **Rating System**: 5-star rating with user persistence
- **Comment System**: Threaded comments with likes

## 📸 Photo Upload Component (`⚡photo-upload.blade.php`)

- **Purpose**: Single photo upload interface for photographers
- **Location**: `resources/views/components/frontend/pages/⚡photo-upload.blade.php`
- **Route**: `/upload` (authenticated users only)

### Features
- Album selection (existing or create new)
- File upload with validation (image, max 50MB)
- Optional title and description fields
- Automatic EXIF data extraction
- Processing indicator with loading state
- Success modal with confirmation
- Error handling with user-friendly messages

### Form Fields
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| Album selection | Radio + Select/Create | Yes | Choose existing or create new album |
| New album title | Text | If creating new | Title for new album |
| Photo file | File | Yes | Image file (JPG, PNG, GIF, WebP) |
| Photo title | Text | No | Custom title for the photo |
| Photo description | Textarea | No | Description of the photo |

### Processing Steps
1. Validate input
2. Get or create target album
3. Process upload via ImageProcessingService
4. Create photo record in database
5. Update album photo count
6. Generate album covers if first photo
7. Display success modal with results

### Dependencies
- `ImageProcessingService` - Image processing logic
- `UnsortedAlbumService` - Fallback album for unassigned uploads
- `ExifExtractorService` - EXIF data extraction

### Request Modal (`⚡request-modal.blade.php`)
- **Request Types**:
    - Personal Use (wallpaper, social media)
    - High Resolution Photos
    - Print Permission
    - Commercial Use
    - Hire Photographer for Event
- **Conditional Fields**: Event date, venue, budget (for hire requests)
- **Auto-fill**: Pre-fills name/email for logged-in users
- **Storage**: Saves to `requests` table with status tracking

## 📁 Albums Index Component (`⚡albums-index.blade.php`)

- **Purpose**: Browse and manage albums (public + photographer views)
- **Location**: `resources/views/components/frontend/⚡albums-index.blade.php`
- **Route**: `/albums` (public access)

### Features

#### Public View
- Browse all published albums
- Grid and list view modes
- Search by album title
- Sort by date, photo count, views
- Responsive design

#### Photographer View (when authenticated)
- "My Albums" toggle to see own albums
- Album statistics (total, published, pending)
- Management tools per album:
    - Publish/Unpublish
    - Delete (move to trash)
- Status badges (pending, approved, published, rejected)
- Unsorted album card with photo count

### Query Logic
```php
// Public view
Album::where('is_published', true)
     ->where('status', 'published')

// Photographer view  
Album::where('photographer_id', auth()->id())
```

### Rating System
- **Database**: `ratings` table (polymorphic)
- **Average Rating**: Stored as decimal, displayed with 1 decimal
- **User Rating**: Stars highlight based on user's previous rating
- **Guest Handling**: Prompts login when unauthenticated

### Like System
- **Database**: `likes` table (polymorphic)
- **Toggle**: Click to like/unlike comments
- **Visual**: Heart icon fills on liked state

## 🌍 Language Switcher with Livewire 4 Integration

### Alpine.js Store (Translations)
Place this in your main layout file (`resources/views/layouts/app.blade.php`):

```javascript
<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('i18n', {
        locale: localStorage.getItem('language') || 'ru',
        
        translations: {
            ru: {
                // Navigation
                search: 'Поиск групп, площадок...',
                signIn: 'Войти',
                submitWork: 'Добавить фото',
                
                // Filters
                all: 'Все',
                rock: 'Рок',
                metal: 'Метал',
                theater: 'Театр',
                festivals: 'Фестивали',
                jazz: 'Джаз',
                classical: 'Классика',
                electronic: 'Электроника',
                folk: 'Фолк',
                
                // Sort options
                mostRecent: '📅 Недавние',
                mostViewed: '🔥 Популярные',
                topRated: '⭐ Лучшие',
                newPhotographers: '👥 Новые авторы',
                
                // Album grid
                latestAlbums: 'Последние альбомы',
                albums: 'альбомов',
                photos: 'фото',
                viewAlbum: 'Смотреть альбом',
                request: 'Заказать',
                loadMore: 'Загрузить ещё',
                showing: 'Показано',
                of: 'из',
                
                // Footer
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
                // Navigation
                search: 'Search bands, venues...',
                signIn: 'Sign In',
                submitWork: 'Submit a Photo',
                
                // Filters
                all: 'All',
                rock: 'Rock',
                metal: 'Metal',
                theater: 'Theater',
                festivals: 'Festivals',
                jazz: 'Jazz',
                classical: 'Classical',
                electronic: 'Electronic',
                folk: 'Folk',
                
                // Sort options
                mostRecent: '📅 Most Recent',
                mostViewed: '🔥 Most Viewed',
                topRated: '⭐ Top Rated',
                newPhotographers: '👥 New Photographers',
                
                // Album grid
                latestAlbums: 'Latest Albums',
                albums: 'albums',
                photos: 'photos',
                viewAlbum: 'View Album',
                request: 'Request',
                loadMore: 'Load More',
                showing: 'Showing',
                of: 'of',
                
                // Footer
                platform: 'Platform',
                community: 'Community',
                legal: 'Legal',
                connect: 'Connect',
                submitWorkLink: 'Submit a Photo',
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
                // Navigation
                search: 'Serĉi bandojn, venuejojn...',
                signIn: 'Ensaluti',
                submitWork: 'Sendi Foton',
                
                // Filters
                all: 'Ĉiuj',
                rock: 'Roko',
                metal: 'Metalo',
                theater: 'Teatro',
                festivals: 'Festivaloj',
                jazz: 'Ĵazo',
                classical: 'Klasika',
                electronic: 'Elektronika',
                folk: 'Folko',
                
                // Sort options
                mostRecent: '📅 Plej Novaj',
                mostViewed: '🔥 Plej Viditaj',
                topRated: '⭐ Plej Bonaj',
                newPhotographers: '👥 Novaj Fotistoj',
                
                // Album grid
                latestAlbums: 'Plej Novaj Albumoj',
                albums: 'albumoj',
                photos: 'fotoj',
                viewAlbum: 'Vidi Albumon',
                request: 'Peti',
                loadMore: 'Ŝargi Pli',
                showing: 'Montrataj',
                of: 'de',
                
                // Footer
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
        },
        
        t(key) {
            return this.translations[this.locale]?.[key] || this.translations.ru[key] || key;
        },
        
        setLocale(lang) {
            if (!this.translations[lang]) return;
            
            this.locale = lang;
            localStorage.setItem('language', lang);
            document.documentElement.lang = lang;
            
            // Notify Livewire components
            window.dispatchEvent(new CustomEvent('language-changed', { 
                detail: { language: lang } 
            }));
        }
    });
});
</script>
```

### Language Switcher Component (`⚡lang-switcher.blade.php`)

```blade
<?php
use Livewire\Component;

new class extends Component {
    public function render()
    {
        return <<<'HTML'
            <div class="flex items-center bg-gray-100 dark:bg-gray-800 rounded-xl p-1 gap-1">
                <button 
                    @click="$store.i18n.setLocale('ru')"
                    class="lang-btn px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-all"
                    :class="{ 
                        'active bg-stage-600 text-white': $store.i18n.locale === 'ru',
                        'text-gray-700 dark:text-gray-300 hover:bg-white/50 dark:hover:bg-gray-700/50': $store.i18n.locale !== 'ru'
                    }"
                    title="Русский"
                >
                    RU
                </button>
                <button 
                    @click="$store.i18n.setLocale('en')"
                    class="lang-btn px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-all"
                    :class="{ 
                        'active bg-stage-600 text-white': $store.i18n.locale === 'en',
                        'text-gray-700 dark:text-gray-300 hover:bg-white/50 dark:hover:bg-gray-700/50': $store.i18n.locale !== 'en'
                    }"
                    title="English"
                >
                    EN
                </button>
                <button 
                    @click="$store.i18n.setLocale('eo')"
                    class="lang-btn px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-all"
                    :class="{ 
                        'active bg-stage-600 text-white': $store.i18n.locale === 'eo',
                        'text-gray-700 dark:text-gray-300 hover:bg-white/50 dark:hover:bg-gray-700/50': $store.i18n.locale !== 'eo'
                    }"
                    title="Esperanto"
                >
                    EO
                </button>
            </div>
        HTML;
    }
};
?>
```

### Livewire Component Listening for Language Changes

```php
<?php
use Livewire\Component;
use Livewire\Attributes\On;

new class extends Component {
    public $albums = [];
    public $selectedGenre = 'all';
    
    #[On('language-changed')]
    public function refreshForLanguage($language)
    {
        // Re-fetch data with new locale
        // For example, load translated album titles or descriptions
        $this->loadAlbums();
        
        // Force component refresh
        $this->dispatch('$refresh');
    }
    
    public function loadAlbums()
    {
        // Load albums with current locale
        $this->albums = Album::withTranslation(app()->getLocale())->get();
    }
    
    public function render()
    {
        return <<<'HTML'
            <div>
                <h2 x-text="$store.i18n.t('latestAlbums')"></h2>
                
                @foreach($albums as $album)
                    <div>{{ $album->getTranslation('title', app()->getLocale()) }}</div>
                @endforeach
            </div>
        HTML;
    }
};
?>
```

### Usage in Blade Templates

```blade
<!-- Anywhere in your Blade files -->
<div x-data>
    <!-- Static text translation -->
    <h1 x-text="$store.i18n.t('latestAlbums')"></h1>
    
    <!-- Dynamic attribute translation -->
    <input 
        type="search" 
        :placeholder="$store.i18n.t('search')"
        class="border rounded"
    >
    
    <!-- Conditional translation -->
    <button x-text="$store.i18n.t($store.theme.dark ? 'dark' : 'light')"></button>
    
    <!-- Pluralization example -->
    <span x-text="`${count} ${$store.i18n.t('albums')}`"></span>
</div>
```

### Adding New Translations

1. **Add key to all language objects** in the Alpine store:
```javascript
translations: {
    ru: { newKey: 'Russian text' },
    en: { newKey: 'English text' },
    eo: { newKey: 'Esperanto text' }
}
```

2. **Use in templates**:
```blade
<span x-text="$store.i18n.t('newKey')"></span>
```

3. **For Livewire translations** (server-side), use Laravel's localization:
```php
// In PHP
__('messages.welcome')

// In Blade
{{ __('messages.welcome') }}
```

### Testing Translations

```javascript
// Test translation switching
test('language changes from RU to EN', async ({ page }) => {
    await page.click('button:has-text("EN")');
    const htmlLang = await page.getAttribute('html', 'lang');
    expect(htmlLang).toBe('en');
    
    const searchPlaceholder = await page.getAttribute('input[type="search"]', 'placeholder');
    expect(searchPlaceholder).toBe('Search bands, venues...');
});

// Test localStorage persistence
test('language persists after reload', async ({ page }) => {
    await page.click('button:has-text("EN")');
    await page.reload();
    const activeButton = await page.locator('.lang-btn.active').textContent();
    expect(activeButton).toBe('EN');
});
```

## 🗑️ Trash Manager (`⚡trash-manager.blade.php`)

- **Purpose**: Manage soft-deleted albums and photos
- **Location**: `resources/views/components/frontend/ui/⚡trash-manager.blade.php`
- **Access**: Photographer-only (authenticated)
- **Features**:
    - Toggle between deleted albums and photos
    - Restore items with one click
    - Permanent deletion with confirmation
    - Shows deletion timestamp
    - Pagination for large lists

### Usage
```blade
@livewire('frontend.trash-manager')
```

## 📸 Photo Uploader (`⚡photo-uploader.blade.php`)

- **Purpose**: Upload photos to albums
- **Location**: `resources/views/components/frontend/⚡photo-uploader.blade.php`
- **Upload Methods**:
    - Single photo upload
    - Multiple photo upload
    - ZIP archive upload
- **Features**:
    - Drag-and-drop support
    - Progress tracking
    - Duplicate detection (by file hash)
    - Automatic WebP conversion
    - Watermark application
    - Automatic unsorted album creation

### Usage
```blade
@livewire('frontend.photo-uploader', ['album' => $album])
```

## 📁 Unsorted Album

- **Purpose**: Default album for unorganized uploads
- **Properties**:
    - `is_unsorted = true`
    - `is_published = false` (private)
    - Special badge: "📁 UNSORTED"
- **Behavior**:
    - Auto-created on first upload
    - Photos can be moved to other albums
    - Not visible to site visitors
    - Accessible only in photographer's dashboard

# Livewire 4 SFC Components

## Upload Components

### Album Selector (`⚡album-selector.blade.php`)
Reusable component for album selection and creation:
- Displays hierarchical album tree with visual indicators
- Handles both existing album selection and new album creation
- Supports parent album selection for sub-albums
- Includes category selection for new albums

### Upload Form (`⚡upload-form.blade.php`)
Centralized upload handling for all upload types:
- Supports single, multiple, and ZIP uploads
- Integrates with album-selector component
- Handles validation, processing, and success/error states

### Partial Components
- `photo-upload-dropzone.blade.php` - Drag-and-drop for photos
- `zip-upload-dropzone.blade.php` - ZIP file upload with info panel
- `photo-details-form.blade.php` - Optional metadata form
- `upload-success-modal.blade.php` - Success/failure feedback modal
