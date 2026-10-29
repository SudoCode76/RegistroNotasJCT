<?php

namespace App\Http\Controllers;

use App\Queries\GradeReportQuery;
use App\Queries\SchoolQuery;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GradeReportController extends Controller
{
    public function index(Request $request, GradeReportQuery $reports, SchoolQuery $school): View
    {
        $year = $school->activeYear();
        $filters = $request->validate([
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'period_id' => ['nullable', 'integer', Rule::exists('periods', 'id')->where('academic_year_id', $year->id)],
        ]);
        $courses = $school->courses((int) $year->id);
        $periods = $school->periods((int) $year->id);

        return view('reports.index', [
            'year' => $year,
            'courses' => $courses,
            'periods' => $periods,
            'results' => $reports->failed(
                (int) $year->id,
                isset($filters['course_id']) ? (int) $filters['course_id'] : null,
                isset($filters['period_id']) ? (int) $filters['period_id'] : null,
            )->paginate(25)->withQueryString(),
            'selectedCourse' => $filters['course_id'] ?? '',
            'selectedPeriod' => $filters['period_id'] ?? '',
        ]);
    }

    public function centralizer(Request $request, GradeReportQuery $reports, SchoolQuery $school): View
    {
        $year = $school->activeYear();
        $filters = $request->validate([
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'period_id' => ['nullable', 'integer', Rule::exists('periods', 'id')->where('academic_year_id', $year->id)],
        ]);
        $courseId = (int) ($filters['course_id'] ?? 0);
        $periodId = (int) ($filters['period_id'] ?? 0);
        $hasFilters = $courseId > 0 && $periodId > 0;

        return view('reports.centralizer', [
            'year' => $year,
            'results' => $hasFilters ? $reports->centralizer($courseId, $periodId)->get() : collect(),
            'courses' => $school->courses((int) $year->id),
            'periods' => $school->periods((int) $year->id),
            'course' => $hasFilters ? $school->course($courseId) : null,
            'period' => $hasFilters ? $school->periods((int) $year->id)->firstWhere('id', $periodId) : null,
            'selectedCourse' => $courseId,
            'selectedPeriod' => $periodId,
        ]);
    }
}
