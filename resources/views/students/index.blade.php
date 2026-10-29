<x-layouts.app title="Estudiantes">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-cyan-800">Gestión académica</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight">Estudiantes</h1>
            <p class="mt-2 text-sm text-slate-500">Administra la nómina y sus identificadores.</p>
        </div>
        <a href="{{ route('students.create') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">Registrar estudiante</a>
    </div>

    <section class="mt-7 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-4 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
            <div>
                <h2 class="font-semibold">Nómina escolar</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $students->total() }} registros</p>
            </div>
            <form method="GET" action="{{ route('students.index') }}" class="flex gap-2">
                <label for="search" class="sr-only">Buscar estudiante</label>
                <input id="search" name="search" value="{{ $search }}" placeholder="Nombre o RUDE" class="min-w-0 flex-1 rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-500/10 sm:w-64">
                <button class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Buscar</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr><th class="px-5 py-3 font-semibold">Estudiante</th><th class="px-5 py-3 font-semibold">RUDE</th><th class="px-5 py-3 font-semibold">CI</th><th class="px-5 py-3 font-semibold">Inscripciones</th><th class="px-5 py-3"></th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($students as $student)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4 font-medium text-slate-800">{{ $student->last_name }}, {{ $student->first_name }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $student->rude ?? '—' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $student->ci ?? '—' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $student->enrollment_count }}</td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('students.edit', $student->id) }}" class="rounded-lg px-2.5 py-1.5 text-xs font-semibold text-cyan-800 hover:bg-cyan-50">Editar</a>
                                    @if ((int) $student->enrollment_count === 0)
                                        <form method="POST" action="{{ route('students.destroy', $student->id) }}" onsubmit="return confirm('¿Eliminar este estudiante?')">
                                            @csrf @method('DELETE')
                                            <button class="rounded-lg px-2.5 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-50">Eliminar</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-12 text-center">
                            <p class="font-semibold text-slate-800">No encontramos estudiantes</p>
                            <p class="mt-1 text-sm text-slate-500">Registra uno nuevo o cambia el texto de búsqueda.</p>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($students->hasPages())<div class="border-t border-slate-100 px-5 py-4">{{ $students->links() }}</div>@endif
    </section>
</x-layouts.app>
