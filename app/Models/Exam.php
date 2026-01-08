<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ExamResult; // ✅ AGREGAR ESTO

class Exam extends Model
{
    protected $fillable = [
        'course_id',
        'teacher_id',
        'titulo',
        'description',
        'score_max',
        'questions_count',
        'topic',
        'level',
        'preguntas',
        'is_active'
    ];

    protected $casts = [
        'preguntas' => 'array',
        'is_active' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // ✅ AHORA FUNCIONA - RelacionResults con ExamResult importado
    public function results()
    {
        return $this->hasMany(ExamResult::class); // ✅ Sin comillas
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
