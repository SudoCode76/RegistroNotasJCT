<?php

namespace App\Http\Controllers;

use App\Queries\SchoolQuery;
use App\Services\GradeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GradeEntryController extends Controller
{
    public function index(Request $request, SchoolQuery $school): View
    {
        $year = $school->activeYear();
        $courses = $school->courses((int) $year->id);
        $periods = $school->periods((int) $year->id);
        $filters = $request->validate([
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'period_id' => ['nullable', 'integer', Rule::exists('periods', 'id')->where('academic_year_id', $year->id)],
        ]);
        $courseId = (int) ($filters['course_id'] ?? $courses->first()?->id ?? 0);
        $periodId = (int) ($filters['period_id'] ?? $periods->first()?->id ?? 0);
        $results = $courseId > 0 && $periodId > 0
            ? $school->gradeMatrix($courseId, $periodId, (int) $year->id)
            : collect();

        return view('grades.index', [
            'year' => $year,
            'courses' => $courses,
            'periods' => $periods,
            'selectedCourse' => $courseId,
            'selectedPeriod' => $periodId,
            'results' => $results,
        ]);
    }

    public function store(Request $request, SchoolQuery $school, GradeService $grades): RedirectResponse
    {
        $year = $school->activeYear();
        $data = $request->validate([
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'period_id' => ['required', 'integer', Rule::exists('periods', 'id')->where('academic_year_id', $year->id)],
            'grades' => ['required', 'array', 'min:1'],
            'grades.*' => ['required', 'array:enrollment_id,ser,saber,hacer,autoevaluacion'],
            'grades.*.enrollment_id' => ['required', 'integer', 'distinct', 'exists:enrollments,id'],
            'grades.*.ser' => ['nullable', 'numeric', 'decimal:0,1', 'min:0', 'max:10'],
            'grades.*.saber' => ['nullable', 'numeric', 'decimal:0,1', 'min:0', 'max:45'],
            'grades.*.hacer' => ['nullable', 'numeric', 'decimal:0,1', 'min:0', 'max:40'],
            'grades.*.autoevaluacion' => ['nullable', 'numeric', 'decimal:0,1', 'min:0', 'max:5'],
        ], [
            'grades.required' => 'No hay filas de estudiantes para guardar.',
            'grades.*.ser.max' => 'Ser no puede superar 10 puntos.',
            'grades.*.saber.max' => 'Saber no puede superar 45 puntos.',
            'grades.*.hacer.max' => 'Hacer no puede superar 40 puntos.',
            'grades.*.autoevaluacion.max' => 'Autoevaluación no puede superar 5 puntos.',
            'grades.*.ser.decimal' => 'Las notas aceptan como máximo un decimal.',
            'grades.*.saber.decimal' => 'Las notas aceptan como máximo un decimal.',
            'grades.*.hacer.decimal' => 'Las notas aceptan como máximo un decimal.',
            'grades.*.autoevaluacion.decimal' => 'Las notas aceptan como máximo un decimal.',
        ]);
        $saved = $grades->saveBatch(
            (int) $data['course_id'],
            (int) $data['period_id'],
            (int) $year->id,
            $data['grades'],
        );
        $message = $saved === 1 ? 'Se guardó una calificación.' : "Se guardaron {$saved} calificaciones.";

        return redirect()->route('grades.index', [
            'course_id' => $data['course_id'],
            'period_id' => $data['period_id'],
        ])->with('status', $saved === 0 ? 'No hubo cambios; ingresa notas en alguna fila.' : $message);
    }
}
