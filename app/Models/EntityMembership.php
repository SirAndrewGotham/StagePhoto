<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\EntityMembershipFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntityMembership extends Model
{
    /** @use HasFactory<EntityMembershipFactory> */
    use HasFactory;
}
