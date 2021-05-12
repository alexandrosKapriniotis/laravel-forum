<?php

namespace App\Models;

use App\Events\ThreadHasNewReply;
use App\Events\ThreadReceivedNewReply;
use App\Notifications\ThreadWasUpdated;
use App\Traits\RecordsActivity;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Database\Factories\ThreadFactory;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Thread
 *
 * @property int $id
 * @property int $user_id
 * @property int $channel_id
 * @property string $title
 * @property string $body
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Activity[] $activity
 * @property-read int|null $activity_count
 * @property-read Channel $channel
 * @property-read User $creator
 * @property-read int $reply_count
 * @property-read Collection|Reply[] $replies
 * @property-read int|null $replies_count
 * @method static ThreadFactory factory(...$parameters)
 * @method static Builder|Thread filter($filters)
 * @method static Builder|Thread newModelQuery()
 * @method static Builder|Thread newQuery()
 * @method static Builder|Thread query()
 * @method static Builder|Thread whereBody($value)
 * @method static Builder|Thread whereChannelId($value)
 * @method static Builder|Thread whereCreatedAt($value)
 * @method static Builder|Thread whereId($value)
 * @method static Builder|Thread whereTitle($value)
 * @method static Builder|Thread whereUpdatedAt($value)
 * @method static Builder|Thread whereUserId($value)
 * @mixin Eloquent
 */
class Thread extends Model
{
    use HasFactory;
    use RecordsActivity;

    protected $guarded = [];

    protected $with    = ['creator', 'channel'];

    protected $appends = ['isSubscribedTo'];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function($thread) {
            $thread->replies->each->delete();
        });
    }

    /**
     * @return string
     */
    public function path(): string
    {
        return "/threads/{$this->channel->slug}/{$this->id}";
    }

    /**
     * A thread may have many replies.
     *
     * @return HasMany
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Reply::class);
    }

    /**
     * Get the reply count.
     * @return int
     */
    public function getReplyCountAttribute(): int
    {
        return $this->replies()->count();
    }

    /**
     * A thread belongs to a creator.
     *
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }

    /**
     * A thread belongs to a channel.
     *
     * @return BelongsTo
     */
    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * Add a reply to the thread.
     *
     * @param Reply $reply
     * @return Model
     * @throws Exception
     */
    public function addReply(Reply $reply): Model
    {
        (new \App\Inspections\Spam)->detect($reply['body']);

        event(new ThreadReceivedNewReply($reply));

        return $reply;
    }

    /**
     * @param $query
     * @param $filters
     * @return mixed
     */
    public function scopeFilter($query, $filters){
        return $filters->apply($query);
    }

    /**
     * @param null $userId
     * @return Thread
     */
    public function subscribe($userId = null)
    {
        $this->subscriptions()->create([
            'user_id' => $userId ?: auth()->id()
        ]);

        return $this;
    }

    public function unsubscribe($userId = null)
    {
        $this->subscriptions()
            ->where('user_id',$userId ?: auth()->id())
            ->delete();
    }

    /**
     * @return HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    /** Set a custom property.
     *
     * @return bool
     */
    public function getIsSubscribedToAttribute()
    {
        return $this->subscriptions()
            ->where('user_id',auth()->id())
            ->exists();
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function hasUpdatesFor()
    {
        $key = sprintf("users.%s.visits.%s",auth()->id(),$this->id);

        return $this->updated_at > cache($key);
    }
}
