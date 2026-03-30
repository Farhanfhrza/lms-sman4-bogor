<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassSubject;
use App\Models\ClassSubjectSection;
use App\Models\Material;
use App\Models\Assignment;
use App\Models\Quiz;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeacherContentReplicationController extends Controller
{
    public function getTree(Request $request, ClassSubject $course)
    {
        $request->validate([
            'source_course_id' => 'required|exists:class_subjects,id'
        ]);

        // Ensure the source course belongs to the same teacher (unless Admin)
        $user = Auth::user();
        $query = ClassSubject::with([
            'sections' => function ($query) {
                $query->orderBy('order_number');
            },
            'sections.materials', 
            'sections.assignments', 
            'sections.quizzes'
        ])
        ->where('id', $request->source_course_id)
        ->where('subject_id', $course->subject_id);

        if (!$user->hasRole('admin') && $user->teacher) {
            $query->where('teacher_id', $user->teacher->id);
        }

        $sourceCourse = $query->firstOrFail();

        return response()->json([
            'sections' => $sourceCourse->sections
        ]);
    }

    public function import(Request $request, ClassSubject $course)
    {
        $request->validate([
            'source_course_id' => 'required|exists:class_subjects,id',
            'sections' => 'array',
            'materials' => 'array',
            'assignments' => 'array',
            'quizzes' => 'array',
        ]);

        $user = Auth::user();
        $query = ClassSubject::where('id', $request->source_course_id)
            ->where('subject_id', $course->subject_id);

        if (!$user->hasRole('admin') && $user->teacher) {
            $query->where('teacher_id', $user->teacher->id);
        }

        $sourceCourse = $query->firstOrFail();

        DB::beginTransaction();
        try {
            $sectionsToImport = $request->input('sections', []);
            $materialsToImport = $request->input('materials', []);
            $assignmentsToImport = $request->input('assignments', []);
            $quizzesToImport = $request->input('quizzes', []);

            // Loop through selected sections
            foreach ($sectionsToImport as $sourceSectionId) {
                $sourceSection = ClassSubjectSection::find($sourceSectionId);
                if (!$sourceSection || $sourceSection->class_subject_id != $sourceCourse->id) continue;

                // Create or find matching section in destination course
                $destSection = ClassSubjectSection::firstOrCreate(
                    [
                        'class_subject_id' => $course->id,
                        'title' => $sourceSection->title
                    ],
                    [
                        'description' => $sourceSection->description,
                        'order_number' => ClassSubjectSection::where('class_subject_id', $course->id)->max('order_number') + 1,
                        'is_published' => false // Always import as draft
                    ]
                );

                // Clone selected Materials in this section
                $sectionMaterials = Material::where('section_id', $sourceSection->id)
                    ->whereIn('id', $materialsToImport)->get();
                foreach ($sectionMaterials as $material) {
                    $newMaterial = $material->replicate();
                    $newMaterial->section_id = $destSection->id;
                    $newMaterial->slug = null; // Force new unique slug generation
                    $newMaterial->published_at = null; 
                    $newMaterial->save();
                }

                // Clone selected Assignments in this section
                $sectionAssignments = Assignment::where('section_id', $sourceSection->id)
                    ->whereIn('id', $assignmentsToImport)->get();
                foreach ($sectionAssignments as $assignment) {
                    $newAssignment = $assignment->replicate();
                    $newAssignment->section_id = $destSection->id;
                    $newAssignment->slug = null; // Force new unique slug generation
                    $newAssignment->due_date = null;
                    $newAssignment->save();
                }

                // Clone selected Quizzes in this section
                $sectionQuizzes = Quiz::where('section_id', $sourceSection->id)
                    ->whereIn('id', $quizzesToImport)->get();
                foreach ($sectionQuizzes as $quiz) {
                    $newQuiz = $quiz->replicate();
                    $newQuiz->section_id = $destSection->id;
                    $newQuiz->is_published = false;
                    $newQuiz->start_at = null;
                    $newQuiz->end_at = null;
                    $newQuiz->save();

                    // Deep Copy Questions and Options
                    foreach ($quiz->questions as $question) {
                        $newQuestion = $question->replicate();
                        $newQuestion->quiz_id = $newQuiz->id;
                        $newQuestion->save();

                        foreach ($question->options as $option) {
                            $newOption = $option->replicate();
                            $newOption->question_id = $newQuestion->id;
                            $newOption->save();
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Konten berhasil di-import sebagai Draf. Silakan atur batas waktu dan terbitkan konten.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal meng-import konten: ' . $e->getMessage());
        }
    }
}
