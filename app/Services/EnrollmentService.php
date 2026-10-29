<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EnrollmentService
{
    public function enroll(int $studentId, int $courseId, int $yearId): void
    {
        $exists = DB::table('enrollments')
            ->where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->where('academic_year_id', $yearId)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'student_id' => 'Este estudiante ya está inscrito en el curso.',
            ]);
        }

        DB::table('enrollments')->insert([
            'student_id' => $studentId,
            'course_id' => $courseId,
            'academic_year_id' => $yearId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function unenroll(int $enrollmentId): int
    {
        return DB::transaction(function () use ($enrollmentId): int {
            $enrollment = DB::table('enrollments')->where('id', $enrollmentId)->lockForUpdate()->firstOrFail();

            if (DB::table('grades')->where('enrollment_id', $enrollmentId)->exists()) {
                throw ValidationException::withMessages([
                    'enrollment' => 'No se puede retirar una inscripción que ya tiene notas registradas.',
                ]);
            }

            DB::table('enrollments')->where('id', $enrollmentId)->delete();

            return (int) $enrollment->course_id;
        });
    }
}
