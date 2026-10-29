@props(['title' => 'Inicio'])

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} · JCT Registro de Notas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <div class="min-h-screen lg:flex">
        <aside class="hidden w-64 shrink-0 flex-col bg-slate-950 px-5 py-6 text-white lg:fixed lg:inset-y-0 lg:flex">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-2xl px-2 py-1">
                <span class="grid size-11 place-items-center rounded-xl bg-cyan-400 text-sm font-black tracking-wide text-slate-950">JCT</span>
                <span>
                    <span class="block text-sm font-semibold tracking-wide">José Carrasco</span>
                    <span class="block text-xs text-slate-400">Registro académico</span>
                </span>
            </a>

            <p class="mb-2 mt-10 px-3 text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Gestión escolar</p>
            <nav class="flex flex-col gap-1" aria-label="Navegación principal">
                <a href="{{ route('dashboard') }}" @class(['flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition', 'bg-white/10 text-white' => request()->routeIs('dashboard'), 'text-slate-300 hover:bg-white/5 hover:text-white' => ! request()->routeIs('dashboard')])>
                    <span class="grid size-7 place-items-center rounded-lg bg-white/10 text-xs">01</span> Resumen
                </a>
                <a href="{{ route('courses.index') }}" @class(['flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition', 'bg-white/10 text-white' => request()->routeIs('courses.*'), 'text-slate-300 hover:bg-white/5 hover:text-white' => ! request()->routeIs('courses.*')])>
                    <span class="grid size-7 place-items-center rounded-lg bg-white/10 text-xs">02</span> Cursos
                </a>
                <a href="{{ route('students.index') }}" @class(['flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition', 'bg-white/10 text-white' => request()->routeIs('students.*'), 'text-slate-300 hover:bg-white/5 hover:text-white' => ! request()->routeIs('students.*')])>
                    <span class="grid size-7 place-items-center rounded-lg bg-white/10 text-xs">03</span> Estudiantes
                </a>
                <a href="{{ route('grades.index') }}" @class(['flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition', 'bg-white/10 text-white' => request()->routeIs('grades.*'), 'text-slate-300 hover:bg-white/5 hover:text-white' => ! request()->routeIs('grades.*')])>
                    <span class="grid size-7 place-items-center rounded-lg bg-white/10 text-xs">04</span> Registrar notas
                </a>
            </nav>

            <p class="mb-2 mt-8 px-3 text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Informes</p>
            <nav class="flex flex-col gap-1" aria-label="Informes">
                <a href="{{ route('reports.centralizer') }}" @class(['flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition', 'bg-white/10 text-white' => request()->routeIs('reports.centralizer'), 'text-slate-300 hover:bg-white/5 hover:text-white' => ! request()->routeIs('reports.centralizer')])>
                    <span class="grid size-7 place-items-center rounded-lg bg-white/10 text-xs">05</span> Centralizador
                </a>
                <a href="{{ route('reports.failed') }}" @class(['flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition', 'bg-white/10 text-white' => request()->routeIs('reports.failed'), 'text-slate-300 hover:bg-white/5 hover:text-white' => ! request()->routeIs('reports.failed')])>
                    <span class="grid size-7 place-items-center rounded-lg bg-white/10 text-xs">06</span> Reprobados
                </a>
            </nav>

            <div class="mt-auto rounded-2xl border border-white/10 bg-white/5 p-4">
                <p class="text-xs font-semibold text-cyan-300">Gestión activa</p>
                <p class="mt-1 text-2xl font-semibold">2026</p>
                <p class="mt-1 text-xs leading-5 text-slate-400">Ser 10 · Saber 45<br>Hacer 40 · Autoevaluación 5</p>
            </div>
        </aside>

        <div class="min-w-0 flex-1 lg:pl-64">
            <header class="border-b border-slate-200 bg-white/90 px-4 py-3 backdrop-blur sm:px-6 lg:px-10">
                <div class="flex items-center justify-between gap-4">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 lg:hidden">
                        <span class="grid size-9 place-items-center rounded-xl bg-cyan-400 text-xs font-black text-slate-950">JCT</span>
                        <span class="text-sm font-semibold">Registro de notas</span>
                    </a>
                    <div class="hidden text-sm text-slate-500 lg:block">{{ $title }}</div>
                    <div class="ml-auto flex items-center gap-3">
                        <div class="hidden text-right sm:block">
                            <p class="text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-500">Docente</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-medium text-slate-600 transition hover:border-slate-300 hover:bg-slate-50">Salir</button>
                        </form>
                    </div>
                </div>
                <nav class="mt-3 flex gap-1 overflow-x-auto pb-1 lg:hidden" aria-label="Navegación móvil">
                    <a href="{{ route('dashboard') }}" @class(['whitespace-nowrap rounded-lg px-3 py-2 text-xs font-semibold', 'bg-slate-900 text-white' => request()->routeIs('dashboard'), 'text-slate-600 hover:bg-slate-100' => ! request()->routeIs('dashboard')])>Inicio</a>
                    <a href="{{ route('courses.index') }}" @class(['whitespace-nowrap rounded-lg px-3 py-2 text-xs font-semibold', 'bg-slate-900 text-white' => request()->routeIs('courses.*'), 'text-slate-600 hover:bg-slate-100' => ! request()->routeIs('courses.*')])>Cursos</a>
                    <a href="{{ route('students.index') }}" @class(['whitespace-nowrap rounded-lg px-3 py-2 text-xs font-semibold', 'bg-slate-900 text-white' => request()->routeIs('students.*'), 'text-slate-600 hover:bg-slate-100' => ! request()->routeIs('students.*')])>Estudiantes</a>
                    <a href="{{ route('grades.index') }}" @class(['whitespace-nowrap rounded-lg px-3 py-2 text-xs font-semibold', 'bg-slate-900 text-white' => request()->routeIs('grades.*'), 'text-slate-600 hover:bg-slate-100' => ! request()->routeIs('grades.*')])>Notas</a>
                    <a href="{{ route('reports.centralizer') }}" @class(['whitespace-nowrap rounded-lg px-3 py-2 text-xs font-semibold', 'bg-slate-900 text-white' => request()->routeIs('reports.centralizer'), 'text-slate-600 hover:bg-slate-100' => ! request()->routeIs('reports.centralizer')])>Centralizador</a>
                    <a href="{{ route('reports.failed') }}" @class(['whitespace-nowrap rounded-lg px-3 py-2 text-xs font-semibold', 'bg-slate-900 text-white' => request()->routeIs('reports.failed'), 'text-slate-600 hover:bg-slate-100' => ! request()->routeIs('reports.failed')])>Reprobados</a>
                </nav>
            </header>

            <main class="mx-auto max-w-7xl p-4 sm:p-6 lg:px-10 lg:py-8">
                @if (session('status'))
                    <div role="status" class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('status') }}</div>
                @endif
                @foreach (['course', 'student', 'enrollment', 'grades', 'period_id'] as $errorKey)
                    @error($errorKey)
                        <div role="alert" class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800">{{ $message }}</div>
                    @enderror
                @endforeach
                {{ $slot }}
            </main>
        </div>
    </div>
    @fluxScripts
</body>
</html>
