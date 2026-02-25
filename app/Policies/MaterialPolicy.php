<?php

namespace App\Policies;

use App\Models\Material;
use App\Models\User;
use App\Models\ClassSubject;

class MaterialPolicy
{
    /**
     * Determine if the user can view the material.
     */
    public function view(User $user, Material $material): bool
    {
        // Admin can view all
        if ($user->hasRole('admin')) {
            return true;
        }

        // Get the class subject through section
        $classSubject = $material->section->classSubject;

        // Teacher can view if they teach this class
        if ($user->hasRole('teacher') && $user->teacher) {
            return $classSubject->teacher_id === $user->teacher->id;
        }

        // Student can view if enrolled in the class
        if ($user->hasRole('student') && $user->student) {
            return $user->student->studentClasses()
                ->where('class_id', $classSubject->class_id)
                ->where('academic_year_id', $classSubject->academic_year_id)
                ->exists();
        }

        return false;
    }

    /**
     * Determine if the user can create materials in a class subject.
     */
    public function create(User $user, ClassSubject $classSubject): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('teacher') && $user->teacher) {
            return $classSubject->teacher_id === $user->teacher->id;
        }

        return false;
    }

    /**
     * Determine if the user can update the material.
     */
    public function update(User $user, Material $material): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        $classSubject = $material->section->classSubject;

        if ($user->hasRole('teacher') && $user->teacher) {
            return $classSubject->teacher_id === $user->teacher->id;
        }

        return false;
    }

    /**
     * Determine if the user can delete the material.
     */
    public function delete(User $user, Material $material): bool
    {
        return $this->update($user, $material);
    }

    /**
     * Determine if the user can mark material as complete.
     */
    public function markComplete(User $user, Material $material): bool
    {
        // Only students can mark materials as complete
        return $user->hasRole('student') && $this->view($user, $material);
    }
}
