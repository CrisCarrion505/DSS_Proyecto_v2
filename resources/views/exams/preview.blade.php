<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Examen Generado - EduSecure</title>
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
            color: #333;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.2rem;
            margin-bottom: 10px;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .info {
            background: rgba(255,255,255,0.2);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }

        .info-icon {
            width: 20px;
            height: 20px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #4f46e5;
        }

        .questions-container {
            padding: 40px;
        }

        .question {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 16px;
            margin-bottom: 25px;
            padding: 25px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .question:hover {
            border-color: #4f46e5;
            box-shadow: 0 10px 30px rgba(79, 70, 229, 0.15);
            transform: translateY(-2px);
        }

        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            gap: 15px;
        }

        .question-number {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .question-title {
            flex: 1;
            font-size: 1.3rem;
            font-weight: 600;
            line-height: 1.5;
            color: #1f2937;
        }

        .question-points {
            background: #10b981;
            color: white;
            padding: 8px 16px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .question-text {
            font-size: 1.1rem;
            line-height: 1.7;
            color: #374151;
            margin-bottom: 25px;
            padding: 20px;
            background: #f8fafc;
            border-radius: 12px;
            border-left: 4px solid #4f46e5;
        }

        .options {
            display: grid;
            gap: 12px;
        }

        .option {
            padding: 18px 24px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            background: white;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            font-size: 1rem;
            line-height: 1.5;
        }

        .option:hover {
            border-color: #4f46e5;
            background: #f3f4ff;
            transform: translateX(5px);
        }

        .option.correct {
            border-color: #10b981;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            font-weight: 600;
        }

        .option.correct::before {
            content: '✅';
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2rem;
        }

        .option.correct:hover {
            transform: translateX(0);
        }

        .actions {
            text-align: center;
            padding: 30px 40px;
            background: #f8fafc;
            border-top: 1px solid #e5e7eb;
        }

        .btn {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            border: none;
            padding: 14px 32px;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6b7280, #9ca3af);
            box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3);
        }

        .btn-secondary:hover {
            box-shadow: 0 8px 25px rgba(107, 114, 128, 0.4);
        }

        @media (max-width: 768px) {
            .container {
                margin: 10px;
                border-radius: 16px;
            }

            .header h1 {
                font-size: 1.8rem;
            }

            .questions-container {
                padding: 20px;
            }

            .question-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $exam['titulo'] ?? 'Examen Generado' }}</h1>
            <div class="info">
                <div class="info-item">
                    <div class="info-icon">📊</div>
                    <span>Calificación máxima: {{ $exam['score_max'] ?? 'N/A' }} pts</span>
                </div>
                <div class="info-item">
                    <div class="info-icon">📝</div>
                    <span>{{ count($exam['preguntas'] ?? []) }} preguntas</span>
                </div>
            </div>
        </div>

        <div class="questions-container">
            @foreach ($exam['preguntas'] as $idx => $q)
                <div class="question">
                    <div class="question-header">
                        <div class="question-number">{{ $idx + 1 }}</div>
                        <div class="question-title">
                            {{ $q['texto'] ?? '' }}
                        </div>
                        <div class="question-points">{{ $q['puntaje'] ?? 0 }} pts</div>
                    </div>

                    <div class="question-text">
                        {{ $q['texto'] ?? '' }}
                    </div>

                    <div class="options">
                        @foreach ($q['opciones'] as $i => $opt)
                            <div class="option @if(isset($q['correcta']) && $q['correcta'] === $i) correct @endif">
                                <strong>{{ chr(65 + $i) }}.</strong> {{ $opt }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="actions">
            <a href="{{ route('exams.create') }}" class="btn btn-secondary">← Nuevo Examen</a>
            
            <form action="{{ route('exams.publish', $exam_id) }}" method="POST" style="display:inline-block; margin-left: 10px;">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn">✅ Activar Examen</button>
            </form>
        </div>
    </div>

</body>
</html>
