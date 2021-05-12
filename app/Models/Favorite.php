<?php

namespace App\Models;

use App\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;
    use RecordsActivity;

    protected $guarded = [];

    /**
     * Return the relationship.
     *
     */
    public function favorited()
    {
        return $this->morphTo();
    }
}
