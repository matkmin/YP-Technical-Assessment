<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $exams = \App\Models\Exam::with(['subject'])->withCount(['questions', 'classes'])->latest()->get();
        return view('lecturer.exams.index', compact('exams'));
    }

    public function create()
    {
        $subjects = \App\Models\Subject::all();
        return view('lecturer.exams.create', compact('subjects'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'duration_minutes' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        \App\Models\Exam::create($validated);

        return redirect()->route('lecturer.exams.index')->with('success', 'Exam created successfully.');
    }

    public function show(\App\Models\Exam $exam)
    {
        $exam->load(['questions.options', 'classes']);
        $allClasses = \App\Models\SchoolClass::whereDoesntHave('exams', function ($q) use ($exam) {
            $q->where('exams.id', $exam->id);
        })->get();

        return view('lecturer.exams.show', compact('exam', 'allClasses'));
    }

    public function edit(\App\Models\Exam $exam)
    {
        $subjects = \App\Models\Subject::all();
        return view('lecturer.exams.edit', compact('exam', 'subjects'));
    }

    public function update(\Illuminate\Http\Request $request, \App\Models\Exam $exam)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'duration_minutes' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active'); // handle checkbox

        $exam->update($validated);

        return redirect()->route('lecturer.exams.index')->with('success', 'Exam updated successfully.');
    }

    public function destroy(\App\Models\Exam $exam)
    {
        $exam->delete();
        return redirect()->route('lecturer.exams.index')->with('success', 'Exam deleted successfully.');
    }

    public function attachClass(\Illuminate\Http\Request $request, \App\Models\Exam $exam)
    {
        $request->validate(['class_id' => 'required|exists:classes,id']);
        $exam->classes()->attach($request->class_id);
        return back()->with('success', 'Exam assigned to class.');
    }

    public function detachClass(\App\Models\Exam $exam, \App\Models\SchoolClass $class)
    {
        $exam->classes()->detach($class->id);
        return back()->with('success', 'Exam unassigned from class.');
    }
}
