# 🎭 Entity System (Theaters, Bands, Individuals)

> Documentation for the polymorphic entity system that powers dedicated pages for theaters, bands, and individual artists.

## 📋 Overview

StagePhoto.ru supports dedicated profile pages for three types of entities:
- **Theaters** - Performance venues with capacity, founded year, artistic director
- **Bands** - Musical groups with genre, formed year, record label
- **Individuals** - Artists, musicians, actors with biographical information

All entities share a common structure via polymorphic relationships while maintaining type-specific attributes.

## 🗄️ Database Schema

### Core Tables

```sql
-- Main polymorphic entity table
entities:
  - id
  - entityable_id (references band/theater/individual)
  - entityable_type (class name: App\Models\Band, etc.)
  - slug (unique, URL-friendly)
  - type (theater/band/individual)
  - is_published (boolean)
  - settings (JSON for privacy preferences)
  - timestamps
  - softDeletes

-- Multi-language profiles
entity_profiles:
  - id
  - entity_id (foreign key)
  - locale (ru/en/eo)
  - name
  - bio (short description)
  - story (extended narrative)
  - website
  - social_links (JSON: {telegram, vk, instagram})
  - email
  - phone
  - address
  - founded_year
  - genre
  - avatar_path
  - cover_path
  - timestamps

-- Contact visibility controls
entity_contacts:
  - id
  - entity_id (foreign key)
  - contact_type (email/phone/telegram/vkontakte/instagram)
  - value
  - visibility (public/registered/photographers/admin)
  - timestamps

-- Entity relationships (individuals in bands/theaters)
entity_memberships:
  - id
  - entity_id (individual)
  - parent_entity_id (band/theater)
  - role (vocalist, guitarist, actor, director, etc.)
  - joined_at
  - left_at (null if active)
  - timestamps

-- Media links
entity_album (pivot):
  - entity_id
  - album_id
  - relationship_type (featured/dedicated/guest)
  - timestamps

entity_photos (pivot):
  - entity_id
  - photo_id
  - timestamps

## 🚏 Routes

```php
// Main entity route (Livewire 4 SFC)
Route::livewire('/persona/{entity:slug}', 'frontend.pages.persona.⚡show')
    ->name('persona.show');

// Convenience redirects for cleaner URLs
Route::get('/theater/{entity:slug}', fn($slug) => redirect()->route('persona.show', $slug));
Route::get('/band/{entity:slug}', fn($slug) => redirect()->route('persona.show', $slug));
Route::get('/artist/{entity:slug}', fn($slug) => redirect()->route('persona.show', $slug));
```

## Status & Moderation

Entity profiles support the same status workflow as albums:
- `published` - Profile visible to public
- `pending` - Awaiting review
- `rejected` - Profile rejected with feedback

*See `09_STATUS_SYSTEM.md` for the complete approval workflow.*
