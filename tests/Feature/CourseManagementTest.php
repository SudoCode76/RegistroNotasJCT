<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CourseManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_create_a_course_and_enroll_a_student_once(): void
    {
        $this->actingAs(User::factory()->create());
        $subjectId = (int) DB::table('subjects')->where('code', 'FIS')->value('id');

        $this->post(route('courses.store'), [
            'subject_id' => $subjectId,
            'name' => '3ro Secundaria A',
            'level' => 'Secundaria',
        ])->assertRedirect();

        $courseId = (int) DB::table('courses')->where('subject_id', $subjectId)
            ->where('name', '3ro Secundaria A')->value('id');
        $yearId = (int) DB::table('academic_years')->where('year', 2026)->value('id');
        $now = now();
        $studentId = DB::table('students')->insertGetId([
            'rude' => 'DEMO-RUDE-2001',
            'first_name' => 'Estudiante',
            'last_name' => 'Curso',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->post(route('enrollments.store'), [
            'course_id' => $courseId,
            'student_id' => $studentId,
        ])->assertRedirect(route('courses.show', $courseId));

        $this->assertDatabaseHas('enrollments', [
            'academic_year_id' => $yearId,
            'course_id' => $courseId,
            'student_id' => $studentId,
        ]);

        $this->from(route('courses.show', $courseId))
            ->post(route('enrollments.store'), ['course_id' => $courseId, 'student_id' => $studentId])
            ->assertSessionHasErrors('student_id');

        $this->assertDatabaseCount('enrollments', 1);

        $this->from(route('courses.index'))
            ->delete(route('courses.destroy', $courseId))
            ->assertSessionHasErrors('course');

        $this->assertDatabaseHas('courses', ['id' => $courseId]);
    }
}
