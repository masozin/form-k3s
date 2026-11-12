<?php
// app/Policies/SchoolPolicy.php

namespace App\Policies;

use App\Models\School;
use App\Models\User;

class SchoolPolicy
{
    public function view(User $user, School $school): bool
    {
        return $user->isAdmin() || $user->school_id === $school->id;
    }

    public function update(User $user, School $school): bool
    {
        return $user->isAdmin() || $user->school_id === $school->id;
    }
}