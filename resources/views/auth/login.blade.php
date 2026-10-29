<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ingresar · JCT Registro de Notas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>
<body class="min-h-screen bg-slate-950 text-slate-900 antialiased">
    <main class="grid min-h-screen lg:grid-cols-[1.1fr_0.9fr]">
        <section class="relative hidden overflow-hidden bg-slate-900 p-12 text-white lg:flex lg:flex-col lg:justify-between xl:p-20">
            <div class="absolute -right-28 -top-28 size-96 rounded-full bg-cyan-400/10 blur-3xl"></div>
            <a href="{{ route('login') }}" class="relative flex items-center gap-3">
                <span class="grid size-12 place-items-center rounded-2xl bg-cyan-400 text-sm font-black text-slate-950">JCT</span>
                <span class="text-sm font-semibold tracking-wide">José Carrasco Torrico</span>
            </a>
            <div class="relative max-w-xl">
                <p class="mb-4 text-xs font-bold uppercase tracking-[0.25em] text-cyan-300">Gestión académica · 2026</p>
            </div>
            <p class="relative text-xs text-slate-500">Sistema interno de registro escolar</p>
        </section>

        <section class="flex items-center justify-center bg-slate-50 px-5 py-12 sm:px-10">
            <div class="w-full max-w-md">
                <a href="{{ route('login') }}" class="mb-10 flex items-center gap-3 lg:hidden">
                    <span class="grid size-11 place-items-center rounded-xl bg-cyan-400 text-xs font-black text-slate-950">JCT</span>
                    <span class="text-sm font-semibold text-slate-800">Registro de notas</span>
                </a>
                <p class="text-sm font-semibold text-cyan-700">Bienvenido de nuevo</p>
                <h2 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">Inicia sesión</h2>
                <p class="mt-2 text-sm text-slate-500">Ingresa con tu cuenta docente para continuar.</p>

                <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-slate-700">Correo electrónico</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition placeholder:text-slate-400 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10">
                        @error('email')<p class="mt-2 text-sm text-rose-700">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="password" class="mb-2 block text-sm font-medium text-slate-700">Contraseña</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition placeholder:text-slate-400 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10">
                        @error('password')<p class="mt-2 text-sm text-rose-700">{{ $message }}</p>@enderror
                    </div>
                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="remember" value="1" class="size-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                        Mantener sesión iniciada
                    </label>
                    <button type="submit" class="flex w-full items-center justify-center rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-900/15">Ingresar al sistema</button>
                </form>
                <p class="mt-8 text-center text-xs leading-5 text-slate-400">José Carrasco Torrico · Gestión 2026</p>
            </div>
        </section>
    </main>
    @fluxScripts
</body>
</html>
