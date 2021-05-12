<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Activity extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return MorphTo
     */
    public function subject()
    {
        return $this->morphTo();
    }

    /**
     * @param $user
     * @param $take = 50
     * @return mixed
     */
    public static function feed($user, $take = 50)
    {
        return static::where('user_id',$user->id)
            ->latest()
            ->with('subject')
            ->take($take = 50)
            ->get()
            ->groupBy(function ($activity) {
            return $activity->created_at->format('Y-m-d');
        });
    }
}
