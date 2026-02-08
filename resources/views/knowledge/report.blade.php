<!DOCTYPE html>
<html lang="es" class="dark">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reporte de Atención</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 text-gray-200 p-8 font-sans">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Reporte de Atención - Lectura</h1>

        <div class="bg-white/5 border border-white/10 rounded-2xl p-6 mb-8">
            <h2 class="text-lg font-semibold mb-4 text-gray-400 uppercase tracking-wide">Veredicto del Sistema</h2>
            <div class="text-3xl font-black {{ $verdictColor }}">
                {{ $verdict }}
            </div>
            <p class="mt-2 text-gray-400">{{ $details }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white/5 border border-white/10 rounded-xl p-5 text-center">
                <div class="text-4xl font-bold mb-2">{{ $faces }}</div>
                <div class="text-sm text-gray-400">Rostros Perdidos (Eventos)</div>
            </div>
            <div class="bg-white/5 border border-white/10 rounded-xl p-5 text-center">
                <div class="text-4xl font-bold mb-2">{{ $gaze }}</div>
                <div class="text-sm text-gray-400">Desvíos de Mirada</div>
            </div>
            <div class="bg-white/5 border border-white/10 rounded-xl p-5 text-center">
                <div class="text-4xl font-bold mb-2">{{ $tabs }}</div>
                <div class="text-sm text-gray-400">Cambios de Pestaña</div>
            </div>
        </div>

        <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
            <h3 class="font-bold mb-3">Detalles de la Sesión</h3>
            <ul class="space-y-2 text-sm text-gray-300">
                <li><b>Estudiante:</b> {{ $session->user->name }}</li>
                <li><b>Módulo:</b> {{ $session->module->title }}</li>
                <li><b>Inicio:</b> {{ $session->started_at }}</li>
                <li><b>Duración:</b> {{ gmdate("H:i:s", $session->duration_sec ?? 0) }}</li>
                <li><b>Estado Final:</b> 
                    <span class="px-2 py-1 rounded text-xs font-bold {{ $session->status === 'flagged' ? 'bg-red-500/20 text-red-400' : 'bg-green-500/20 text-green-400' }}">
                        {{ $session->status }}
                    </span>
                </li>
            </ul>
        </div>
        
        <div class="mt-8 text-center">
            <a href="{{ route('profesor.dashboard') }}" class="inline-block px-6 py-3 bg-indigo-600 hover:bg-indigo-700 rounded-lg text-white font-bold transition">
                Volver al Dashboard
            </a>
        </div>
    </div>
</body>
</html>
