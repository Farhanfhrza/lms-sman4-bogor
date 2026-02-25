# LMS Migration and Model Setup

All requested migrations and models have been created successfully based on the provided schema.

## 1. Core System
- **Users**: Modified `users` table to add `login_identifier` (unique), `full_name`, `is_active`, and `softDeletes`.
- **Roles**: Created `Role` model and `roles` table.
- **UserRoles**: Created `user_roles` pivot table.

## 2. Academic Structure
- **AcademicYear**: Created model and table.
- **Classes**: Created `SchoolClass` model (mapped to `classes` table).
- **Teachers**: Created `Teacher` model and table.
- **Students**: Created `Student` model and table.
- **HomeroomTeachers**: Created model and table.
- **StudentClasses**: Created model and table.

## 3. Curriculum & Content
- **Subjects**: Created `Subject` model.
- **ClassSubjects**: Created `ClassSubject` model.
- **ClassSubjectSections**: Created sections/chapters structure.
- **Materials, Assignments, Quizzes**: Created models with publication settings.

## 4. Assessment & Submission
- **AssignmentSubmission**: Created model.
- **Quiz Structure**: `QuizQuestion`, `QuizOption`.
- **Quiz Execution**: `QuizAttempt`, `QuizAnswer`.

## 5. Tracking & Progress
- **Attendance**: `StudentAttendance`, `TeacherAttendance` (+ Recaps).
- **Progress**: `MaterialProgress`, `QuizProgress`, `SectionProgress`, `ClassSubjectProgress`.
- **Grades**: `ClassSubjectGrade`.
- **Trait**: Added `CalculatesProgress` trait to helper models.

## 6. Miscellaneous
- **Notifications, Announcements, Events**.
- **TeacherSurvey** related models.
- **Reports, ActivityLogs**.

## Instructions
1. Configure your `.env` file with correct database credentials.
2. Run migrations:
   ```sh
   php artisan migrate
   ```
