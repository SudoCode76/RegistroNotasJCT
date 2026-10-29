<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StudentService
{
    public function store(array $data): int
    {
        return DB::table('students')->insertGetId([
            ...$data,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function update(int $studentId, array $data): void
    {
        DB::table('students')->where('id', $studentId)->update([
            ...$data,
            'updated_at' => now(),
        ]);
    }

    public function delete(int $studentId): void
    {
        if (DB::table('enrollments')->where('student_id', $studentId)->exists()) {
            throw ValidationException::withMessages([
                'student' => 'No se puede eliminar un estudiante con inscripciones. Retíralo de sus cursos primero.',
            ]);
        }

        DB::table('students')->where('id', $studentId)->delete();
    }
}
