<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(): View
    {
        $subjects = Subject::latest()->get();

        return view('lecturer.subjects.index', compact('subjects'));
    }

    public function create(): View
    {
        return view('lecturer.subjects.create');
    }

    public function store(StoreSubjectRequest $request): RedirectResponse
    {
        Subject::create($request->validated());

        return redirect()
            ->route('lecturer.subjects.index')
            ->with('success', 'Subject created successfully.');
    }

    public function edit(Subject $subject): View
    {
        return view('lecturer.subjects.edit', compact('subject'));
    }

    public function update(
        UpdateSubjectRequest $request,
        Subject $subject
    ): RedirectResponse {
        $subject->update($request->validated());

        return redirect()
            ->route('lecturer.subjects.index')
            ->with('success', 'Subject updated successfully.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        $subject->delete();

        return redirect()
            ->route('lecturer.subjects.index')
            ->with('success', 'Subject deleted successfully.');
    }
}
