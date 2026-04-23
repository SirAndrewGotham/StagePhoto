<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\EntityAlbumFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntityAlbum extends Model
{
    /** @use HasFactory<EntityAlbumFactory> */
    use HasFactory;
}
