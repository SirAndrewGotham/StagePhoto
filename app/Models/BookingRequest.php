<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BookingRequestStatus;
use Database\Factories\BookingRequestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['requester_id', 'photographer_id', 'album_id', 'message', 'status', 'desired_date_start', 'desired_date_end', 'budget_notes'])]
class BookingRequest extends Model
{
    /** @use HasFactory<BookingRequestFactory> */
    use HasFactory;

    #[\Override]
    protected function casts(): array
    {
        return [
            'status' => BookingRequestStatus::class,
            'desired_date_start' => 'date',
            'desired_date_end' => 'date',
        ];
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function photographer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'photographer_id');
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }
}
