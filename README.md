# 📸 StagePhoto.ru

[![Laravel Version](https://img.shields.io/badge/Laravel-13.x-red.svg)](https://laravel.com)
[![Livewire Version](https://img.shields.io/badge/Livewire-4.x-purple.svg)](https://livewire.laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.4+-blue.svg)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-4.x-06B6D4.svg)](https://tailwindcss.com)

**StagePhoto.ru** is a professional platform for concert and theater photography, connecting photographers with fans, bands, and event organizers. Built with Laravel 13 and Livewire 4.

🌐 **Live Site**: [stagephoto.ru](https://stagephoto.ru)

---

## ✨ Features

### 🖼️ For Visitors
- **Full-width responsive album grid** - No containers, pure masonry layout
- **Dark mode** - System preference detection with manual toggle
- **Multi-language support** - Russian, English, and Esperanto
- **Album browsing** - Filter by genre, type, and sort options
- **Photo viewing** - Lightbox modal with full-size images
- **Comments** - Threaded comments on albums and individual photos
- **Rating system** - 5-star ratings with user persistence
- **Like system** - Like/unlike comments

### 📸 For Photographers
- **Album management** - Create, edit, publish, and delete albums
- **Photo upload** - Single, multiple, and ZIP archive uploads
- **Image processing** - Automatic WebP conversion and optimization
- **Watermark protection** - Automatic watermark application
- **Unsorted album** - Default album for unorganized uploads
- **Bulk operations** - Move multiple photos between albums
- **Trash management** - Soft delete with restore functionality

### 🎯 For Bands & Organizers
- **Photographer requests** - Request specific photographers
- **High-res downloads** - Request high-resolution photos
- **Print permissions** - Request print rights
- **Commercial licensing** - Request commercial usage rights

---

## 🛠️ Tech Stack

| Category | Technologies |
|----------|--------------|
| **Backend** | Laravel 13, PHP 8.4+ |
| **Frontend** | Livewire 4, Alpine.js 3, Tailwind CSS 4 |
| **Database** | SQLite / MySQL / PostgreSQL |
| **Image Processing** | Intervention Image |
| **Queue** | Laravel Queue (Redis/Database) |
| **Cache** | Laravel Cache (Redis/File) |

---

## 📦 System Requirements

- PHP >= 8.4
- Composer
- Node.js & NPM (for Vite)
- SQLite / MySQL / PostgreSQL
- GD or Imagick PHP extension

---

## 🚀 Installation

### 1. Clone the repository

```bash
git clone https://github.com/SirAndrewGotham/StagePhoto.git
cd StagePhoto
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Environment configuration

```bash
cp .env.example .env
php artisan key:generate
```

Configure your database in `.env`:

```env
DB_CONNECTION=sqlite
# or for MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=stagephoto
# DB_USERNAME=root
# DB_PASSWORD=
```

### 4. Run migrations and seeders

```bash
php artisan migrate:fresh --seed
```

### 5. Build assets

```bash
npm run build
# or for development:
npm run dev
```

### 6. Start the server

```bash
php artisan serve
```

Visit `http://localhost:8000`

---

## 📁 Project Structure

```
app/
├── Livewire/                 # Livewire components
├── Models/                   # Eloquent models
├── Services/                 # Business logic services
│   ├── ImageProcessingService.php
│   └── UnsortedAlbumService.php
└── Http/
    └── Middleware/
        └── SetLocale.php     # Language detection

database/
├── factories/                # Model factories
├── migrations/               # Database migrations
└── seeders/                  # Database seeders

resources/views/
├── components/
│   └── frontend/            # Livewire SFC components
│       ├── ui/              # UI components
│       ├── islands/         # Island components
│       └── pages/           # Page components
├── layouts/                 # Layout templates
└── livewire/                # Legacy Livewire views

config/
├── image.php                # Image processing config
└── livewire.php             # Livewire configuration

lang/                        # Multi-language files
├── en/                      # English
├── ru/                      # Russian
└── eo/                      # Esperanto
```

---

## 🖼️ Image Processing

All uploaded images are automatically processed:

| Variant | Dimensions | Watermark | Usage |
|---------|------------|-----------|-------|
| Original | User-uploaded | No | Archival |
| Full-size | 1600px max side | Yes | Photo modal |
| Thumbnail | 600×600 (crop) | Yes | Grid preview |
| Cover Square | 800×800 | No | Album cards |
| Cover Hero | 2000×800 | No | Album header |

**Storage structure:**
```
storage/app/public/stagephoto/
├── originals/{user_id}/{album_id}/
├── webp/{user_id}/{album_id}/
│   ├── {photo_id}_full.webp
│   └── {photo_id}_thumb.webp
└── albums/{user_id}/{album_id}/
    ├── cover_square.webp
    └── cover_hero.webp
```

---

## 🌍 Multi-Language Support

Supported languages:
- 🇷🇺 Russian (default)
- 🇬🇧 English
- 🌐 Esperanto

Language switching uses Laravel's localization with JSON files in `/lang`.

---

## 🎨 Design Principles

- **Full-width layout** - No `container` or `max-w-*` constraints on main content
- **Responsive grid** - `grid-template-columns: repeat(auto-fill, minmax(280px, 1fr))`
- **Dark mode** - CSS class-based with system preference detection
- **Mobile-first** - Responsive breakpoints from 640px to 1920px

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --filter=AlbumTest

# Run Livewire component tests
php artisan test --filter=Livewire
```

---

## 📝 License

This project is proprietary software. All rights reserved.

---

## 👨‍💻 Author

**Andrew Gotham**

- 📧 Email: [andrewgotham@mail.ru](mailto:andrewgotham@mail.ru)
- 💬 Telegram: [@AndrewGotham](https://t.me/AndrewGotham)
- 📱 Phone: +7 (991) 873-9137
- 🌐 VK: [vk.com/AndrewGotham](https://vk.com/AndrewGotham)

---

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - The PHP framework
- [Livewire](https://livewire.laravel.com) - Full-stack framework
- [Tailwind CSS](https://tailwindcss.com) - Utility-first CSS
- [Alpine.js](https://alpinejs.dev) - Lightweight JavaScript framework
- [Intervention Image](https://image.intervention.io) - Image processing library

---

## 📊 Current Status

| Feature | Status |
|---------|--------|
| Album grid | ✅ Complete |
| Filter bar | ✅ Complete |
| Album show page | ✅ Complete |
| Photo modal | ✅ Complete |
| Comments | ✅ Complete |
| Rating system | ✅ Complete |
| Like system | ✅ Complete |
| Request system | ✅ Complete |
| Multi-language | ✅ Complete |
| Dark mode | ✅ Complete |
| Image upload | ✅ Complete |
| Image processing | ✅ Complete |
| Soft deletes | ✅ Complete |
| Trash manager | ✅ Complete |
| Unsorted albums | ✅ Complete |
| Admin dashboard | ⏳ Planned |

---

## 🤝 Contributing

This is a private project. For suggestions or bug reports, please contact the author directly.

---

## 📄 Changelog

### v1.0.0 (Current)
- Initial release
- Full album management system
- Multi-language support (RU, EN, EO)
- Image processing with WebP conversion
- Comment, rating, and like systems
- Photographer request system
- Soft delete with trash management

---

**Built with ❤️ in Moscow**
```

---

## Additional Files to Consider

### `CONTRIBUTING.md`

```markdown
# Contributing to StagePhoto.ru

This is a private project. Please contact Andrew Gotham directly for any contributions.

**Contact Information:**
- Email: andrewgotham@mail.ru
- Telegram: @AndrewGotham
- VK: vk.com/AndrewGotham
```

### `CHANGELOG.md`

```markdown
# Changelog

## [1.0.0] - 2026-04-15

### Added
- Initial release
- Album grid with infinite scroll
- Filter bar with genre, type, and sort options
- Album show page with photo grid
- Photo modal with lightbox navigation
- Comment system (albums and photos)
- Rating system (5-star)
- Like system for comments
- Tag system for albums
- Category system with translations
- Request system for photographers
- Image upload (single, multiple, ZIP)
- WebP conversion and optimization
- Watermark application
- Soft deletes for albums and photos
- Trash manager
- Unsorted albums
- Multi-language support (RU, EN, EO)
- Dark mode
```

### `.github/ISSUE_TEMPLATE/bug_report.md`

```markdown
---
name: Bug Report
about: Report a bug to help us improve
title: '[BUG] '
labels: bug
assignees: SirAndrewGotham

---

## Description
A clear description of the bug.

## Steps to Reproduce
1. Go to '...'
2. Click on '...'
3. Scroll to '...'
4. See error

## Expected Behavior
What you expected to happen.

## Screenshots
If applicable, add screenshots.

## Environment
- OS: [e.g., macOS]
- Browser: [e.g., Chrome, Safari]
- Version: [e.g., 22]

## Additional Context
Add any other context here.
```

### `.github/ISSUE_TEMPLATE/feature_request.md`

```markdown
---
name: Feature Request
about: Suggest an idea for this project
title: '[FEATURE] '
labels: enhancement
assignees: SirAndrewGotham

---

## Is your feature request related to a problem?
A clear description of what the problem is.

## Solution
A clear description of what you want to happen.

## Alternatives
Any alternative solutions you've considered.

## Additional Context
Add any other context here.
```

# GitHub Badges

[![Laravel](https://img.shields.io/badge/Laravel-13.x-red.svg)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-4.x-purple.svg)](https://livewire.laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.4+-blue.svg)](https://php.net)
[![Tailwind](https://img.shields.io/badge/Tailwind-4.x-06B6D4.svg)](https://tailwindcss.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC0D0.svg)](https://alpinejs.dev)
[![License](https://img.shields.io/badge/License-Proprietary-red.svg)](LICENSE)
