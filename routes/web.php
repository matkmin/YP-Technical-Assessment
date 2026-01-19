<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Lecturer\DashboardController as LecturerDashboard;
use App\Http\Controllers\Lecturer\SubjectController;
use App\Http\Controllers\Lecturer\SchoolClassController;
use App\Http\Controllers\Lecturer\ExamController as LecturerExamController;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\Student\ExamController as StudentExamController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->hasRole('lecturer')) {
            return redirect()->route('lecturer.dashboard');
        }
        return redirect()->route('student.dashboard');
    })->name('dashboard');

    Route::middleware(['role:lecturer'])->prefix('lecturer')->name('lecturer.')->group(function () {
        Route::get('/dashboard', [LecturerDashboard::class, 'index'])->name('dashboard');
        Route::resource('subjects', SubjectController::class);

        Route::post('classes/{class}/students', [SchoolClassController::class, 'attachStudent'])->name('classes.students.store');
        Route::delete('classes/{class}/students/{user}', [SchoolClassController::class, 'detachStudent'])->name('classes.students.destroy');
        Route::post('classes/{class}/subjects', [SchoolClassController::class, 'attachSubject'])->name('classes.subjects.store');
        Route::delete('classes/{class}/subjects/{subject}', [SchoolClassController::class, 'detachSubject'])->name('classes.subjects.destroy');
        Route::resource('classes', SchoolClassController::class);

        Route::resource('exams', LecturerExamController::class);
        Route::resource('exams.questions', \App\Http\Controllers\Lecturer\QuestionController::class)->shallow();

        Route::post('exams/{exam}/classes', [LecturerExamController::class, 'attachClass'])->name('exams.classes.store');
        Route::delete('exams/{exam}/classes/{class}', [LecturerExamController::class, 'detachClass'])->name('exams.classes.destroy');
    });

    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentDashboard::class, 'index'])->name('dashboard');
        Route::get('/exams/{exam}', [StudentExamController::class, 'show'])->name('exams.show');
        Route::post('/exams/{exam}/start', [StudentExamController::class, 'start'])->name('exams.start'); // Was store
        Route::post('/exams/{exam}/submit', [StudentExamController::class, 'submit'])->name('exams.submit');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
