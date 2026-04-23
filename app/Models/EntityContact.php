<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\EntityContactFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntityContact extends Model
{
    /** @use HasFactory<EntityContactFactory> */
    use HasFactory;
}
