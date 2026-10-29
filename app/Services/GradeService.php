<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class GradeService
{
    /** @throws ValidationException */
    public function validateComponents(array $components): array
    {
        $validated = Validator::make($components, [
            'ser' => ['required', 'numeric', 'decimal:0,1', 'min:0', 'max:10'],
            'saber' => ['required', 'numeric', 'decimal:0,1', 'min:0', 'max:45'],
            'hacer' => ['required', 'numeric', 'decimal:0,1', 'min:0', 'max:40'],
            'autoevaluacion' => ['required', 'numeric', 'decimal:0,1', 'min:0', 'max:5'],
        ])->validate();

        return $validated;
    }

    public function finalScore(array $components): float
    {
        return round(array_sum($components), 1);
    }

    public function saveBatch(int $courseId, int $periodId, int $yearId, array $rows): int
    {
        $periodExists = DB::table('periods')->where('id', $periodId)
            ->where('academic_year_id', $yearId)->exists();

        if (! $periodExists) {
            throw ValidationException::withMessages(['period_id' => 'El trimestre no pertenece a la gestión activa.']);
        }

        $enrollmentIds = array_map(static fn (array $row): int => (int) $row['enrollment_id'], $rows);
        $validEnrollmentCount = DB::table('enrollments')
            ->where('course_id', $courseId)
            ->where('academic_year_id', $yearId)
            ->whereIn('id', $enrollmentIds)
            ->count();

        if ($validEnrollmentCount !== count($enrollmentIds)) {
            throw ValidationException::withMessages(['grades' => 'Una o más filas no pertenecen a este curso.']);
        }

        return DB::transaction(function () use ($rows, $periodId): int {
            $records = [];

            foreach ($rows as $index => $row) {
                $components = Arr::only($row, ['ser', 'saber', 'hacer', 'autoevaluacion']);
                $components = array_map(static fn ($value) => $value === '' ? null : $value, $components);
                $filled = array_filter($components, static fn ($value): bool => $value !== null);

                if ($filled === []) {
                    continue;
                }

                if (count($filled) !== 4) {
                    throw ValidationException::withMessages([
                        "grades.{$index}.ser" => 'Completa los cuatro componentes o deja toda la fila vacía.',
                    ]);
                }

                $validated = $this->validateComponents($components);
                $records[] = [
                    'enrollment_id' => (int) $row['enrollment_id'],
                    'period_id' => $periodId,
                    ...$validated,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if ($records !== []) {
                DB::table('grades')->upsert(
                    $records,
                    ['enrollment_id', 'period_id'],
                    ['ser', 'saber', 'hacer', 'autoevaluacion', 'updated_at'],
                );
            }

            return count($records);
        });
    }
}
