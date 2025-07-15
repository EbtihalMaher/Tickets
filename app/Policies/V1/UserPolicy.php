<?php

namespace App\Policies\V1;

use App\Models\User;
use App\Permissions\V1\Abilities;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        
    }

    public function store(User $user)
    {
        return $user->tokenCan(Abilities::CreateUser);
    }

    public function replace(User $user, User $model)
    {
        return $user->tokenCan(Abilities::ReplaceUser);
    }

    public function update(User $user, User $model)
    {
        return $user->tokenCan(Abilities::UpdateUser); 
    }

    public function destroy(User $user, User $model)
    {
        return $user->tokenCan(Abilities::DeleteUser);
    }
}
