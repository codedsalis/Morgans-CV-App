<?php

namespace App\Policies;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use App\Options\Role;

class ProfilePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function create(User $user)
    {
        return $user->role === Role::USER ? Response::allow()
            : Response::deny('Sorry, you are not a user');;
    }

    public function update(User $user, Profile $profile): Response
    {
        return $user->id === $profile->user_id
            ? Response::allow()
            : Response::deny('You cannot modify someone else\'s resume');
    }

    public function view(User $user, Profile $profile)
    {
        return $user->id === $profile->user_id
            ? Response::allow()
            : Response::deny('You cannot view someone else\'s resume');
    }
}
