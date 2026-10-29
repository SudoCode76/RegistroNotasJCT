<x-layouts.app :title="$course ? 'Editar curso' : 'Nuevo curso'">
    <div class="mx-auto max-w-2xl">
        <a href="{{ route('courses.index') }}" class="text-sm font-semibold text-cyan-800 hover:text-cyan-950">← Volver a cursos</a>
        <div class="mt-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-cyan-800">Catálogo académico</p>
            <h1 class="mt-2 text-2xl font-semibold tracking-tight">{{ $course ? 'Editar curso' : 'Crear curso' }}</h1>
            <p class="mt-2 text-sm text-slate-500">Cada curso se registra dentro de una materia.</p>

            <form method="POST" action="{{ $course ? route('courses.update', $course->id) : route('courses.store') }}" class="mt-7 space-y-5">
                @csrf
                @if ($course) @method('PUT') @endif
                <div>
                    <label for="subject_id" class="mb-2 block text-sm font-medium text-slate-700">Materia</label>
                    <select id="subject_id" name="subject_id" required class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-500/10">
                        <option value="">Selecciona una materia</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}" @selected((string) old('subject_id', $course->subject_id ?? '') === (string) $subject->id)>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                    @error('subject_id')<p class="mt-2 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="name" class="mb-2 block text-sm font-medium text-slate-700">Nombre del curso</label>
                    <input id="name" name="name" value="{{ old('name', $course->name ?? '') }}" required maxlength="120" placeholder="Ej. 1ro Secundaria A" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-500/10">
                    @error('name')<p class="mt-2 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="level" class="mb-2 block text-sm font-medium text-slate-700">Nivel</label>
                    <input id="level" name="level" value="{{ old('level', $course->level ?? 'Secundaria') }}" required maxlength="80" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-500/10">
                    @error('level')<p class="mt-2 text-sm text-rose-700">{{ $message }}</p>@enderror
                </div>
                <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                    <a href="{{ route('courses.index') }}" class="rounded-xl px-4 py-3 text-center text-sm font-semibold text-slate-600 hover:bg-slate-100">Cancelar</a>
                    <button class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">{{ $course ? 'Guardar cambios' : 'Crear curso' }}</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
