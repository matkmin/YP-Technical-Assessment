<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classes = \App\Models\SchoolClass::withCount('students')->latest()->get();
        return view('lecturer.classes.index', compact('classes'));
    }

    public function create()
    {
        return view('lecturer.classes.create');
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        \App\Models\SchoolClass::create($validated);

        return redirect()->route('lecturer.classes.index')->with('success', 'Class created successfully.');
    }

    public function show(\App\Models\SchoolClass $class)
    {
        $class->load(['students', 'subjects']);
        $allStudents = \App\Models\User::role('student')->whereDoesntHave('classes', function ($q) use ($class) {
            $q->where('classes.id', $class->id);
        })->get();

        $allSubjects = \App\Models\Subject::whereDoesntHave('classes', function ($q) use ($class) {
            $q->where('classes.id', $class->id);
        })->get();

        return view('lecturer.classes.show', compact('class', 'allStudents', 'allSubjects'));
    }

    public function edit(\App\Models\SchoolClass $class)
    {
        return view('lecturer.classes.edit', compact('class'));
    }

    public function update(\Illuminate\Http\Request $request, \App\Models\SchoolClass $class)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $class->update($validated);

        return redirect()->route('lecturer.classes.index')->with('success', 'Class updated successfully.');
    }

    public function destroy(\App\Models\SchoolClass $class)
    {
        $class->delete();
        return redirect()->route('lecturer.classes.index')->with('success', 'Class deleted successfully.');
    }

    public function attachStudent(\Illuminate\Http\Request $request, \App\Models\SchoolClass $class)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        $class->students()->attach($request->user_id);
        return back()->with('success', 'Student assigned to class.');
    }

    public function detachStudent(\App\Models\SchoolClass $class, \App\Models\User $user)
    {
        $class->students()->detach($user->id);
        return back()->with('success', 'Student removed from class.');
    }

    public function attachSubject(\Illuminate\Http\Request $request, \App\Models\SchoolClass $class)
    {
        $request->validate(['subject_id' => 'required|exists:subjects,id']);
        $class->subjects()->attach($request->subject_id);
        return back()->with('success', 'Subject assigned to class.');
    }

    public function detachSubject(\App\Models\SchoolClass $class, \App\Models\Subject $subject)
    {
        $class->subjects()->detach($subject->id);
        return back()->with('success', 'Subject removed from class.');
    }
}
