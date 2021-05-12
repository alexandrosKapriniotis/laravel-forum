<?php


namespace App\Traits;


use App\Models\Favorite;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Favoritable
{
    /**
     *
     */
    protected static function bootFavoritable()
    {
        static::deleting(function ($model) {
            $model->favorites->each->delete();
        });
    }

    /**
     * One to many relationship with favorite.
     * @return MorphMany
     */
    public function favorites()
    {
        return $this->morphMany(Favorite::class,'favorited');
    }

    /**
     * Favorite a reply.
     * @return mixed
     */
    public function favorite()
    {
        $attributes = ['user_id' => auth()->id()];

        if (! $this->favorites()->where($attributes)->exists()){
            return $this->favorites()->create($attributes);
        }
    }

    /**
     * Unfavorite a reply.
     */
    public function unfavorite()
    {
        $attributes = ['user_id' => auth()->id()];

        $this->favorites()->where($attributes)->get()->each->delete();
    }

    /**
     * Check if reply is favorited.
     * @return mixed
     */
    public function isFavorited(): bool
    {
        return !! $this->favorites->where('user_id',auth()->id())->count();
    }

    /** Get the is favorited attribute
     *
     * @return bool|mixed
     */
    public function getIsFavoritedAttribute()
    {
        return $this->isFavorited();
    }

    /**
     * Get favorites count.
     * @return int
     */
    public function getFavoritesCountAttribute(): int
    {
        return $this->favorites->count();
    }
}
