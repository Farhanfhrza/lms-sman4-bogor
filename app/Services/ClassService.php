<?php

namespace App\Services;

use App\Models\User;
use App\Models\SchoolClass;
use App\Models\StudentClass;
use App\Models\ClassSubject;
use App\Models\AcademicYear;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ClassService
{
    /**
     * Get class subjects for a specific user based on their role and active academic year.
     *
     * @param User $user
     * @param int|null $academicYearId Filter by specific academic year (optional)
     * @param string|null $search Search query for subject/class name
     * @param string|null $sortBy Sort field (name, date)
     * @return Collection
     */
    public function getClassSubjectsForUser(
        User $user, 
        ?int $academicYearId = null,
        ?string $search = null,
        ?string $sortBy = 'name'
    ): Collection {
        try {
            // Get active academic year ID if not provided
            if (!$academicYearId) {
                $academicYearId = AcademicYear::where('is_active', true)->value('id');
            }

            if ($user->hasRole('admin')) {
                // Admin: Get all class subjects
                return $this->getAdminClassSubjects($academicYearId, $search, $sortBy);
            }

            if ($user->hasRole('teacher')) {
                // Teacher: Get class subjects they teach
                return $this->getTeacherClassSubjects($user, $academicYearId, $search, $sortBy);
            }

            if ($user->hasRole('student')) {
                // Student: Get class subjects from their enrolled class
                return $this->getStudentClassSubjects($user, $academicYearId, $search, $sortBy);
            }

            return collect();

        } catch (\Exception $e) {
            Log::error('Error fetching class subjects for user: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);
            return collect();
        }
    }

    /**
     * Get class subjects for admin
     */
    protected function getAdminClassSubjects(?int $academicYearId, ?string $search, string $sortBy): Collection
    {
        $query = ClassSubject::with(['subject', 'schoolClass', 'teacher.user', 'academicYear']);

        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }

        if ($search) {
            $searchLower = strtolower($search);
            $query->where(function($q) use ($searchLower) {
                $q->whereHas('subject', function($subQ) use ($searchLower) {
                    $subQ->whereRaw('LOWER(name) LIKE ?', ["%{$searchLower}%"]);
                })->orWhereHas('schoolClass', function($classQ) use ($searchLower) {
                    $classQ->whereRaw('LOWER(name) LIKE ?', ["%{$searchLower}%"]);
                });
            });
        }

        // Get results
        $results = $query->get();

        // Apply sorting on collection to avoid JOIN conflicts
        if ($sortBy === 'name') {
            $results = $results->sortBy(function($classSubject) {
                return $classSubject->subject->name ?? '';
            })->values();
        } else {
            // Sort by latest (already handled by latest() in query, but for consistency)
            $results = $results->sortByDesc('created_at')->values();
        }

        return $results;
    }

    /**
     * Get class subjects for teacher
     */
    protected function getTeacherClassSubjects(User $user, ?int $academicYearId, ?string $search, string $sortBy): Collection
    {
        if (!$user->teacher) {
            return collect();
        }

        $query = ClassSubject::where('teacher_id', $user->teacher->id)
            ->with(['subject', 'schoolClass', 'academicYear']);

        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }

        if ($search) {
            $searchLower = strtolower($search);
            $query->where(function($q) use ($searchLower) {
                $q->whereHas('subject', function($subQ) use ($searchLower) {
                    $subQ->whereRaw('LOWER(name) LIKE ?', ["%{$searchLower}%"]);
                })->orWhereHas('schoolClass', function($classQ) use ($searchLower) {
                    $classQ->whereRaw('LOWER(name) LIKE ?', ["%{$searchLower}%"]);
                });
            });
        }

        // Get results
        $results = $query->get();

        // Apply sorting on collection to avoid JOIN conflicts
        if ($sortBy === 'name') {
            $results = $results->sortBy(function($classSubject) {
                return $classSubject->subject->name ?? '';
            })->values();
        } else {
            $results = $results->sortByDesc('created_at')->values();
        }

        return $results;
    }

    /**
     * Get class subjects for student based on their enrolled class
     */
    protected function getStudentClassSubjects(User $user, ?int $academicYearId, ?string $search, string $sortBy): Collection
    {
        if (!$user->student) {
            return collect();
        }

        // Get student's current class
        $studentClassQuery = StudentClass::where('student_id', $user->student->id);
        
        if ($academicYearId) {
            $studentClassQuery->where('academic_year_id', $academicYearId);
        }

        $studentClass = $studentClassQuery->first();

        if (!$studentClass) {
            return collect();
        }

        // Get all class subjects for this class
        $query = ClassSubject::where('class_id', $studentClass->class_id)
            ->where('academic_year_id', $studentClass->academic_year_id)
            ->with(['subject', 'schoolClass', 'teacher.user', 'academicYear']);

        if ($search) {
            $searchLower = strtolower($search);
            $query->where(function($q) use ($searchLower) {
                $q->whereHas('subject', function($subQ) use ($searchLower) {
                    $subQ->whereRaw('LOWER(name) LIKE ?', ["%{$searchLower}%"]);
                });
            });
        }

        // Get results
        $results = $query->get();

        // Apply sorting on collection to avoid JOIN conflicts
        if ($sortBy === 'name') {
            $results = $results->sortBy(function($classSubject) {
                return $classSubject->subject->name ?? '';
            })->values();
        } else {
            $results = $results->sortByDesc('created_at')->values();
        }

        return $results;
    }

    /**
     * Get available academic years for dropdown
     */
    public function getAcademicYears(): Collection
    {
        return AcademicYear::orderBy('is_active', 'desc')
            ->orderBy('name', 'desc')
            ->get();
    }

    /**
     * Get available classes for a student (for filtering)
     */
    public function getStudentClasses(User $user): Collection
    {
        if (!$user->student) {
            return collect();
        }

        return StudentClass::where('student_id', $user->student->id)
            ->with(['schoolClass', 'academicYear'])
            ->get();
    }

    /**
     * Get detailed information for a specific class subject with caching.
     * Cache for 60 minutes to improve performance.
     *
     * @param int $classSubjectId
     * @return ClassSubject|null
     */
    public function getClassDetail(int $classSubjectId): ?ClassSubject
    {
        $cacheKey = "class_subject_detail_{$classSubjectId}";

        return Cache::remember($cacheKey, 3600, function () use ($classSubjectId) {
            return ClassSubject::with([
                'subject',
                'schoolClass',
                'teacher.user',
                'academicYear',
                // Deep eager loading for sections and their content
                'sections' => function($query) {
                    $query->orderBy('order_number', 'asc');
                },
                'sections.materials' => function($query) {
                    $query->orderBy('order_number', 'asc');
                },
                'sections.assignments' => function($query) {
                    $query->orderBy('due_date', 'asc');
                },
                'sections.quizzes' => function($query) {
                    $query->orderBy('end_at', 'asc');
                },
            ])->find($classSubjectId);
        });
    }

    /**
     * Get classmates for a specific class subject.
     *
     * @param ClassSubject $classSubject
     * @return Collection
     */
    public function getClassmates(ClassSubject $classSubject): Collection
    {
        return StudentClass::where('class_id', $classSubject->class_id)
            ->where('academic_year_id', $classSubject->academic_year_id)
            ->with(['student.user'])
            ->get()
            ->sortBy(function($studentClass) {
                $num = $studentClass->attendance_number ?? 999;
                $name = $studentClass->student->user->full_name ?? '';
                return sprintf('%03d-%s', $num, $name);
            })
            ->map(function($studentClass) {
                return $studentClass->student->user ?? null;
            })->filter()->values();
    }

    /**
     * Calculate progress for a student in a class subject.
     *
     * @param User $user
     * @param ClassSubject $classSubject
     * @return float
     */
    public function calculateProgress(User $user, ClassSubject $classSubject): float
    {
        if (!$user->student || !$classSubject->sections) {
            return 0;
        }

        $totalItems = 0;
        $completedItems = 0;

        foreach ($classSubject->sections as $section) {
            // Count materials
            if ($section->materials) {
                $totalItems += $section->materials->count();
                $completedItems += $section->materials->filter(function($material) use ($user) {
                    return $material->materialProgress()
                        ->where('student_id', $user->student->id)
                        ->where('is_completed', true)
                        ->exists();
                })->count();
            }

            // Count assignments
            if ($section->assignments) {
                $totalItems += $section->assignments->count();
                $completedItems += $section->assignments->filter(function($assignment) use ($user) {
                    return $assignment->submissions()
                        ->where('student_id', $user->student->id)
                        ->exists();
                })->count();
            }

            // Count quizzes
            if ($section->quizzes) {
                $totalItems += $section->quizzes->count();
                $completedItems += $section->quizzes->filter(function($quiz) use ($user) {
                    return $quiz->attempts()
                        ->where('student_id', $user->student->id)
                        ->where('is_submitted', true)
                        ->exists();
                })->count();
            }
        }

        return $totalItems > 0 ? round(($completedItems / $totalItems) * 100, 2) : 0;
    }

    /**
     * Invalidate cache for a class subject.
     * Call this whenever materials, assignments, or quizzes are added/updated.
     *
     * @param int $classSubjectId
     * @return void
     */
    public function invalidateClassCache(int $classSubjectId): void
    {
        Cache::forget("class_subject_detail_{$classSubjectId}");
    }
}
