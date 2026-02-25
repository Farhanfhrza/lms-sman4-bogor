<?php

namespace App\Http\Controllers;

use App\Services\ClassService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ClassController extends Controller
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
     * Display a listing of the class subjects based on user role.
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

        // Get class subjects with filters
        $classSubjects = $this->classService->getClassSubjectsForUser(
            $user, 
            $academicYearId, 
            $search, 
            $sortBy
        );

        // Get academic years for dropdown
        $academicYears = $this->classService->getAcademicYears();

        return view('classes.index', compact('classSubjects', 'academicYears', 'search', 'sortBy'));
    }

    /**
     * Display the specified class subject.
     *
     * @param int $id
     * @return View
     */
    public function show($id): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        // Get class subject detail with caching
        $classSubject = $this->classService->getClassDetail($id);

        if (!$classSubject) {
            abort(404, 'Kelas tidak ditemukan');
        }

        // Authorization check
        $this->authorize('view', $classSubject);

        // Get classmates
        $classmates = $this->classService->getClassmates($classSubject);

        // Calculate progress for students
        $progress = 0;
        if ($user->hasRole('student')) {
            $progress = $this->classService->calculateProgress($user, $classSubject);
        }

        // Breadcrumb data
        $breadcrumbs = [
            ['label' => 'Kelas', 'url' => route('classes.index')],
            ['label' => $classSubject->subject->name ?? 'Detail Kelas'],
        ];

        return view('classes.show', compact('classSubject', 'classmates', 'progress', 'breadcrumbs'));
    }
}
