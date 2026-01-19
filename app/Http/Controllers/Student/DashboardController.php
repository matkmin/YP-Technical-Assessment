<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $classIds = $user->classes()->pluck('classes.id');

        $exams = \App\Models\Exam::where('is_active', true)
            ->whereHas('classes', function ($q) use ($classIds) {
                $q->whereIn('classes.id', $classIds);
            })
            ->with([
                'subject',
                'attempts' => function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                }
            ])
            ->latest()
            ->get();

        return view('student.dashboard', compact('exams'));
    }
}
