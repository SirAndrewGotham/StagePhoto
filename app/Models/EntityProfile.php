<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\EntityProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntityProfile extends Model
{
    /** @use HasFactory<EntityProfileFactory> */
    use HasFactory;
}
