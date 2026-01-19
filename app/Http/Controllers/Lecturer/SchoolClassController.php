<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSchoolClassRequest;
use App\Http\Requests\UpdateSchoolClassRequest;
use App\Http\Requests\AttachStudentRequest;
use App\Http\Requests\AttachSubjectRequest;
use App\Models\SchoolClass;
use App\Models\User;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SchoolClassController extends Controller
{
    public function index(): View
    {
        $classes = SchoolClass::withCount('students')->latest()->get();

        return view('lecturer.classes.index', compact('classes'));
    }

    public function create(): View
    {
        return view('lecturer.classes.create');
    }

    public function store(StoreSchoolClassRequest $request): RedirectResponse
    {
        SchoolClass::create($request->validated());

        return redirect()
            ->route('lecturer.classes.index')
            ->with('success', 'Class created successfully.');
    }

    public function show(SchoolClass $class): View
    {
        $class->load(['students', 'subjects']);

        $allStudents = User::role('student')
            ->whereDoesntHave('classes', fn ($q) =>
                $q->where('classes.id', $class->id)
            )->get();

        $allSubjects = Subject::whereDoesntHave('classes', fn ($q) =>
            $q->where('classes.id', $class->id)
        )->get();

        return view(
            'lecturer.classes.show',
            compact('class', 'allStudents', 'allSubjects')
        );
    }

    public function edit(SchoolClass $class): View
    {
        return view('lecturer.classes.edit', compact('class'));
    }

    public function update(
        UpdateSchoolClassRequest $request,
        SchoolClass $class
    ): RedirectResponse {
        $class->update($request->validated());

        return redirect()
            ->route('lecturer.classes.index')
            ->with('success', 'Class updated successfully.');
    }

    public function destroy(SchoolClass $class): RedirectResponse
    {
        $class->delete();

        return redirect()
            ->route('lecturer.classes.index')
            ->with('success', 'Class deleted successfully.');
    }

    public function attachStudent(
        AttachStudentRequest $request,
        SchoolClass $class
    ): RedirectResponse {
        $class->students()->attach($request->user_id);

        return back()->with('success', 'Student assigned to class.');
    }

    public function detachStudent(
        SchoolClass $class,
        User $user
    ): RedirectResponse {
        $class->students()->detach($user->id);

        return back()->with('success', 'Student removed from class.');
    }

    public function attachSubject(
        AttachSubjectRequest $request,
        SchoolClass $class
    ): RedirectResponse {
        $class->subjects()->attach($request->subject_id);

        return back()->with('success', 'Subject assigned to class.');
    }

    public function detachSubject(
        SchoolClass $class,
        Subject $subject
    ): RedirectResponse {
        $class->subjects()->detach($subject->id);

        return back()->with('success', 'Subject removed from class.');
    }
}
