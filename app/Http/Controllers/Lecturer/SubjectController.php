<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subjects = \App\Models\Subject::latest()->get();
        return view('lecturer.subjects.index', compact('subjects'));
    }

    public function create()
    {
        return view('lecturer.subjects.create');
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects',
        ]);

        \App\Models\Subject::create($validated);

        return redirect()->route('lecturer.subjects.index')->with('success', 'Subject created successfully.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $subject = \App\Models\Subject::findOrFail($id);
        return view('lecturer.subjects.edit', compact('subject'));
    }

    public function update(\Illuminate\Http\Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects,code,' . $id,
        ]);

        $subject = \App\Models\Subject::findOrFail($id);
        $subject->update($validated);

        return redirect()->route('lecturer.subjects.index')->with('success', 'Subject updated successfully.');
    }

    public function destroy(string $id)
    {
        $subject = \App\Models\Subject::findOrFail($id);
        $subject->delete();

        return redirect()->route('lecturer.subjects.index')->with('success', 'Subject deleted successfully.');
    }
}
