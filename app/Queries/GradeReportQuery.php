<?php

namespace App\Queries;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class GradeReportQuery
{
    public function centralizer(int $courseId, int $periodId): Builder
    {
        return DB::table('enrollments as e')
            ->join('students as s', 's.id', '=', 'e.student_id')
            ->join('courses as c', 'c.id', '=', 'e.course_id')
            ->join('subjects as subject', 'subject.id', '=', 'c.subject_id')
            ->join('periods as p', 'p.academic_year_id', '=', 'e.academic_year_id')
            ->leftJoin('grades as g', function ($join) use ($periodId) {
                $join->on('g.enrollment_id', '=', 'e.id')->where('g.period_id', '=', $periodId);
            })
            ->where('e.course_id', $courseId)->where('p.id', $periodId)
            ->whereColumn('e.academic_year_id', 'p.academic_year_id')
            ->select('s.rude', 's.last_name', 's.first_name', 'subject.name as subject', 'c.name as course',
                'g.ser', 'g.saber', 'g.hacer', 'g.autoevaluacion', 'g.final_score')
            ->selectRaw("CASE WHEN g.final_score < 51 THEN 'REPROBADO' WHEN g.final_score >= 51 THEN 'APROBADO' ELSE 'SIN NOTA' END AS status")
            ->orderBy('s.last_name')->orderBy('s.first_name');
    }

    public function failed(int $yearId, ?int $courseId = null, ?int $periodId = null): Builder
    {
        return DB::table('grades as g')
            ->join('enrollments as e', 'e.id', '=', 'g.enrollment_id')
            ->join('students as s', 's.id', '=', 'e.student_id')
            ->join('courses as c', 'c.id', '=', 'e.course_id')
            ->join('subjects as subject', 'subject.id', '=', 'c.subject_id')
            ->join('periods as p', 'p.id', '=', 'g.period_id')
            ->where('g.final_score', '<', 51)
            ->where('p.academic_year_id', $yearId)
            ->when($courseId, fn (Builder $query) => $query->where('e.course_id', $courseId))
            ->when($periodId, fn (Builder $query) => $query->where('g.period_id', $periodId))
            ->select('s.rude', 's.last_name', 's.first_name', 'c.name as course', 'subject.name as subject',
                'p.name as period', 'g.ser', 'g.saber', 'g.hacer', 'g.autoevaluacion', 'g.final_score')
            ->orderBy('g.final_score')->orderBy('s.last_name')->orderBy('s.first_name');
    }
}
