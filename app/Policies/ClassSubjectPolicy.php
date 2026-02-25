<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ClassSubject;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClassSubjectPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view the class subject details.
     *
     * @param User $user
     * @param ClassSubject $classSubject
     * @return bool
     */
    public function view(User $user, ClassSubject $classSubject): bool
    {
        // Admin can view all
        if ($user->hasRole('admin')) {
            return true;
        }

        // Teacher can view if they teach this class subject
        if ($user->hasRole('teacher') && $user->teacher) {
            return $classSubject->teacher_id === $user->teacher->id;
        }

        // Student can view if they are enrolled in this class
        if ($user->hasRole('student') && $user->student) {
            return $user->student->studentClasses()
                ->where('class_id', $classSubject->class_id)
                ->where('academic_year_id', $classSubject->academic_year_id)
                ->exists();
        }

        return false;
    }

    /**
     * Determine if the user can update the class subject.
     *
     * @param User $user
     * @param ClassSubject $classSubject
     * @return bool
     */
    public function update(User $user, ClassSubject $classSubject): bool
    {
        // Admin can update all
        if ($user->hasRole('admin')) {
            return true;
        }

        // Teacher can update if they teach this class subject
        if ($user->hasRole('teacher') && $user->teacher) {
            return $classSubject->teacher_id === $user->teacher->id;
        }

        return false;
    }

    /**
     * Determine if the user can delete the class subject.
     *
     * @param User $user
     * @param ClassSubject $classSubject
     * @return bool
     */
    public function delete(User $user, ClassSubject $classSubject): bool
    {
        // Only admin can delete
        return $user->hasRole('admin');
    }
}
