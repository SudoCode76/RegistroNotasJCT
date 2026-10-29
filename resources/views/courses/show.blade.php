<x-layouts.app :title="$course->name">
    <a href="{{ route('courses.index') }}" class="text-sm font-semibold text-cyan-800 hover:text-cyan-950">← Volver a cursos</a>
    <section class="mt-4 flex flex-col gap-5 rounded-3xl bg-slate-900 p-6 text-white shadow-sm sm:flex-row sm:items-end sm:justify-between sm:p-8">
        <div>
            <span @class(['inline-flex rounded-full px-3 py-1 text-xs font-bold', 'bg-cyan-300 text-slate-950' => $course->subject_code === 'FIS', 'bg-emerald-300 text-slate-950' => $course->subject_code !== 'FIS'])>{{ $course->subject }}</span>
            <h1 class="mt-3 text-3xl font-semibold tracking-tight">{{ $course->name }}</h1>
            <p class="mt-2 text-sm text-slate-300">{{ $course->level }} · Gestión {{ $year->year }}</p>
        </div>
        <a href="{{ route('courses.edit', $course->id) }}" class="rounded-xl border border-white/20 px-4 py-2.5 text-center text-sm font-semibold text-white transition hover:bg-white/10">Editar curso</a>
    </section>

    <div class="mt-7 grid gap-5 xl:grid-cols-[0.8fr_1.2fr]">
        <section class="h-fit rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-lg font-semibold">Inscribir estudiante</h2>
            <p class="mt-1 text-sm text-slate-500">La inscripción corresponde a la gestión {{ $year->year }}.</p>
            @if ($availableStudents->isNotEmpty())
                <form method="POST" action="{{ route('enrollments.store') }}" class="mt-5 space-y-4">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $course->id }}">
                    <div>
                        <label for="student_id" class="mb-2 block text-sm font-medium text-slate-700">Estudiante</label>
                        <select id="student_id" name="student_id" required class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-sm focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-500/10">
                            <option value="">Selecciona estudiante</option>
                            @foreach ($availableStudents as $student)
                                <option value="{{ $student->id }}">{{ $student->last_name }}, {{ $student->first_name }} · {{ $student->rude ?? 'Sin RUDE' }}</option>
                            @endforeach
                        </select>
                        @error('student_id')<p class="mt-2 text-sm text-rose-700">{{ $message }}</p>@enderror
                    </div>
                    <button class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Inscribir</button>
                </form>
            @else
                <div class="mt-5 rounded-xl bg-slate-50 p-4 text-sm text-slate-600">Todos los estudiantes ya están inscritos en este curso o aún no hay estudiantes registrados.</div>
                <a href="{{ route('students.create') }}" class="mt-4 inline-flex text-sm font-semibold text-cyan-800 hover:text-cyan-950">Registrar estudiante →</a>
            @endif
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-end justify-between gap-4 border-b border-slate-100 px-5 py-5 sm:px-6">
                <div>
                    <h2 class="text-lg font-semibold">Lista de estudiantes</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ $enrollments->count() }} inscritos</p>
                </div>
                <a href="{{ route('grades.index', ['course_id' => $course->id]) }}" class="text-sm font-semibold text-cyan-800 hover:text-cyan-950">Registrar notas →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[620px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr><th class="px-5 py-3 font-semibold">Estudiante</th><th class="px-5 py-3 font-semibold">RUDE</th><th class="px-5 py-3 font-semibold">Trimestres con notas</th><th class="px-5 py-3"></th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($enrollments as $enrollment)
                            <tr>
                                <td class="px-5 py-4 font-medium text-slate-800">{{ $enrollment->last_name }}, {{ $enrollment->first_name }}</td>
                                <td class="px-5 py-4 text-slate-500">{{ $enrollment->rude ?? '—' }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $enrollment->graded_periods }} / 3</td>
                                <td class="px-5 py-4 text-right">
                                    @if ((int) $enrollment->graded_periods === 0)
                                        <form method="POST" action="{{ route('enrollments.destroy', $enrollment->enrollment_id) }}" onsubmit="return confirm('¿Retirar a este estudiante del curso?')">
                                            @csrf @method('DELETE')
                                            <button class="rounded-lg px-2 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-50">Retirar</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-slate-400">Con notas</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-10 text-center text-sm text-slate-500">Este curso todavía no tiene estudiantes inscritos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-layouts.app>
