<?php

namespace App\Http\Controllers;

use App\Queries\SchoolQuery;
use App\Services\StudentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request, SchoolQuery $school): View
    {
        $filters = $request->validate(['search' => ['nullable', 'string', 'max:80']]);
        $year = $school->activeYear();

        return view('students.index', [
            'students' => $school->students((int) $year->id, trim($filters['search'] ?? '')),
            'search' => $filters['search'] ?? '',
        ]);
    }

    public function create(): View
    {
        return view('students.form', ['student' => null]);
    }

    public function store(Request $request, StudentService $students): RedirectResponse
    {
        $students->store($this->validatedStudent($request));

        return redirect()->route('students.index')->with('status', 'Estudiante registrado.');
    }

    public function edit(string $student, SchoolQuery $school): View
    {
        return view('students.form', ['student' => $school->student((int) $student)]);
    }

    public function update(Request $request, string $student, StudentService $students): RedirectResponse
    {
        $studentId = (int) $student;
        $students->update($studentId, $this->validatedStudent($request, $studentId));

        return redirect()->route('students.index')->with('status', 'Datos del estudiante actualizados.');
    }

    public function destroy(string $student, StudentService $students): RedirectResponse
    {
        $students->delete((int) $student);

        return redirect()->route('students.index')->with('status', 'Estudiante eliminado.');
    }

    private function validatedStudent(Request $request, ?int $studentId = null): array
    {
        return $request->validate([
            'rude' => ['nullable', 'string', 'max:40', Rule::unique('students', 'rude')->ignore($studentId)],
            'ci' => ['nullable', 'string', 'max:40', Rule::unique('students', 'ci')->ignore($studentId)],
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
        ], [
            'rude.unique' => 'Ya existe un estudiante con ese RUDE.',
            'ci.unique' => 'Ya existe un estudiante con ese CI.',
            'first_name.required' => 'Ingresa el nombre del estudiante.',
            'last_name.required' => 'Ingresa los apellidos del estudiante.',
        ]);
    }
}
