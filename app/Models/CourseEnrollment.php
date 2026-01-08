<?php

// app/Models/CourseEnrollment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseEnrollment extends Model
{
    protected $fillable = ['course_id', 'user_id', 'status', 'enrolled_at'];
    protected $casts = [
        'enrolled_at' => 'datetime'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
