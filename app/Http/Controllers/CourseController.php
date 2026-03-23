<?php

namespace App\Http\Controllers;

use App\Models\ClassSubject;
use App\Services\ClassService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CourseController extends Controller
{
    use AuthorizesRequests;

    /**
     * @var ClassService
     */
    protected $classService;

    /**
     * Create a new controller instance.
     *
     * @param ClassService $classService
     */
    public function __construct(ClassService $classService)
    {
        $this->classService = $classService;
    }

    /**
     * Display a listing of courses.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        // Get filter parameters
        $academicYearId = $request->get('academic_year');
        $search = $request->get('search');
        $sortBy = $request->get('sort_by', 'name');

        // Get courses with filters
        $courses = $this->classService->getClassSubjectsForUser(
            $user, 
            $academicYearId, 
            $search, 
            $sortBy
        );

        // Get academic years for dropdown
        $academicYears = $this->classService->getAcademicYears();

        return view('courses.index', compact('courses', 'academicYears', 'search', 'sortBy'));
    }

    /**
     * Display the specified course.
     *
     * @param ClassSubject $course Route model binding with slug
     * @return View
     */
    public function show(ClassSubject $course): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        // Authorization check
        $this->authorize('view', $course);

        // Get classmates
        $classmates = $this->classService->getClassmates($course);

        // Calculate progress for students
        $progress = 0;
        if ($user->hasRole('student')) {
            $progress = $this->classService->calculateProgress($user, $course);
        }

        // Breadcrumb data (English)
        $breadcrumbs = [
            ['label' => $course->subject->name ?? 'Course Detail'],
        ];

        return view('courses.show', compact('course', 'classmates', 'progress', 'breadcrumbs'));
    }
}
