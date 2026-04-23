<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['genre', 'formed_year', 'record_label'])]
class Band extends Model
{
    use SoftDeletes;

    public function entity()
    {
        return $this->morphOne(Entity::class, 'entityable');
    }
}
