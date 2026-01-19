<?php

namespace App\Http\Controllers\Student;

use App\Models\Exam;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $exams = Exam::forStudent(auth()->user())
            ->latest()
            ->get();

        return view('student.dashboard', compact('exams'));
    }
}
