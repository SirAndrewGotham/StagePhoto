<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['capacity', 'founded_year', 'artistic_director'])]
class Theater extends Model
{
    use SoftDeletes;

    public function entity()
    {
        return $this->morphOne(Entity::class, 'entityable');
    }
}
