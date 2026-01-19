<?php

namespace App\Models;

use App\Models\Subject;
use App\Models\Question;
use App\Models\ExamAttempt;
use App\Models\SchoolClass;
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
    
    public function scopeForStudent($query, $user)
    {
        return $query
            ->where('is_active', true)
            ->whereHas('classes', fn ($q) =>
                $q->whereIn('classes.id', $user->classes()->pluck('classes.id'))
            )
            ->with([
                'subject',
                'attempts' => fn ($q) => $q->where('user_id', $user->id),
            ]);
    }
}
