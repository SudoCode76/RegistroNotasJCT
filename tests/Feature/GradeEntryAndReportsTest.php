<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class GradeEntryAndReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_batch_grade_entry_drives_the_centralizer_and_strict_failure_report(): void
    {
        $this->actingAs(User::factory()->create());
        [$courseId, $periodId, $enrollments] = $this->courseWithEnrollments(3);

        $this->post(route('grades.store'), [
            'course_id' => $courseId,
            'period_id' => $periodId,
            'grades' => [
                ['enrollment_id' => $enrollments[0], 'ser' => 7, 'saber' => 22, 'hacer' => 17, 'autoevaluacion' => 4],
                ['enrollment_id' => $enrollments[1], 'ser' => 7, 'saber' => 22, 'hacer' => 18, 'autoevaluacion' => 4],
            ],
        ])->assertRedirect(route('grades.index', ['course_id' => $courseId, 'period_id' => $periodId]));

        $this->assertSame(50.0, (float) DB::table('grades')->where('enrollment_id', $enrollments[0])->value('final_score'));
        $this->assertSame(51.0, (float) DB::table('grades')->where('enrollment_id', $enrollments[1])->value('final_score'));

        $this->get(route('reports.centralizer', ['course_id' => $courseId, 'period_id' => $periodId]))
            ->assertOk()
            ->assertSee('REPROBADO')
            ->assertSee('APROBADO')
            ->assertSee('SIN NOTA');

        $this->get(route('reports.failed', ['course_id' => $courseId, 'period_id' => $periodId]))
            ->assertOk()
            ->assertSee('Estudiante 01')
            ->assertDontSee('Estudiante 02')
            ->assertDontSee('Estudiante 03');
    }

    public function test_grade_components_cannot_exceed_their_limits(): void
    {
        $this->actingAs(User::factory()->create());
        [$courseId, $periodId, $enrollments] = $this->courseWithEnrollments(1);

        $this->from(route('grades.index', ['course_id' => $courseId, 'period_id' => $periodId]))
            ->post(route('grades.store'), [
                'course_id' => $courseId,
                'period_id' => $periodId,
                'grades' => [[
                    'enrollment_id' => $enrollments[0],
                    'ser' => 10,
                    'saber' => 46,
                    'hacer' => 40,
                    'autoevaluacion' => 5,
                ]],
            ])
            ->assertSessionHasErrors('grades.0.saber');

        $this->assertDatabaseCount('grades', 0);
    }

    public function test_grade_entry_rejects_an_enrollment_from_another_course(): void
    {
        $this->actingAs(User::factory()->create());
        [$courseId] = $this->courseWithEnrollments(0, 'Curso destino');
        [, $periodId, $otherCourseEnrollments] = $this->courseWithEnrollments(1, 'Curso ajeno');

        $this->from(route('grades.index', ['course_id' => $courseId, 'period_id' => $periodId]))
            ->post(route('grades.store'), [
                'course_id' => $courseId,
                'period_id' => $periodId,
                'grades' => [[
                    'enrollment_id' => $otherCourseEnrollments[0],
                    'ser' => 10,
                    'saber' => 45,
                    'hacer' => 40,
                    'autoevaluacion' => 5,
                ]],
            ])
            ->assertSessionHasErrors('grades');

        $this->assertDatabaseCount('grades', 0);
    }

    private function courseWithEnrollments(int $studentCount, string $courseName = 'Curso de prueba'): array
    {
        $yearId = (int) DB::table('academic_years')->where('year', 2026)->value('id');
        $periodId = (int) DB::table('periods')->where('academic_year_id', $yearId)->where('number', 1)->value('id');
        $subjectId = (int) DB::table('subjects')->where('code', 'FIS')->value('id');
        $now = now();
        $courseId = DB::table('courses')->insertGetId([
            'subject_id' => $subjectId,
            'name' => $courseName,
            'level' => 'Secundaria',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $enrollmentIds = [];

        for ($index = 1; $index <= $studentCount; $index++) {
            $studentId = DB::table('students')->insertGetId([
                'rude' => sprintf('DEMO-GRADE-%s-%02d', str_replace(' ', '-', $courseName), $index),
                'first_name' => sprintf('Estudiante %02d', $index),
                'last_name' => 'Prueba',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $enrollmentIds[] = DB::table('enrollments')->insertGetId([
                'academic_year_id' => $yearId,
                'course_id' => $courseId,
                'student_id' => $studentId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return [$courseId, $periodId, $enrollmentIds];
    }
}
