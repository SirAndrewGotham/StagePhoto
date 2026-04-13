<?php

declare(strict_types=1);

namespace App\Enums;

enum BookingRequestStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending Review',
            self::Accepted => 'Accepted',
            self::Rejected => 'Declined',
            self::Completed => 'Completed',
        };
    }
}
