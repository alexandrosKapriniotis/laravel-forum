<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{

    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        'App\Models\Thread' => 'App\Policies\ThreadPolicy',
        'App\Models\Reply'  => 'App\Policies\ReplyPolicy',
        'App\Models\User'  => 'App\Policies\UserPolicy'
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

//        Gate::before(function ($user){
//            if ($user->name === 'Αλεξανδρος Καπρινιωτης') return true;
//        });
    }
}
