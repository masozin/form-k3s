<?php
// app/Policies/FormSubmissionPolicy.php

namespace App\Policies;

use App\Models\FormSubmission;
use App\Models\User;

class FormSubmissionPolicy
{
    public function update(User $user, FormSubmission $submission): bool
    {
        return $submission->user_id === $user->id;
    }

    public function view(User $user, FormSubmission $submission): bool
    {
        return $submission->user_id === $user->id;
    }
}
