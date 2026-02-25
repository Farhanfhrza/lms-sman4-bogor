<?php

namespace App\Policies;

use App\Models\AcademicEvent;
use App\Models\User;

class AcademicEventPolicy
{
    /**
     * Determine if the user can view academic events (calendar).
     * All authenticated users may view.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can create academic events.
     * Only admin and teachers are allowed.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('teacher');
    }

    /**
     * Determine if the user can update an academic event.
     * Admin can update any; teachers can update only their own.
     */
    public function update(User $user, AcademicEvent $event): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return $user->hasRole('teacher') && $event->created_by === $user->id;
    }

    /**
     * Determine if the user can delete an academic event.
     * Same rules as update.
     */
    public function delete(User $user, AcademicEvent $event): bool
    {
        return $this->update($user, $event);
    }
}
