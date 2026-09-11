<?php

namespace App\Policies;

use App\Models\Contact;
use App\Models\User;

class ContactPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Semua user aktif bisa melihat list kontak
    }

    public function view(User $user, Contact $contact): bool
    {
        return true; // Read-only access diperbolehkan untuk seluruh database
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Contact $contact): bool
    {
        return $user->isMaster() || $user->id === $contact->owner_id;
    }

    public function delete(User $user, Contact $contact): bool
    {
        return $user->isMaster() || $user->id === $contact->owner_id;
    }

    public function transfer(User $user): bool
    {
        return $user->isMaster();
    }
}
