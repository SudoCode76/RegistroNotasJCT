<x-layouts.app title="Alumnos reprobados">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-rose-700">Informe · Gestión {{ $year->year }}</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight">Alumnos reprobados</h1>
            <p class="mt-2 text-sm text-slate-500">Estudiantes con nota final estrictamente menor a 51.</p>
        </div>
        <a href="{{ route('reports.centralizer') }}" class="text-sm font-semibold text-cyan-800 hover:text-cyan-950">Ir al centralizador →</a>
    </div>

    <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <form method="GET" action="{{ route('reports.failed') }}" class="grid gap-4 sm:grid-cols-[1fr_1fr_auto] sm:items-end">
            <div>
                <label for="course_id" class="mb-2 block text-sm font-medium text-slate-700">Curso y materia</label>
                <select id="course_id" name="course_id" class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-500/10">
                    <option value="">Todos los cursos</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}" @selected((string) $selectedCourse === (string) $course->id)>{{ $course->subject }} · {{ $course->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="period_id" class="mb-2 block text-sm font-medium text-slate-700">Trimestre</label>
                <select id="period_id" name="period_id" class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-500/10">
                    <option value="">Todos los trimestres</option>
                    @foreach ($periods as $period)
                        <option value="{{ $period->id }}" @selected((string) $selectedPeriod === (string) $period->id)>{{ $period->name }}</option>
                    @endforeach
                </select>
            </div>
            <button class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Aplicar filtros</button>
        </form>
    </section>

    <section class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <div>
                <h2 class="font-semibold">Resultados</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $results->total() }} calificaciones requieren seguimiento</p>
            </div>
            <span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700">&lt; 51</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[920px] text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr><th class="px-4 py-3 font-semibold">Estudiante</th><th class="px-4 py-3 font-semibold">RUDE</th><th class="px-4 py-3 font-semibold">Curso / materia</th><th class="px-4 py-3 font-semibold">Trimestre</th><th class="px-3 py-3 font-semibold">Ser</th><th class="px-3 py-3 font-semibold">Saber</th><th class="px-3 py-3 font-semibold">Hacer</th><th class="px-3 py-3 font-semibold">Auto</th><th class="px-3 py-3 font-semibold">Final</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($results as $row)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-4 font-semibold text-slate-800">{{ $row->last_name }}, {{ $row->first_name }}</td>
                            <td class="px-4 py-4 text-slate-500">{{ $row->rude ?? '—' }}</td>
                            <td class="px-4 py-4 text-slate-600">{{ $row->course }} · {{ $row->subject }}</td>
                            <td class="px-4 py-4 text-slate-600">{{ $row->period }}</td>
                            <td class="px-3 py-4 tabular-nums">{{ $row->ser }}</td><td class="px-3 py-4 tabular-nums">{{ $row->saber }}</td>
                            <td class="px-3 py-4 tabular-nums">{{ $row->hacer }}</td><td class="px-3 py-4 tabular-nums">{{ $row->autoevaluacion }}</td>
                            <td class="px-3 py-4"><span class="rounded-full bg-rose-50 px-2.5 py-1 text-xs font-bold tabular-nums text-rose-700">{{ $row->final_score }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-5 py-12 text-center">
                            <p class="font-semibold text-slate-800">No hay reprobados para esos filtros</p>
                            <p class="mt-1 text-sm text-slate-500">Cuando una nota sea menor a 51, aparecerá en este informe.</p>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($results->hasPages())<div class="border-t border-slate-100 px-5 py-4">{{ $results->links() }}</div>@endif
    </section>
</x-layouts.app>
