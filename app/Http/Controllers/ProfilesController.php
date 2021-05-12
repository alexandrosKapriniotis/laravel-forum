<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;

class ProfilesController extends Controller
{

    /**
     * Show a user's profile
     * @param User $user
     * @return Application|Factory|View
     */
    public function show(User $user)
    {
        return view('profiles.show',[
            'profileUser' => $user,
            'activities'  => Activity::feed($user),
        ]);
    }

    /**
     * @param User $user
     * @return Collection
     */
    public function getActivity(User $user): Collection
    {

    }
}
