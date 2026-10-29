<?php

namespace App\Queries;

use Illuminate\Database\Query\JoinClause;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SchoolQuery
{
    public function activeYear(): object
    {
        return DB::table('academic_years')->where('is_active', true)->orderByDesc('year')->first()
            ?? DB::table('academic_years')->orderByDesc('year')->first()
            ?? throw new RuntimeException('No hay una gestión académica configurada.');
    }

    public function periods(int $yearId): Collection
    {
        return DB::table('periods')->where('academic_year_id', $yearId)->orderBy('number')->get();
    }

    public function subjects(): Collection
    {
        return DB::table('subjects')->orderBy('name')->get();
    }

    public function courses(int $yearId): Collection
    {
        return DB::table('courses as c')
            ->join('subjects as subject', 'subject.id', '=', 'c.subject_id')
            ->leftJoin('enrollments as e', function (JoinClause $join) use ($yearId) {
                $join->on('e.course_id', '=', 'c.id')->where('e.academic_year_id', '=', $yearId);
            })
            ->select('c.id', 'c.name', 'c.level', 'c.subject_id', 'subject.name as subject', 'subject.code as subject_code')
            ->selectRaw('COUNT(DISTINCT e.id) as enrollment_count')
            ->groupBy('c.id', 'c.name', 'c.level', 'c.subject_id', 'subject.name', 'subject.code')
            ->orderBy('subject.name')->orderBy('c.name')->get();
    }

    public function course(int $courseId): object
    {
        return DB::table('courses as c')
            ->join('subjects as subject', 'subject.id', '=', 'c.subject_id')
            ->where('c.id', $courseId)
            ->select('c.id', 'c.name', 'c.level', 'c.subject_id', 'subject.name as subject', 'subject.code as subject_code')
            ->firstOrFail();
    }

    public function student(int $studentId): object
    {
        return DB::table('students')->where('id', $studentId)->firstOrFail();
    }

    public function students(int $yearId, string $search = ''): LengthAwarePaginator
    {
        return DB::table('students as s')
            ->leftJoin('enrollments as e', function (JoinClause $join) use ($yearId) {
                $join->on('e.student_id', '=', 's.id')->where('e.academic_year_id', '=', $yearId);
            })
            ->select('s.id', 's.rude', 's.ci', 's.first_name', 's.last_name')
            ->selectRaw('COUNT(DISTINCT e.id) as enrollment_count')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('s.first_name', 'like', "%{$search}%")
                        ->orWhere('s.last_name', 'like', "%{$search}%")
                        ->orWhere('s.rude', 'like', "%{$search}%");
                });
            })
            ->groupBy('s.id', 's.rude', 's.ci', 's.first_name', 's.last_name')
            ->orderBy('s.last_name')->orderBy('s.first_name')->paginate(15)->withQueryString();
    }

    public function courseEnrollments(int $courseId, int $yearId): Collection
    {
        return DB::table('enrollments as e')
            ->join('students as s', 's.id', '=', 'e.student_id')
            ->where('e.course_id', $courseId)->where('e.academic_year_id', $yearId)
            ->select('e.id as enrollment_id', 's.id as student_id', 's.rude', 's.first_name', 's.last_name')
            ->selectSub(DB::table('grades')->selectRaw('COUNT(*)')
                ->whereColumn('grades.enrollment_id', 'e.id'), 'graded_periods')
            ->orderBy('s.last_name')->orderBy('s.first_name')->get();
    }

    public function availableStudents(int $courseId, int $yearId): Collection
    {
        return DB::table('students as s')
            ->whereNotExists(function ($query) use ($courseId, $yearId) {
                $query->selectRaw('1')->from('enrollments as e')
                    ->whereColumn('e.student_id', 's.id')
                    ->where('e.course_id', $courseId)
                    ->where('e.academic_year_id', $yearId);
            })
            ->select('s.id', 's.rude', 's.first_name', 's.last_name')
            ->orderBy('s.last_name')->orderBy('s.first_name')->get();
    }

    public function gradeMatrix(int $courseId, int $periodId, int $yearId): Collection
    {
        return DB::table('enrollments as e')
            ->join('students as s', 's.id', '=', 'e.student_id')
            ->leftJoin('grades as g', function (JoinClause $join) use ($periodId) {
                $join->on('g.enrollment_id', '=', 'e.id')->where('g.period_id', '=', $periodId);
            })
            ->where('e.course_id', $courseId)->where('e.academic_year_id', $yearId)
            ->select('e.id as enrollment_id', 's.rude', 's.first_name', 's.last_name',
                'g.ser', 'g.saber', 'g.hacer', 'g.autoevaluacion', 'g.final_score')
            ->orderBy('s.last_name')->orderBy('s.first_name')->get();
    }

    public function dashboardSummary(int $yearId, int $periodId): object
    {
        return DB::table('enrollments as e')
            ->leftJoin('grades as g', function (JoinClause $join) use ($periodId) {
                $join->on('g.enrollment_id', '=', 'e.id')->where('g.period_id', '=', $periodId);
            })
            ->where('e.academic_year_id', $yearId)
            ->selectRaw('COUNT(DISTINCT e.student_id) as students')
            ->selectRaw('COUNT(e.id) as enrollments')
            ->selectRaw('COUNT(g.id) as graded')
            ->selectRaw('COALESCE(SUM(CASE WHEN g.final_score < 51 THEN 1 ELSE 0 END), 0) as failed')
            ->selectRaw('COALESCE(SUM(CASE WHEN g.final_score >= 51 THEN 1 ELSE 0 END), 0) as passed')
            ->first();
    }
}
