<x-layouts.app title="Centralizador">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-cyan-800">Informe · Gestión {{ $year->year }}</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight">Centralizador de notas</h1>
            <p class="mt-2 text-sm text-slate-500">Una fila por estudiante, con los cuatro componentes y su estado.</p>
        </div>
        <a href="{{ route('reports.failed') }}" class="text-sm font-semibold text-cyan-800 hover:text-cyan-950">Ver reprobados →</a>
    </div>

    <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <form method="GET" action="{{ route('reports.centralizer') }}" class="grid gap-4 sm:grid-cols-[1fr_1fr_auto] sm:items-end">
            <div>
                <label for="course_id" class="mb-2 block text-sm font-medium text-slate-700">Curso y materia</label>
                <select id="course_id" name="course_id" required class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-500/10">
                    <option value="">Selecciona curso</option>
                    @foreach ($courses as $option)
                        <option value="{{ $option->id }}" @selected((string) $selectedCourse === (string) $option->id)>{{ $option->subject }} · {{ $option->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="period_id" class="mb-2 block text-sm font-medium text-slate-700">Trimestre</label>
                <select id="period_id" name="period_id" required class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-500/10">
                    <option value="">Selecciona trimestre</option>
                    @foreach ($periods as $option)
                        <option value="{{ $option->id }}" @selected((string) $selectedPeriod === (string) $option->id)>{{ $option->name }}</option>
                    @endforeach
                </select>
            </div>
            <button class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Generar</button>
        </form>
    </section>

    @if ($course && $period)
        <section class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-2 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-semibold">{{ $course->subject }} · {{ $course->name }}</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ $period->name }} · {{ $results->count() }} estudiantes</p>
                </div>
                <a href="{{ route('grades.index', ['course_id' => $course->id, 'period_id' => $selectedPeriod]) }}" class="text-sm font-semibold text-cyan-800 hover:text-cyan-950">Editar notas →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[920px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr><th class="px-4 py-3 font-semibold">Estudiante</th><th class="px-4 py-3 font-semibold">RUDE</th><th class="px-3 py-3 font-semibold">Ser<br><span class="font-normal normal-case">/ 10</span></th><th class="px-3 py-3 font-semibold">Saber<br><span class="font-normal normal-case">/ 45</span></th><th class="px-3 py-3 font-semibold">Hacer<br><span class="font-normal normal-case">/ 40</span></th><th class="px-3 py-3 font-semibold">Auto<br><span class="font-normal normal-case">/ 5</span></th><th class="px-3 py-3 font-semibold">Final<br><span class="font-normal normal-case">/ 100</span></th><th class="px-3 py-3 font-semibold">Estado</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($results as $row)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-4 font-semibold text-slate-800">{{ $row->last_name }}, {{ $row->first_name }}</td>
                                <td class="px-4 py-4 text-slate-500">{{ $row->rude ?? '—' }}</td>
                                <td class="px-3 py-4 tabular-nums">{{ $row->ser ?? '—' }}</td><td class="px-3 py-4 tabular-nums">{{ $row->saber ?? '—' }}</td>
                                <td class="px-3 py-4 tabular-nums">{{ $row->hacer ?? '—' }}</td><td class="px-3 py-4 tabular-nums">{{ $row->autoevaluacion ?? '—' }}</td>
                                <td class="px-3 py-4 font-semibold tabular-nums">{{ $row->final_score ?? '—' }}</td>
                                <td class="px-3 py-4">
                                    @if ($row->status === 'APROBADO')
                                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">APROBADO</span>
                                    @elseif ($row->status === 'REPROBADO')
                                        <span class="rounded-full bg-rose-50 px-2.5 py-1 text-xs font-bold text-rose-700">REPROBADO</span>
                                    @else
                                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">SIN NOTA</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-5 py-12 text-center">
                                <p class="font-semibold text-slate-800">Sin estudiantes inscritos</p>
                                <p class="mt-1 text-sm text-slate-500">Agrega estudiantes desde la página del curso.</p>
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @else
        <div class="mt-5 rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center">
            <p class="font-semibold text-slate-800">Selecciona el curso y trimestre</p>
            <p class="mt-1 text-sm text-slate-500">El centralizador mostrará también a estudiantes que todavía no tienen nota.</p>
        </div>
    @endif
</x-layouts.app>
