<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\User;
use App\Models\ClassSubject;

class AssignmentPolicy
{
    /**
     * Determine if the user can view the assignment.
     */
    public function view(User $user, Assignment $assignment): bool
    {
        // Admin can view all
        if ($user->hasRole('admin')) {
            return true;
        }

        // Get the class subject through section
        $classSubject = $assignment->section->classSubject;

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
     * Determine if the user can create assignments in a class subject.
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
     * Determine if the user can update the assignment.
     */
    public function update(User $user, Assignment $assignment): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        $classSubject = $assignment->section->classSubject;

        if ($user->hasRole('teacher') && $user->teacher) {
            return $classSubject->teacher_id === $user->teacher->id;
        }

        return false;
    }

    /**
     * Determine if the user can delete the assignment.
     */
    public function delete(User $user, Assignment $assignment): bool
    {
        return $this->update($user, $assignment);
    }

    /**
     * Determine if the user can submit the assignment.
     */
    public function submit(User $user, Assignment $assignment): bool
    {
        // Only students can submit
        if (!$user->hasRole('student')) {
            return false;
        }

        // Check if student can view (enrolled)
        if (!$this->view($user, $assignment)) {
            return false;
        }

        // Check if assignment is still open (before due date)
        if ($assignment->due_date && now()->isAfter($assignment->due_date)) {
            return false;
        }

        return true;
    }

    /**
     * Determine if the user can view submissions.
     */
    public function viewSubmissions(User $user, Assignment $assignment): bool
    {
        // Admin and teachers can view all submissions
        if ($user->hasRole('admin') || $user->hasRole('teacher')) {
            return $this->view($user, $assignment);
        }

        // Students can only view their own submission
        return $user->hasRole('student') && $this->view($user, $assignment);
    }
}
