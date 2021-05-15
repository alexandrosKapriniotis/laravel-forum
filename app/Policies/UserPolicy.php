<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @param User $signedInUser
     * @return bool
     */
    public function update(User $user, User $signedInUser): bool
    {
        return $signedInUser->id == $user->id;
    }
}
