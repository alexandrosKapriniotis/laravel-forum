<?php


namespace App\Traits;


use App\Models\Activity;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;
use ReflectionClass;

trait RecordsActivity
{
    /**
     * Extends boot method of Thread.
     */
    protected static function bootRecordsActivity()
    {
        if (auth()->guest()) return;

        foreach (static::getActivitiesToRecord() as $event){
            static::$event(function($model) use ($event) {
                $model->recordActivity($event);
            });
        }

        static::deleting(function ($model){
            $model->activity()->delete();
        });

    }

    /**
     * @return string[]
     */
    protected static function getActivitiesToRecord(): array
    {
        return ['created'];
    }

    /**
     * Get the activity type
     *
     * @param $event
     * @return string
     */
    protected function getActivityType($event): string
    {
        $type = strtolower((new ReflectionClass($this))->getShortName());

        return "{$event}_{$type}";
    }

    /**
     * @return MorphMany
     */
    public function activity()
    {
        return $this->morphMany('App\Models\Activity','subject');
    }

    /**
     * Record thread activity.
     * @param $event
     */
    protected function recordActivity($event)
    {
        $this->activity()->create([
            'user_id' => (int)auth()->id(),
            'type'    => $this->getActivityType($event)
        ]);
    }
}
