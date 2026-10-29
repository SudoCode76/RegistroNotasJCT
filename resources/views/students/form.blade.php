<x-layouts.app :title="$student ? 'Editar estudiante' : 'Nuevo estudiante'">
    <div class="mx-auto max-w-2xl">
        <a href="{{ route('students.index') }}" class="text-sm font-semibold text-cyan-800 hover:text-cyan-950">← Volver a estudiantes</a>
        <div class="mt-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-cyan-800">Nómina escolar</p>
            <h1 class="mt-2 text-2xl font-semibold tracking-tight">{{ $student ? 'Editar estudiante' : 'Registrar estudiante' }}</h1>
            <p class="mt-2 text-sm text-slate-500">Los identificadores son opcionales, pero no pueden repetirse.</p>
            <form method="POST" action="{{ $student ? route('students.update', $student->id) : route('students.store') }}" class="mt-7 space-y-5">
                @csrf
                @if ($student) @method('PUT') @endif
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="first_name" class="mb-2 block text-sm font-medium text-slate-700">Nombres</label>
                        <input id="first_name" name="first_name" value="{{ old('first_name', $student->first_name ?? '') }}" required maxlength="120" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-500/10">
                        @error('first_name')<p class="mt-2 text-sm text-rose-700">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="last_name" class="mb-2 block text-sm font-medium text-slate-700">Apellidos</label>
                        <input id="last_name" name="last_name" value="{{ old('last_name', $student->last_name ?? '') }}" required maxlength="120" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-500/10">
                        @error('last_name')<p class="mt-2 text-sm text-rose-700">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="rude" class="mb-2 block text-sm font-medium text-slate-700">RUDE <span class="font-normal text-slate-400">(opcional)</span></label>
                        <input id="rude" name="rude" value="{{ old('rude', $student->rude ?? '') }}" maxlength="40" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-500/10">
                        @error('rude')<p class="mt-2 text-sm text-rose-700">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="ci" class="mb-2 block text-sm font-medium text-slate-700">Carnet de identidad <span class="font-normal text-slate-400">(opcional)</span></label>
                        <input id="ci" name="ci" value="{{ old('ci', $student->ci ?? '') }}" maxlength="40" class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-500/10">
                        @error('ci')<p class="mt-2 text-sm text-rose-700">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                    <a href="{{ route('students.index') }}" class="rounded-xl px-4 py-3 text-center text-sm font-semibold text-slate-600 hover:bg-slate-100">Cancelar</a>
                    <button class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">{{ $student ? 'Guardar cambios' : 'Registrar estudiante' }}</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
