<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Examen - EduSecure</title>
    <style>
        :root {
            --bg-body: #0f172a;
            --bg-card: #1e293b;
            --bg-input: #334155;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent: #6366f1;
            --accent-hover: #4f46e5;
            --success: #10b981;
            --danger: #ef4444;
            --border: rgba(255,255,255,0.1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1.6;
        }

        .container {
            max-width: 640px;
            width: 100%;
            background: var(--bg-card);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid var(--border);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }

        .header {
            padding: 40px 32px;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.15), rgba(168, 85, 247, 0.15));
            border-bottom: 1px solid var(--border);
        }

        .header h1 {
            font-size: 2rem;
            margin-bottom: 8px;
            font-weight: 700;
            letter-spacing: -0.025em;
        }

        .header p { color: var(--text-muted); font-size: 1rem; }

        .form-container { padding: 32px; }

        .form-group { margin-bottom: 24px; }

        .form-group label {
            display: block;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 10px;
            font-size: 0.95rem;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid var(--border);
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.2s ease;
            background: var(--bg-input);
            color: var(--text-main);
            font-family: inherit;
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
            background: #475569;
        }

        .form-group select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23f8fafc' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 40px;
        }

        .hint {
            margin-top: 6px;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .error-messages {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
        }

        .error-messages ul {
            list-style: none;
            padding: 0;
        }

        .error-messages li {
            padding: 6px 0;
            padding-left: 28px;
            position: relative;
            color: #fca5a5;
            font-size: 0.9rem;
        }

        .error-messages li::before {
            content: '⚠️';
            position: absolute;
            left: 0;
            font-size: 1.1rem;
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--accent), #7c3aed);
            color: white;
            border: none;
            padding: 16px 32px;
            border-radius: 12px;
            font-size: 1.05rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
            position: relative;
            overflow: hidden;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .back-link {
            padding: 20px 32px;
            background: rgba(255,255,255,0.03);
            border-top: 1px solid var(--border);
            text-align: center;
        }

        .back-link a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .back-link a:hover {
            color: #818cf8;
            transform: translateX(-5px);
        }

        @media (max-width: 640px) {
            body { padding: 12px; }
            .container { border-radius: 16px; }
            .header { padding: 32px 24px; }
            .header h1 { font-size: 1.6rem; }
            .form-container { padding: 24px; }
        }
    </style>

</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧠 Crear Examen</h1>
            <p>Genera exámenes automáticos con IA</p>
        </div>

        <div class="form-container">
            @if ($errors->any())
                <div class="error-messages">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('exams.store') }}" method="POST">
                @csrf

                {{-- ✅ Curso --}}
                <div class="form-group">
                    <label for="course_id">📚 Curso</label>
                    <select id="course_id" name="course_id" required>
                        <option value="" disabled {{ old('course_id') ? '' : 'selected' }}>
                            Selecciona un curso
                        </option>

                        @foreach(($courses ?? []) as $course)
                            <option value="{{ $course->id }}"
                                {{ (string)old('course_id') === (string)$course->id ? 'selected' : '' }}>
                                {{ $course->course_id }} — {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="hint">Este examen se publicará en el curso seleccionado.</div>
                </div>

                {{-- ✅ Nivel --}}
                <div class="form-group">
                    <label for="level">⚡ Nivel</label>
                    <select id="level" name="level" required>
                        <option value="basico" {{ old('level','basico')==='basico' ? 'selected' : '' }}>Básico</option>
                        <option value="intermedio" {{ old('level')==='intermedio' ? 'selected' : '' }}>Intermedio</option>
                        <option value="avanzado" {{ old('level')==='avanzado' ? 'selected' : '' }}>Avanzado</option>
                    </select>
                </div>

                {{-- ✅ Calificación máxima --}}
                <div class="form-group">
                    <label for="score_max">📊 Calificación máxima</label>
                    <input type="number" id="score_max" name="score_max"
                           value="{{ old('score_max') }}" min="1" max="100" required
                           placeholder="Ej: 20">
                </div>

                {{-- ✅ Número de preguntas --}}
                <div class="form-group">
                    <label for="questions_count">📝 Número de preguntas</label>
                    <input type="number" id="questions_count" name="questions_count"
                           value="{{ old('questions_count') }}" min="1" max="50" required
                           placeholder="Ej: 10">
                </div>

                {{-- ✅ Tema --}}
                <div class="form-group">
                    <label for="topic">🎯 Tema del examen</label>
                    <input type="text" id="topic" name="topic"
                           value="{{ old('topic') }}" required
                           placeholder="Ej: Programación web, Matemáticas, Historia...">
                </div>

                {{-- ✅ Descripción (opcional) --}}
                <div class="form-group">
                    <label for="description">🧾 Descripción (opcional)</label>
                    <textarea id="description" name="description"
                              placeholder="Ej: Examen parcial, incluye unidad 1 y 2...">{{ old('description') }}</textarea>
                </div>

                <button type="submit" class="submit-btn">
                    ✨ Generar Examen con IA
                </button>
            </form>
        </div>

        <div class="back-link">
            {{-- Pon aquí tu ruta real del dashboard del profesor si quieres --}}
            {{-- <a href="{{ route('profesor.dashboard') }}">← Volver</a> --}}
        </div>
    </div>
</body>
</html>
