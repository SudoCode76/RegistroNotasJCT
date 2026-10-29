<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StudentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_register_edit_and_find_a_student(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('students.store'), [
            'rude' => 'DEMO-RUDE-1001',
            'ci' => 'DEMO-CI-1001',
            'first_name' => 'Estudiante',
            'last_name' => 'Ejemplo',
        ])->assertRedirect(route('students.index'));

        $studentId = (int) DB::table('students')->where('rude', 'DEMO-RUDE-1001')->value('id');

        $this->assertDatabaseHas('students', [
            'id' => $studentId,
            'first_name' => 'Estudiante',
            'last_name' => 'Ejemplo',
        ]);

        $this->put(route('students.update', $studentId), [
            'rude' => 'DEMO-RUDE-1001',
            'ci' => 'DEMO-CI-1001',
            'first_name' => 'Nombre actualizado',
            'last_name' => 'Ejemplo',
        ])->assertRedirect(route('students.index'));

        $this->get(route('students.index', ['search' => 'DEMO-RUDE-1001']))
            ->assertOk()
            ->assertSee('Nombre actualizado');
    }

    public function test_student_with_enrollment_cannot_be_deleted(): void
    {
        $user = User::factory()->create();
        $yearId = (int) DB::table('academic_years')->where('year', 2026)->value('id');
        $subjectId = (int) DB::table('subjects')->where('code', 'FIS')->value('id');
        $now = now();
        $courseId = DB::table('courses')->insertGetId([
            'subject_id' => $subjectId,
            'name' => 'Curso protegido',
            'level' => 'Secundaria',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $studentId = DB::table('students')->insertGetId([
            'rude' => 'DEMO-RUDE-1002',
            'first_name' => 'Alumno',
            'last_name' => 'Inscrito',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('enrollments')->insert([
            'academic_year_id' => $yearId,
            'course_id' => $courseId,
            'student_id' => $studentId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->actingAs($user)
            ->delete(route('students.destroy', $studentId))
            ->assertSessionHasErrors('student');

        $this->assertDatabaseHas('students', ['id' => $studentId]);
    }
}
