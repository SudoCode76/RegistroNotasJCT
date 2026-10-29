<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AcademicDemoSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $yearId = DB::table('academic_years')->where('year', 2026)->value('id');

        if (! $yearId) {
            $yearId = DB::table('academic_years')->insertGetId([
                'year' => 2026,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            DB::table('academic_years')->where('id', $yearId)->update(['is_active' => true, 'updated_at' => $now]);
        }

        $periodNames = ['Primer trimestre', 'Segundo trimestre', 'Tercer trimestre'];

        foreach ($periodNames as $index => $name) {
            DB::table('periods')->updateOrInsert(
                ['academic_year_id' => $yearId, 'number' => $index + 1],
                ['name' => $name, 'created_at' => $now, 'updated_at' => $now],
            );
        }

        $subjects = [
            ['name' => 'Física', 'code' => 'FIS'],
            ['name' => 'Biología', 'code' => 'BIO'],
        ];

        foreach ($subjects as $subject) {
            DB::table('subjects')->updateOrInsert(
                ['code' => $subject['code']],
                [...$subject, 'created_at' => $now, 'updated_at' => $now],
            );
        }

        $subjectIds = DB::table('subjects')->whereIn('code', ['FIS', 'BIO'])->pluck('id', 'code');
        $assignments = [];

        foreach (['FIS' => 'A', 'BIO' => 'B'] as $code => $division) {
            foreach (['1ro', '2do', '3ro', '4to', '5to', '6to'] as $grade) {
                $name = "{$grade} Secundaria {$division}";
                $assignments[] = ['subject_id' => $subjectIds[$code], 'code' => $code, 'name' => $name];
            }
        }

        DB::table('courses')->insertOrIgnore(array_map(
            static fn (array $course): array => [
                'subject_id' => $course['subject_id'],
                'name' => $course['name'],
                'level' => 'Secundaria',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            $assignments,
        ));

        $courseIds = DB::table('courses')
            ->whereIn('subject_id', $subjectIds->values())
            ->get(['id', 'subject_id', 'name'])
            ->keyBy(fn (object $course): string => $course->subject_id.'|'.$course->name);
        $studentRows = [];
        $rosters = [];
        $studentNumber = 0;

        foreach ($assignments as $courseIndex => $assignment) {
            $courseId = (int) $courseIds[$assignment['subject_id'].'|'.$assignment['name']]->id;
            $rosters[$courseId] = [];

            for ($index = 1; $index <= 10; $index++) {
                $studentNumber++;
                $rude = sprintf('DEMO2026%04d', $studentNumber);
                $rosterRow = [
                    'rude' => $rude,
                    'ci' => sprintf('DEMO-CI-%04d', $studentNumber),
                    'first_name' => sprintf('Estudiante %02d', $index),
                    'last_name' => sprintf('Curso %02d', $courseIndex + 1),
                ];
                $studentRows[] = [...$rosterRow, 'created_at' => $now, 'updated_at' => $now];
                $rosters[$courseId][] = $rosterRow;
            }
        }

        foreach (array_chunk($studentRows, 100) as $chunk) {
            DB::table('students')->insertOrIgnore($chunk);
        }

        $studentsByRude = DB::table('students')
            ->whereIn('rude', array_column($studentRows, 'rude'))
            ->pluck('id', 'rude');
        $enrollmentRows = [];

        foreach ($rosters as $courseId => $roster) {
            foreach ($roster as $student) {
                $enrollmentRows[] = [
                    'academic_year_id' => $yearId,
                    'course_id' => $courseId,
                    'student_id' => $studentsByRude[$student['rude']],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($enrollmentRows, 100) as $chunk) {
            DB::table('enrollments')->insertOrIgnore($chunk);
        }

        $enrollments = DB::table('enrollments')
            ->where('academic_year_id', $yearId)
            ->whereIn('course_id', array_keys($rosters))
            ->get(['id', 'course_id', 'student_id'])
            ->keyBy(fn (object $enrollment): string => $enrollment->course_id.':'.$enrollment->student_id);
        $patterns = [
            [7, 20, 17, 4], [7, 22, 18, 4], [8, 30, 25, 5], [10, 40, 35, 5], [6, 20, 18, 3],
            [9, 34, 32, 4], [8, 26, 24, 4], [10, 35, 38, 5], [5, 22, 20, 3], [9, 29, 23, 4],
        ];
        $gradeRows = [];
        $periodIds = DB::table('periods')->where('academic_year_id', $yearId)->pluck('id', 'number');

        foreach ($rosters as $courseId => $roster) {
            foreach ($roster as $index => $student) {
                $studentId = (int) $studentsByRude[$student['rude']];
                $enrollmentId = (int) $enrollments[$courseId.':'.$studentId]->id;

                foreach ([1, 2, 3] as $periodNumber) {
                    if ($index === 9 && $periodNumber === 3) {
                        continue;
                    }

                    $scores = $patterns[($index + $periodNumber - 1) % count($patterns)];
                    $gradeRows[] = [
                        'enrollment_id' => $enrollmentId,
                        'period_id' => $periodIds[$periodNumber],
                        'ser' => $scores[0],
                        'saber' => $scores[1],
                        'hacer' => $scores[2],
                        'autoevaluacion' => $scores[3],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        foreach (array_chunk($gradeRows, 100) as $chunk) {
            DB::table('grades')->insertOrIgnore($chunk);
        }
    }
}
