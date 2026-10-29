<x-layouts.app title="Resumen">
    <div class="relative overflow-hidden rounded-3xl bg-slate-900 px-6 py-8 text-white shadow-sm sm:px-9 sm:py-10">
        <div class="absolute -right-12 -top-24 size-72 rounded-full bg-cyan-400/15 blur-3xl"></div>
        <div class="relative flex flex-col gap-7 sm:flex-row sm:items-end sm:justify-between">
            <div class="max-w-xl">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-cyan-300">Gestión {{ $year->year }}</p>
                <h1 class="mt-3 text-3xl font-semibold tracking-tight sm:text-4xl">Hola, {{ auth()->user()->name }}</h1>
                <p class="mt-3 max-w-lg text-sm leading-6 text-slate-300">Tu espacio para organizar cursos, registrar notas y revisar el avance académico.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('grades.index') }}" class="rounded-xl bg-cyan-400 px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300">Registrar notas</a>
                <a href="{{ route('reports.centralizer') }}" class="rounded-xl border border-white/20 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/10">Ver centralizador</a>
            </div>
        </div>
    </div>

    <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-xl font-semibold tracking-tight text-slate-900">Resumen del trimestre</h2>
            <p class="mt-1 text-sm text-slate-500">Elige un periodo para actualizar los indicadores.</p>
        </div>
        <form method="GET" action="{{ route('dashboard') }}" class="flex items-end gap-3">
            <div>
                <label for="period_id" class="mb-1.5 block text-xs font-semibold text-slate-600">Trimestre</label>
                <select id="period_id" name="period_id" class="min-w-52 rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-500/10">
                    @foreach ($periods as $period)
                        <option value="{{ $period->id }}" @selected((string) $selectedPeriod === (string) $period->id)>{{ $period->name }}</option>
                    @endforeach
                </select>
            </div>
            <button class="rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 ring-1 ring-slate-200 transition hover:bg-slate-100">Aplicar</button>
        </form>
    </div>

    <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Cursos asignados</p>
            <p class="mt-3 text-3xl font-semibold tracking-tight">{{ $summary->courses }}</p>
            <p class="mt-2 text-xs text-slate-500">Física y Biología</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Estudiantes inscritos</p>
            <p class="mt-3 text-3xl font-semibold tracking-tight">{{ $summary->students }}</p>
            <p class="mt-2 text-xs text-slate-500">En {{ $summary->enrollments }} inscripciones</p>
        </article>
        <article class="rounded-2xl border border-emerald-100 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-emerald-700">Aprobados</p>
            <p class="mt-3 text-3xl font-semibold tracking-tight text-emerald-700">{{ $summary->passed }}</p>
            <p class="mt-2 text-xs text-slate-500">Nota final de 51 o más</p>
        </article>
        <article class="rounded-2xl border border-rose-100 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-rose-700">Reprobados</p>
            <p class="mt-3 text-3xl font-semibold tracking-tight text-rose-700">{{ $summary->failed }}</p>
            <p class="mt-2 text-xs text-slate-500">Nota final menor a 51</p>
        </article>
        <article class="rounded-2xl border border-amber-100 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-amber-700">Pendientes de nota</p>
            <p class="mt-3 text-3xl font-semibold tracking-tight text-amber-700">{{ $summary->enrollments - $summary->graded }}</p>
            <p class="mt-2 text-xs text-slate-500">En el trimestre seleccionado</p>
        </article>
    </div>

    <div class="mt-9 flex items-end justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold tracking-tight text-slate-900">Tus cursos</h2>
            <p class="mt-1 text-sm text-slate-500">Acceso rápido a listas e inscripciones.</p>
        </div>
        <a href="{{ route('courses.index') }}" class="text-sm font-semibold text-cyan-800 hover:text-cyan-950">Ver todos <span aria-hidden="true">→</span></a>
    </div>
    <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($courses->take(6) as $course)
            <a href="{{ route('courses.show', $course->id) }}" class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-cyan-300 hover:shadow-md">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <span @class(['inline-flex rounded-full px-2.5 py-1 text-xs font-semibold', 'bg-cyan-50 text-cyan-800' => $course->subject_code === 'FIS', 'bg-emerald-50 text-emerald-800' => $course->subject_code !== 'FIS'])>{{ $course->subject }}</span>
                        <h3 class="mt-3 font-semibold text-slate-900">{{ $course->name }}</h3>
                        <p class="mt-1 text-sm text-slate-500">{{ $course->enrollment_count }} estudiantes</p>
                    </div>
                    <span class="text-lg text-slate-300 transition group-hover:translate-x-1 group-hover:text-cyan-700" aria-hidden="true">→</span>
                </div>
            </a>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-6 text-sm text-slate-500">Aún no hay cursos. Crea el primero para empezar.</div>
        @endforelse
    </div>
</x-layouts.app>
