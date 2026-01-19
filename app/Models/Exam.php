<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = ['title', 'subject_id', 'duration_minutes', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function classes()
    {
        return $this->belongsToMany(SchoolClass::class, 'exam_class', 'exam_id', 'class_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }
}
