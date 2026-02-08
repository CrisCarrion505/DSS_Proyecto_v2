<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeSession extends Model
{
    protected $fillable = [
        'knowledge_module_id',
        'user_id',
        'started_at',
        'ended_at',
        'duration_sec',
        'proctoring_metrics',
        'alert_count',
        'answers',
        'score',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'proctoring_metrics' => 'array',
        'answers' => 'array',
    ];

    public function module()
    {
        return $this->belongsTo(KnowledgeModule::class, 'knowledge_module_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user()
    {
        return $this->student();
    }
}
