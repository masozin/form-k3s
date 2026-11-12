<?php
// app/Policies/FormPolicy.php

namespace App\Policies;

use App\Models\Form;
use App\Models\User;

class FormPolicy
{
    // Admin & operator boleh lihat daftar/buat form
    public function viewAny(User $user): bool
    {
        return $user->canManageForms();
    }

    public function view(User $user, Form $form): bool
    {
        return $user->isAdmin() || $form->school_id === $user->school_id;
    }

    public function create(User $user): bool
    {
        return $user->canManageForms();
    }

    public function update(User $user, Form $form): bool
    {
        return $user->isAdmin()
            || ($user->isOperator() && $form->school_id === $user->school_id);
    }

    public function delete(User $user, Form $form): bool
    {
        return $this->update($user, $form);
    }
}
