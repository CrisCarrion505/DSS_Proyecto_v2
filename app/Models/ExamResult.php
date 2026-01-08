<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    protected $table = 'exam_results';

    protected $fillable = [
        'exam_id',
        'user_id',
        'score_obtained',
        'score_max',
        'percentage',
        'proctoring_metrics',
        'evaluation',
        'status'
    ];

    protected $casts = [
        'proctoring_metrics' => 'array',
        'evaluation' => 'array',
    ];

    // Relación: un resultado pertenece a un examen
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    // Relación: un resultado pertenece a un estudiante
    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scope: resultados completados
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Scope: resultados flagged (sospecha)
    public function scopeFlagged($query)
    {
        return $query->where('status', 'flagged');
    }
}
