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
| pending | Awaiting admin review | No |
| approved | Approved but not yet published | No |
| published | Live on the site | Yes |
| rejected | Rejected with feedback | No |
| blocked | Blocked for policy violation | No |

## Workflow

### Standard Flow

1. Photographer uploads → Status: pending
2. Admin reviews → Status: approved or rejected
3. Photographer publishes → Status: published

### Admin Actions

- Approve with optional comment
- Reject with required feedback
- Block with reason
- Publish directly

## Frontend Display - Status Badges

### Status Badge Component

Create `resources/views/components/frontend/ui/⚡status-badge.blade.php`:

```blade
<?php

use Livewire\Component;

new class extends Component {
    public $status;
    public $showLabel = true;

    public function getStatusConfigProperty()
    {
        return [
            'published' => ['class' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400', 'icon' => '✅', 'label' => 'Published'],
            'pending' => ['class' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400', 'icon' => '⏳', 'label' => 'Pending Review'],
            'approved' => ['class' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400', 'icon' => '✓', 'label' => 'Approved'],
            'rejected' => ['class' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400', 'icon' => '❌', 'label' => 'Rejected'],
            'blocked' => ['class' => 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-400', 'icon' => '🚫', 'label' => 'Blocked'],
        ];
    }

    public function getConfigProperty()
    {
        return $this->statusConfig[$this->status] ?? [
            'class' => 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-400',
            'icon' => '●',
            'label' => ucfirst($this->status)
        ];
    }
};

?>

<span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full {{ $this->config['class'] }}">
    <span>{{ $this->config['icon'] }}</span>
    @if($showLabel)
        <span>{{ $this->config['label'] }}</span>
    @endif
</span>
```

### Usage in Album Card

```blade
@livewire('frontend.ui.status-badge', ['status' => $album->status], key('status-' . $album->id))
```

### Inline Usage

```blade
@switch($album->status)
    @case('published')
        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">
            ✅ Published
        </span>
        @break
    @case('pending')
        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">
            ⏳ Pending Review
        </span>
        @break
    @case('approved')
        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
            ✓ Approved
        </span>
        @break
    @case('rejected')
        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">
            ❌ Rejected
        </span>
        @break
    @case('blocked')
        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-400">
            🚫 Blocked
        </span>
        @break
@endswitch
```

### Status Change Dropdown (for Photographers)

```blade
<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="px-2 py-1 text-xs rounded border">
        {{ $album->status }}
    </button>
    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-1 bg-white dark:bg-gray-800 rounded shadow-lg z-10">
        <button wire:click="updateStatus('published')" class="block w-full text-left px-3 py-1 text-xs hover:bg-gray-100">✅ Publish</button>
        <button wire:click="updateStatus('pending')" class="block w-full text-left px-3 py-1 text-xs hover:bg-gray-100">⏳ Send to Review</button>
        <button wire:click="updateStatus('rejected')" class="block w-full text-left px-3 py-1 text-xs hover:bg-gray-100">❌ Reject</button>
    </div>
</div>
```

## Helper Methods

Add these to your Album and Photo models:

```php
public function isPublished(): bool
{
    return $this->status === 'published';
}

public function isPending(): bool
{
    return $this->status === 'pending';
}

public function isApproved(): bool
{
    return $this->status === 'approved';
}

public function isRejected(): bool
{
    return $this->status === 'rejected';
}

public function isBlocked(): bool
{
    return $this->status === 'blocked';
}

public function canBePublished(): bool
{
    return in_array($this->status, ['approved', 'published']);
}

public function addStatus(string $status, ?string $comment = null): void
{
    Status::create([
        'statusable_id' => $this->id,
        'statusable_type' => get_class($this),
        'status' => $status,
        'comment' => $comment,
        'changed_by' => auth()->id(),
    ]);

    $this->update(['status' => $status]);
}
```

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

### Conditional Display in Blade

```blade
@if($album->isPublished())
    <span class="badge badge-success">Published</span>
@elseif($album->isPending())
    <span class="badge badge-warning">Pending Review</span>
@endif
```

## Admin Panel (Planned)

Planned admin features:
- Bulk approve/reject
- Status change notifications
- Review queue dashboard
- Moderation comments
- Appeal process

## Integration with Entity System

Entities (theaters, bands, individuals) can also have statuses for their profile pages:
- published - Profile visible to public
- draft - Profile in progress, not visible
- archived - Profile hidden but preserved

See `10_ENTITY_SYSTEM.md` for more details.
```

This version has:
- Removed duplicate "Admin Panel" section
- Cleaned up all markdown formatting
- Added proper helper methods section
- Consistent spacing and structure
- No broken code blocks
