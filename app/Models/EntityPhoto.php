<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\EntityPhotoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntityPhoto extends Model
{
    /** @use HasFactory<EntityPhotoFactory> */
    use HasFactory;
}
