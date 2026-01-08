<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Examen - EduSecure</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            max-width: 500px;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.2rem;
            margin-bottom: 10px;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .header p {
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .form-container {
            padding: 40px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
            font-size: 1rem;
        }

        .form-group input {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8fafc;
            font-family: inherit;
        }

        .form-group input:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
            background: white;
            transform: translateY(-1px);
        }

        .error-messages {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
        }

        .error-messages ul {
            list-style: none;
            padding: 0;
        }

        .error-messages li {
            padding: 5px 0;
            position: relative;
            padding-left: 25px;
        }

        .error-messages li::before {
            content: '⚠️';
            position: absolute;
            left: 0;
            font-size: 1.1rem;
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            padding: 18px 32px;
            border-radius: 12px;
            font-size: 1.2rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            position: relative;
            overflow: hidden;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
        }

        .submit-btn:active {
            transform: translateY(-1px);
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .back-link {
            text-align: center;
            padding: 20px 40px;
            background: #f8fafc;
            border-top: 1px solid #e5e7eb;
        }

        .back-link a {
            color: #4f46e5;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .back-link a:hover {
            color: #7c3aed;
            transform: translateX(-5px);
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }

            .container {
                margin: 10px;
                border-radius: 16px;
            }

            .header h1 {
                font-size: 1.8rem;
            }

            .form-container {
                padding: 25px;
            }
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

                <div class="form-group">
                    <label for="score_max">📊 Calificación máxima</label>
                    <input type="number" id="score_max" name="score_max"
                           value="{{ old('score_max') }}" min="1" max="100" required
                           placeholder="Ej: 20">
                </div>

                <div class="form-group">
                    <label for="questions_count">📝 Número de preguntas</label>
                    <input type="number" id="questions_count" name="questions_count"
                           value="{{ old('questions_count') }}" min="1" max="50" required
                           placeholder="Ej: 10">
                </div>

                <div class="form-group">
                    <label for="topic">🎯 Tema del examen</label>
                    <input type="text" id="topic" name="topic"
                           value="{{ old('topic') }}" required
                           placeholder="Ej: Programación web, Matemáticas, Historia...">
                </div>

                <button type="submit" class="submit-btn">
                    ✨ Generar Examen con IA
                </button>
            </form>
        </div>

        <div class="back-link">
            {{-- <a href="{{ route('examen.show') }}">← Volver al panel principal</a> --}}
        </div>
    </div>
</body>
</html>
