<?php

namespace App\Http\Controllers;

use App\Queries\SchoolQuery;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, SchoolQuery $school): View
    {
        $year = $school->activeYear();
        $periods = $school->periods((int) $year->id);
        $filters = $request->validate([
            'period_id' => ['nullable', 'integer', Rule::exists('periods', 'id')->where('academic_year_id', $year->id)],
        ]);
        $periodId = (int) ($filters['period_id'] ?? $periods->first()?->id ?? 0);
        $courses = $school->courses((int) $year->id);
        $summary = $school->dashboardSummary((int) $year->id, $periodId);
        $summary->courses = $courses->count();

        return view('dashboard.index', [
            'year' => $year,
            'periods' => $periods,
            'selectedPeriod' => $periodId,
            'summary' => $summary,
            'courses' => $courses,
        ]);
    }
}
