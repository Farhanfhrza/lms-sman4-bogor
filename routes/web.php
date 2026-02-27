<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\TeacherCourseController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AcademicEventController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\AdminLogController;
use App\Http\Controllers\AdminTeacherController;
use App\Http\Controllers\AdminStudentController;
use App\Http\Controllers\StudentQuizController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

// Root route (/) - Login page for guests, redirect to dashboard if authenticated
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('login');

// Handle login POST
Route::post('/', [AuthenticatedSessionController::class, 'store'])->middleware('guest');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'check.email'])->name('dashboard');

Route::middleware(['auth', 'check.email'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Course Management Routes (Professional RESTful naming)
    Route::resource('courses', CourseController::class)->only(['index', 'show']);

    // Material Routes
    Route::prefix('materials')->name('materials.')->group(function () {
        Route::get('/{material}', [MaterialController::class, 'show'])->name('show');
        Route::post('/{material}/complete', [MaterialController::class, 'markComplete'])->name('complete');
    });

    // Assignment Routes
    Route::prefix('assignments')->name('assignments.')->group(function () {
        Route::get('/{assignment}', [AssignmentController::class, 'show'])->name('show');
        Route::post('/{assignment}/submit', [AssignmentController::class, 'submit'])->name('submit');
    });

    // Student Quiz Routes (preview/start nested under course)
    Route::prefix('courses/{course}/quizzes')->name('student.quiz.')->group(function () {
        Route::get('/{quiz}', [StudentQuizController::class, 'show'])->name('show');
        Route::post('/{quiz}/start', [StudentQuizController::class, 'start'])->name('start');
    });
    // Quiz attempt routes (UUID-based for security)
    Route::prefix('quiz-attempts')->name('student.quiz.')->group(function () {
        Route::get('/{attempt}', [StudentQuizController::class, 'take'])->name('take');
        Route::post('/{attempt}/answer', [StudentQuizController::class, 'saveAnswer'])->name('saveAnswer');
        Route::post('/{attempt}/submit', [StudentQuizController::class, 'submit'])->name('submit');
    });

    // Academic Calendar Routes
    Route::prefix('academic-calendar')->name('academic-calendar.')->group(function () {
        Route::get('/', [AcademicEventController::class, 'index'])->name('index');
        Route::get('/events-for-date', [AcademicEventController::class, 'eventsForDate'])->name('events-for-date');
        Route::get('/events-for-month', [AcademicEventController::class, 'eventsForMonth'])->name('events-for-month');
        Route::post('/', [AcademicEventController::class, 'store'])->name('store');
        Route::put('/{event}', [AcademicEventController::class, 'update'])->name('update');
        Route::delete('/{event}', [AcademicEventController::class, 'destroy'])->name('destroy');
    });

    // Backward compatibility redirect (optional - remove after migration)
    Route::get('/kelas', function () {
        return redirect()->route('courses.index');
    });
    Route::get('/kelas/{any}', function ($any) {
        return redirect()->route('courses.index');
    });

    // --- Admin Only ---
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/activity-logs', [AdminLogController::class, 'index'])->name('activity-logs');

        // Teacher Management
        Route::get('/teachers', [AdminTeacherController::class, 'index'])->name('teachers.index');
        Route::get('/teachers/create', [AdminTeacherController::class, 'create'])->name('teachers.create');
        Route::post('/teachers', [AdminTeacherController::class, 'store'])->name('teachers.store');
        Route::get('/teachers/{teacher}/edit', [AdminTeacherController::class, 'edit'])->name('teachers.edit');
        Route::put('/teachers/{teacher}', [AdminTeacherController::class, 'update'])->name('teachers.update');
        Route::delete('/teachers/{teacher}', [AdminTeacherController::class, 'destroy'])->name('teachers.destroy');

        // Student Management
        Route::get('/students', [AdminStudentController::class, 'index'])->name('students.index');
        Route::get('/students/create', [AdminStudentController::class, 'create'])->name('students.create');
        Route::post('/students', [AdminStudentController::class, 'store'])->name('students.store');
        Route::post('/students/import', [AdminStudentController::class, 'import'])->name('students.import');
        Route::get('/students/{student}/edit', [AdminStudentController::class, 'edit'])->name('students.edit');
        Route::put('/students/{student}', [AdminStudentController::class, 'update'])->name('students.update');
        Route::delete('/students/{student}', [AdminStudentController::class, 'destroy'])->name('students.destroy');
    });

    // --- Teacher Course Management ---
    Route::middleware(['role:admin|teacher'])->group(function () {
        Route::prefix('manage/courses/{course}')->name('manage.courses.')->group(function () {
            // Course manage page
            Route::get('/', [TeacherCourseController::class, 'manage'])->name('show');
            Route::put('/info', [TeacherCourseController::class, 'updateInfo'])->name('update-info');

            // Sections (BAB) CRUD
            Route::post('/sections', [TeacherCourseController::class, 'storeSection'])->name('sections.store');
            Route::put('/sections/{section}', [TeacherCourseController::class, 'updateSection'])->name('sections.update');
            Route::delete('/sections/{section}', [TeacherCourseController::class, 'deleteSection'])->name('sections.destroy');

            // Materials CRUD
            Route::get('/materials/create', [MaterialController::class, 'create'])->name('materials.create');
            Route::post('/materials', [MaterialController::class, 'store'])->name('materials.store');
            Route::get('/materials/{material}/edit', [MaterialController::class, 'edit'])->name('materials.edit');
            Route::put('/materials/{material}', [MaterialController::class, 'update'])->name('materials.update');
            Route::delete('/materials/{material}', [MaterialController::class, 'destroy'])->name('materials.destroy');

            // Assignments CRUD
            Route::get('/assignments/create', [AssignmentController::class, 'create'])->name('assignments.create');
            Route::post('/assignments', [AssignmentController::class, 'store'])->name('assignments.store');
            Route::get('/assignments/{assignment}/edit', [AssignmentController::class, 'edit'])->name('assignments.edit');
            Route::put('/assignments/{assignment}', [AssignmentController::class, 'update'])->name('assignments.update');
            Route::delete('/assignments/{assignment}', [AssignmentController::class, 'destroy'])->name('assignments.destroy');

            // Assignment Grading / Submissions
            Route::get('/assignments/{assignment}/submissions', [AssignmentController::class, 'submissions'])->name('assignments.submissions');
            Route::get('/assignments/{assignment}/submissions/{student}', [AssignmentController::class, 'showSubmission'])->name('assignments.submissions.show');
            Route::put('/assignments/{assignment}/submissions/{student}/grade', [AssignmentController::class, 'gradeSubmission'])->name('assignments.submissions.grade');

            // Quizzes CRUD
            Route::get('/quizzes/create', [QuizController::class, 'create'])->name('quizzes.create');
            Route::post('/quizzes', [QuizController::class, 'store'])->name('quizzes.store');
            Route::get('/quizzes/{quiz}/edit', [QuizController::class, 'edit'])->name('quizzes.edit');
            Route::put('/quizzes/{quiz}', [QuizController::class, 'update'])->name('quizzes.update');
            Route::delete('/quizzes/{quiz}', [QuizController::class, 'destroy'])->name('quizzes.destroy');
        });
    });
});

require __DIR__.'/auth.php';
