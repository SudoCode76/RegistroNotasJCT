<?php

namespace App\Http\Controllers;

use App\Queries\SchoolQuery;
use App\Services\CourseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(SchoolQuery $school): View
    {
        $year = $school->activeYear();

        return view('courses.index', [
            'year' => $year,
            'courses' => $school->courses((int) $year->id),
        ]);
    }

    public function create(SchoolQuery $school): View
    {
        return view('courses.form', ['course' => null, 'subjects' => $school->subjects()]);
    }

    public function store(Request $request, CourseService $courses): RedirectResponse
    {
        $courseId = $courses->store($this->validatedCourse($request));

        return redirect()->route('courses.show', $courseId)->with('status', 'Curso creado.');
    }

    public function show(string $course, SchoolQuery $school): View
    {
        $year = $school->activeYear();
        $courseId = (int) $course;

        return view('courses.show', [
            'year' => $year,
            'course' => $school->course($courseId),
            'enrollments' => $school->courseEnrollments($courseId, (int) $year->id),
            'availableStudents' => $school->availableStudents($courseId, (int) $year->id),
        ]);
    }

    public function edit(string $course, SchoolQuery $school): View
    {
        return view('courses.form', [
            'course' => $school->course((int) $course),
            'subjects' => $school->subjects(),
        ]);
    }

    public function update(Request $request, string $course, CourseService $courses): RedirectResponse
    {
        $courseId = (int) $course;
        $courses->update($courseId, $this->validatedCourse($request, $courseId));

        return redirect()->route('courses.show', $courseId)->with('status', 'Curso actualizado.');
    }

    public function destroy(string $course, CourseService $courses): RedirectResponse
    {
        $courses->delete((int) $course);

        return redirect()->route('courses.index')->with('status', 'Curso eliminado.');
    }

    private function validatedCourse(Request $request, ?int $courseId = null): array
    {
        $subjectId = $request->integer('subject_id');

        return $request->validate([
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'name' => [
                'required', 'string', 'max:120',
                Rule::unique('courses', 'name')->where('subject_id', $subjectId)->ignore($courseId),
            ],
            'level' => ['required', 'string', 'max:80'],
        ], [
            'name.unique' => 'Ya existe ese curso para la materia seleccionada.',
            'subject_id.required' => 'Selecciona una materia.',
            'name.required' => 'Ingresa el nombre del curso.',
            'level.required' => 'Ingresa el nivel del curso.',
        ]);
    }
}
