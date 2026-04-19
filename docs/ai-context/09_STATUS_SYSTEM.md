# 📋 Status & Approval System

## Overview

StagePhoto.ru implements a comprehensive status and approval workflow for both albums and photos. This ensures content quality and provides moderation capabilities.

## Database Schema

### Statuses Table
```sql
CREATE TABLE statuses (
    id BIGINT PRIMARY KEY,
    statusable_id BIGINT NOT NULL,
    statusable_type VARCHAR(255) NOT NULL,
    status VARCHAR(50) NOT NULL,
    comment TEXT NULL,
    changed_by BIGINT NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    INDEX idx_statusable (statusable_id, statusable_type),
    INDEX idx_status (status)
);
```

### Album/Photo Status Fields
```sql
ALTER TABLE albums ADD COLUMN status VARCHAR(50) DEFAULT 'pending';
ALTER TABLE photos ADD COLUMN status VARCHAR(50) DEFAULT 'pending';
```

## Status Values

| Status | Description | Visible to Public |
|--------|-------------|-------------------|
| `pending` | Awaiting admin review | No |
| `approved` | Approved but not yet published | No |
| `published` | Live on the site | Yes |
| `rejected` | Rejected with feedback | No |
| `blocked` | Blocked for policy violation | No |

## Workflow

### Standard Flow
1. Photographer uploads → Status: `pending`
2. Admin reviews → Status: `approved` or `rejected`
3. Photographer publishes → Status: `published`

### Admin Actions
- Approve with optional comment
- Reject with required feedback
- Block with reason
- Publish directly

## Usage Examples

### Adding Status to Album
```php
$album = Album::find(1);
$album->addStatus('approved', 'Quality meets standards');
```

### Checking Status History
```php
$history = $album->statuses()->with('changer')->get();

foreach ($history as $status) {
    echo $status->status . ' by ' . $status->changer->name;
    echo ' on ' . $status->created_at;
    if ($status->comment) echo ' - ' . $status->comment;
}
```

### Conditional Display
```blade
@if($album->status === 'published')
    <span class="badge badge-success">Published</span>
@elseif($album->status === 'pending')
    <span class="badge badge-warning">Pending Review</span>
@endif
```

## Admin Panel (Future)

Planned admin features:
- Bulk approve/reject
- Status change notifications
- Review queue dashboard
- Moderation comments
- Appeal process











```

### 6. Update `CHANGELOG.md`

```markdown
## [1.1.0] - 2026-04-19

### Added
- Single photo upload with metadata
- Multiple photos upload with drag-and-drop
- Albums index page with public and photographer views
- Status/approval system for albums and photos
- Status history tracking with comments
- Grid and list view modes for albums
- Album search and sort functionality
- Photographer statistics dashboard
- Unsorted album card for photographers
- Publish/Unpublish controls for photographers
- Status badges (pending, approved, published, rejected, blocked)

### Changed
- Albums route now public with conditional photographer tools
- Factories and seeders updated for status system
- ImageProcessingService optimized for Intervention Image v4

### Fixed
- Drag-and-drop functionality for file uploads
- Image preview generation
- EXIF data extraction
