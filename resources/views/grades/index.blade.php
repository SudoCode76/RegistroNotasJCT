<x-layouts.app title="Registrar notas">
    <div>
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-cyan-800">Gestión {{ $year->year }}</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight">Registro de notas</h1>
        <p class="mt-2 text-sm text-slate-500">Completa los cuatro componentes. El total se calcula automáticamente sobre 100.</p>
    </div>

    <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <form method="GET" action="{{ route('grades.index') }}" class="grid gap-4 sm:grid-cols-[1fr_1fr_auto] sm:items-end">
            <div>
                <label for="course_id" class="mb-2 block text-sm font-medium text-slate-700">Curso y materia</label>
                <select id="course_id" name="course_id" required class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-500/10">
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}" @selected((string) $selectedCourse === (string) $course->id)>{{ $course->subject }} · {{ $course->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="period_id" class="mb-2 block text-sm font-medium text-slate-700">Trimestre</label>
                <select id="period_id" name="period_id" required class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-500/10">
                    @foreach ($periods as $period)
                        <option value="{{ $period->id }}" @selected((string) $selectedPeriod === (string) $period->id)>{{ $period->name }}</option>
                    @endforeach
                </select>
            </div>
            <button class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Ver lista</button>
        </form>
    </section>

    @if ($courses->isEmpty())
        <div class="mt-5 rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center">
            <p class="font-semibold text-slate-800">No hay cursos para registrar</p>
            <a href="{{ route('courses.create') }}" class="mt-3 inline-flex text-sm font-semibold text-cyan-800">Crear curso →</a>
        </div>
    @elseif ($results->isEmpty())
        <div class="mt-5 rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center">
            <p class="font-semibold text-slate-800">Este curso todavía no tiene estudiantes</p>
            <p class="mt-1 text-sm text-slate-500">Inscríbelos desde la página del curso y vuelve para cargar sus notas.</p>
            <a href="{{ route('courses.show', $selectedCourse) }}" class="mt-4 inline-flex rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white">Administrar inscripciones</a>
        </div>
    @else
        <form method="POST" action="{{ route('grades.store') }}" class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            @csrf
            <input type="hidden" name="course_id" value="{{ $selectedCourse }}">
            <input type="hidden" name="period_id" value="{{ $selectedPeriod }}">
            <div class="flex flex-col gap-2 border-b border-slate-100 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                <div>
                    <h2 class="font-semibold text-slate-900">{{ $results->count() }} estudiantes</h2>
                    <p class="mt-1 text-xs text-slate-500">Máximos: Ser 10 · Saber 45 · Hacer 40 · Autoevaluación 5</p>
                </div>
                <button class="rounded-xl bg-cyan-500 px-4 py-2.5 text-sm font-semibold text-slate-950 transition hover:bg-cyan-400">Guardar calificaciones</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1050px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="sticky left-0 z-10 bg-slate-50 px-4 py-3 font-semibold">Estudiante</th>
                            <th class="px-3 py-3 font-semibold">Ser<br><span class="font-normal normal-case">/ 10</span></th>
                            <th class="px-3 py-3 font-semibold">Saber<br><span class="font-normal normal-case">/ 45</span></th>
                            <th class="px-3 py-3 font-semibold">Hacer<br><span class="font-normal normal-case">/ 40</span></th>
                            <th class="px-3 py-3 font-semibold">Autoeval.<br><span class="font-normal normal-case">/ 5</span></th>
                            <th class="px-3 py-3 font-semibold">Total</th>
                            <th class="px-3 py-3 font-semibold">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($results as $index => $row)
                            <tr class="align-top hover:bg-slate-50/70">
                                <td class="sticky left-0 bg-white px-4 py-3.5">
                                    <input type="hidden" name="grades[{{ $index }}][enrollment_id]" value="{{ $row->enrollment_id }}">
                                    <p class="font-semibold text-slate-800">{{ $row->last_name }}, {{ $row->first_name }}</p>
                                    <p class="mt-1 text-xs text-slate-500">RUDE {{ $row->rude ?? '—' }}</p>
                                </td>
                                @foreach ([['ser', 10], ['saber', 45], ['hacer', 40], ['autoevaluacion', 5]] as [$component, $maximum])
                                    <td class="px-2 py-3">
                                        <label for="grade-{{ $index }}-{{ $component }}" class="sr-only">{{ ucfirst($component) }} para {{ $row->first_name }} {{ $row->last_name }}</label>
                                        <input id="grade-{{ $index }}-{{ $component }}" name="grades[{{ $index }}][{{ $component }}]" type="number" min="0" max="{{ $maximum }}" step="0.1" value="{{ old('grades.'.$index.'.'.$component, $row->$component) }}" class="w-20 rounded-lg border border-slate-300 px-2 py-2 text-sm tabular-nums focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-500/10">
                                    </td>
                                @endforeach
                                <td class="px-3 py-3.5 font-semibold tabular-nums text-slate-800">{{ $row->final_score ?? '—' }}</td>
                                <td class="px-3 py-3.5">
                                    @if ($row->final_score === null)
                                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">SIN NOTA</span>
                                    @elseif ((float) $row->final_score < 51)
                                        <span class="rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700">REPROBADO</span>
                                    @else
                                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">APROBADO</span>
                                    @endif
                                </td>
                            </tr>
                            @foreach (['ser', 'saber', 'hacer', 'autoevaluacion'] as $component)
                                @error('grades.'.$index.'.'.$component)
                                    <tr><td colspan="7" class="bg-rose-50 px-4 py-2 text-xs font-medium text-rose-800">{{ $message }}</td></tr>
                                @enderror
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end border-t border-slate-100 px-4 py-4 sm:px-5">
                <button class="w-full rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 sm:w-auto">Guardar calificaciones</button>
            </div>
        </form>
    @endif
</x-layouts.app>
