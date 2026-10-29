<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CourseService
{
    public function store(array $data): int
    {
        return DB::table('courses')->insertGetId([
            ...$data,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function update(int $courseId, array $data): void
    {
        $course = DB::table('courses')->where('id', $courseId)->firstOrFail();

        if ((int) $course->subject_id !== (int) $data['subject_id']
            && DB::table('enrollments')->where('course_id', $courseId)->exists()) {
            throw ValidationException::withMessages([
                'subject_id' => 'No se puede cambiar la materia de un curso que ya tiene inscripciones.',
            ]);
        }

        DB::table('courses')->where('id', $courseId)->update([
            ...$data,
            'updated_at' => now(),
        ]);
    }

    public function delete(int $courseId): void
    {
        if (DB::table('enrollments')->where('course_id', $courseId)->exists()) {
            throw ValidationException::withMessages([
                'course' => 'No se puede eliminar un curso con estudiantes inscritos.',
            ]);
        }

        DB::table('courses')->where('id', $courseId)->delete();
    }
}
