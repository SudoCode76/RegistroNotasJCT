<?php

namespace App\Http\Controllers;

use App\Queries\SchoolQuery;
use App\Services\EnrollmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function store(Request $request, SchoolQuery $school, EnrollmentService $enrollments): RedirectResponse
    {
        $data = $request->validate([
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'student_id' => ['required', 'integer', 'exists:students,id'],
        ], [
            'student_id.required' => 'Selecciona un estudiante.',
        ]);
        $year = $school->activeYear();
        $enrollments->enroll((int) $data['student_id'], (int) $data['course_id'], (int) $year->id);

        return redirect()->route('courses.show', $data['course_id'])->with('status', 'Estudiante inscrito.');
    }

    public function destroy(string $enrollment, EnrollmentService $enrollments): RedirectResponse
    {
        $courseId = $enrollments->unenroll((int) $enrollment);

        return redirect()->route('courses.show', $courseId)->with('status', 'Inscripción retirada.');
    }
}
