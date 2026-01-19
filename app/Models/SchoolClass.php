<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    protected $table = 'classes';
    protected $fillable = ['name'];

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'class_subject', 'class_id', 'subject_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'student_class', 'class_id', 'user_id');
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_class', 'class_id', 'exam_id');
    }
}
