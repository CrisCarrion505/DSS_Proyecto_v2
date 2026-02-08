<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Resultado del Examen</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-950 text-gray-100 min-h-screen font-sans antialiased selection:bg-indigo-500 selection:text-white">

<div class="max-w-5xl mx-auto p-6 md:py-12">
    
    <!-- Main Result Card -->
    <div class="bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl overflow-hidden">
        
        <!-- Header Section -->
        <div class="relative bg-gray-800/50 p-8 border-b border-gray-800">
            <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/10 to-purple-500/10"></div>
            <div class="relative z-10 text-center space-y-4">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium bg-gray-800 border border-gray-700 text-gray-400">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    {{ $course->course_id }} — {{ $course->name }}
                </span>
                
                <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight">
                    {{ $exam->titulo ?? 'Examen Finalizado' }}
                </h1>
                
                <div class="flex justify-center gap-3">
                    @if($result->status === 'flagged')
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-red-500/10 text-red-400 border border-red-500/20 text-sm font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            REVISIÓN REQUERIDA
                        </div>
                    @else
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-sm font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            COMPLETADO
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="p-6 md:p-8 space-y-10">
            <!-- Score Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Puntaje -->
                <div class="bg-gray-800 p-6 rounded-2xl border border-gray-700 text-center hover:border-indigo-500/50 transition-colors group">
                    <p class="text-xs uppercase tracking-widest text-gray-500 font-semibold mb-2 group-hover:text-indigo-400">Puntaje</p>
                    <p class="text-4xl font-black text-white">
                        {{ $result->score_obtained }}<span class="text-2xl text-gray-600 font-medium">/{{ $result->score_max }}</span>
                    </p>
                </div>
                
                <!-- Porcentaje -->
                <div class="bg-gray-800 p-6 rounded-2xl border border-gray-700 text-center hover:border-indigo-500/50 transition-colors group">
                    <p class="text-xs uppercase tracking-widest text-gray-500 font-semibold mb-2 group-hover:text-indigo-400">Porcentaje</p>
                    <p class="text-4xl font-black {{ $result->percentage >= 60 ? 'text-emerald-400' : 'text-rose-400' }}">
                        {{ $result->percentage }}%
                    </p>
                </div>

                <!-- Estado -->
                <div class="bg-gray-800 p-6 rounded-2xl border border-gray-700 text-center hover:border-indigo-500/50 transition-colors group">
                    <p class="text-xs uppercase tracking-widest text-gray-500 font-semibold mb-2 group-hover:text-indigo-400">Resultado</p>
                    <p class="text-4xl font-black text-white">
                         @if($result->percentage >= 60)
                            Aprobado
                        @else
                            Reprobado
                        @endif
                    </p>
                </div>
            </div>

            <!-- Proctoring Breakdown -->
            @php
                // Logica de seguridad para parsear JSON
                $metrics = [];
                if (is_string($result->proctoring_metrics)) {
                    $decoded = json_decode($result->proctoring_metrics, true);
                    $metrics = json_last_error() === JSON_ERROR_NONE ? $decoded : [];
                } elseif (is_array($result->proctoring_metrics)) {
                    $metrics = $result->proctoring_metrics;
                }

                $ui = $metrics['ui'] ?? [];
                $warningsList = $metrics['warnings'] ?? [];
                $lastMetrics = $metrics['last_metrics'] ?? [];
                $warningCount = $metrics['warning_count'] ?? 0;
                
                // Métricas UI
                $tabs = $ui['tab_hidden_count'] ?? 0;
                $blurs = $ui['blur_count'] ?? 0;
                $copyPaste = ($ui['copy_count'] ?? 0) + ($ui['paste_count'] ?? 0);
                
                // Métricas IA
                // Contar warnings de tipo 'rostro'
                $faceLoss = collect($warningsList)->where('type', 'rostro')->count();
                // Desvíos totales (vienen en last_metrics)
                $gazeDeviation = $lastMetrics['desvios_mirada'] ?? 0;
            @endphp
            
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="h-px bg-gray-800 flex-1"></div>
                    <h3 class="text-gray-400 font-medium uppercase text-sm tracking-widest">Reporte de Comportamiento</h3>
                    <div class="h-px bg-gray-800 flex-1"></div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <!-- Tabs -->
                    <div class="p-4 rounded-xl border {{ $tabs > 0 ? 'bg-amber-900/10 border-amber-500/30' : 'bg-gray-800/50 border-gray-800' }}">
                        <div class="flex items-center gap-3 mb-2">
                             <span class="text-xs font-bold uppercase text-gray-400">Pestañas</span>
                        </div>
                        <div class="text-2xl font-bold {{ $tabs > 0 ? 'text-amber-400' : 'text-gray-200' }}">{{ $tabs }}</div>
                    </div>

                    <!-- Foco -->
                    <div class="p-4 rounded-xl border {{ $blurs > 0 ? 'bg-amber-900/10 border-amber-500/30' : 'bg-gray-800/50 border-gray-800' }}">
                         <div class="flex items-center gap-3 mb-2">
                             <span class="text-xs font-bold uppercase text-gray-400">Foco</span>
                        </div>
                        <div class="text-2xl font-bold {{ $blurs > 0 ? 'text-amber-400' : 'text-gray-200' }}">{{ $blurs }}</div>
                    </div>

                    <!-- Copy/Paste -->
                    <div class="p-4 rounded-xl border {{ $copyPaste > 0 ? 'bg-rose-900/10 border-rose-500/30' : 'bg-gray-800/50 border-gray-800' }}">
                        <div class="flex items-center gap-3 mb-2">
                             <span class="text-xs font-bold uppercase text-gray-400">Copy/Paste</span>
                        </div>
                        <div class="text-2xl font-bold {{ $copyPaste > 0 ? 'text-rose-400' : 'text-gray-200' }}">{{ $copyPaste }}</div>
                    </div>

                    <!-- Rostro Perdido -->
                    <div class="p-4 rounded-xl border {{ $faceLoss > 0 ? 'bg-amber-900/10 border-amber-500/30' : 'bg-gray-800/50 border-gray-800' }}">
                        <div class="flex items-center gap-3 mb-2">
                             <span class="text-xs font-bold uppercase text-gray-400 whitespace-nowrap">Rostro</span>
                        </div>
                        <div class="text-2xl font-bold {{ $faceLoss > 0 ? 'text-amber-400' : 'text-gray-200' }}">{{ $faceLoss }}</div>
                    </div>

                     <!-- Mirada -->
                     <div class="p-4 rounded-xl border {{ $gazeDeviation > 2000 ? 'bg-amber-900/10 border-amber-500/30' : 'bg-gray-800/50 border-gray-800' }}">
                        <div class="flex items-center gap-3 mb-2">
                             <span class="text-xs font-bold uppercase text-gray-400 whitespace-nowrap">Mirada</span>
                        </div>
                        <div class="text-xl font-bold {{ $gazeDeviation > 2000 ? 'text-amber-400' : 'text-gray-200' }}">{{ $gazeDeviation }}</div>
                    </div>

                    <!-- Advertencias -->
                    <div class="p-4 rounded-xl border {{ $warningCount > 0 ? 'bg-rose-900/10 border-rose-500/30' : 'bg-gray-800/50 border-gray-800' }}">
                        <div class="flex items-center gap-3 mb-2">
                             <span class="text-xs font-bold uppercase text-gray-400">Alertas</span>
                        </div>
                        <div class="text-2xl font-bold {{ $warningCount > 0 ? 'text-rose-400' : 'text-emerald-400' }}">{{ $warningCount }}</div>
                    </div>
                </div>

                @if($warningCount > 0)
                    <div class="bg-rose-500/10 border border-rose-500/20 rounded-xl p-4 flex items-start gap-4">
                        <div class="p-2 bg-rose-500/20 rounded-full text-rose-500 shrink-0">
                             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-rose-400 font-bold text-sm uppercase mb-1">Actividad Sospechosa Detectada</h4>
                            <p class="text-rose-200/80 text-sm leading-relaxed">
                                El sistema registró múltiples incidencias durante tu sesión (Pestañas, Foco, Rostro no detectado o Desvío de mirada). 
                                Tu examen ha sido marcado con <span class="text-white font-bold">FLAGGED</span> y será revisado manualmente por el profesor para verificar su validez.
                            </p>
                        </div>
                    </div>
                @endif

            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-800/50 p-6 md:px-8 border-t border-gray-800 flex flex-col md:flex-row gap-4 justify-between items-center">
            <a href="{{ route('estudiante.dashboard') }}" class="w-full md:w-auto text-gray-400 hover:text-white text-sm font-medium transition-colors flex items-center justify-center gap-2 px-4 py-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Volver al Dashboard
            </a>
            
            <a href="{{ route('courses.show', $course) }}" class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-500 text-white px-6 py-3 rounded-xl font-bold text-sm transition-all shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/30 flex items-center justify-center gap-2">
                <span>Continuar Curso</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
        </div>

    </div>
</div>

</body>
</html>
