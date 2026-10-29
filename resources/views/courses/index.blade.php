<x-layouts.app title="Cursos">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-cyan-800">Gestión {{ $year->year }}</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight">Cursos y materias</h1>
            <p class="mt-2 text-sm text-slate-500">Organiza las asignaciones e inscribe a tus estudiantes.</p>
        </div>
        <a href="{{ route('courses.create') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">Crear curso</a>
    </div>

    <div class="mt-7 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($courses as $course)
            <article class="flex flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <span @class(['inline-flex rounded-full px-2.5 py-1 text-xs font-semibold', 'bg-cyan-50 text-cyan-800' => $course->subject_code === 'FIS', 'bg-emerald-50 text-emerald-800' => $course->subject_code !== 'FIS'])>{{ $course->subject }}</span>
                        <h2 class="mt-3 text-lg font-semibold text-slate-900">{{ $course->name }}</h2>
                        <p class="mt-1 text-sm text-slate-500">{{ $course->level }} · {{ $course->enrollment_count }} inscritos</p>
                    </div>
                    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-slate-100 text-sm font-bold text-slate-600">{{ mb_substr($course->subject, 0, 1) }}</span>
                </div>
                <div class="mt-6 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-4">
                    <a href="{{ route('courses.show', $course->id) }}" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">Ver lista</a>
                    <a href="{{ route('courses.edit', $course->id) }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-cyan-800 transition hover:bg-cyan-50">Editar</a>
                    @if ((int) $course->enrollment_count === 0)
                        <form method="POST" action="{{ route('courses.destroy', $course->id) }}" class="ml-auto" onsubmit="return confirm('¿Eliminar este curso?')">
                            @csrf @method('DELETE')
                            <button class="rounded-lg px-3 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-50">Eliminar</button>
                        </form>
                    @endif
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center sm:col-span-2 xl:col-span-3">
                <p class="font-semibold text-slate-800">Todavía no hay cursos</p>
                <p class="mt-1 text-sm text-slate-500">Crea una asignación de Física o Biología para comenzar.</p>
                <a href="{{ route('courses.create') }}" class="mt-4 inline-flex rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white">Crear curso</a>
            </div>
        @endforelse
    </div>
</x-layouts.app>
