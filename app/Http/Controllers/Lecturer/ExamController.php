<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExamRequest;
use App\Http\Requests\UpdateExamRequest;
use App\Http\Requests\AttachClassRequest;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExamController extends Controller
{
    public function __construct(
        protected Exam $exams,
        protected Subject $subjects,
        protected SchoolClass $classes
    ) {
    }

    public function index(): View
    {
        $exams = $this->exams
            ->with('subject')
            ->withCount(['questions', 'classes'])
            ->latest()
            ->get();

        return view('lecturer.exams.index', compact('exams'));
    }

    public function create(): View
    {
        $subjects = $this->subjects->all();

        return view('lecturer.exams.create', compact('subjects'));
    }

    public function store(StoreExamRequest $request): RedirectResponse
    {
        $this->exams->create($request->validated());

        return redirect()
            ->route('lecturer.exams.index')
            ->with('success', 'Exam created successfully.');
    }

    public function show(Exam $exam): View
    {
        $exam->load(['questions.options', 'classes']);

        $allClasses = $this->classes
            ->whereDoesntHave(
                'exams',
                fn($q) =>
                $q->where('exams.id', $exam->id)
            )->get();

        return view('lecturer.exams.show', compact('exam', 'allClasses'));
    }

    public function edit(Exam $exam): View
    {
        $subjects = $this->subjects->all();

        return view('lecturer.exams.edit', compact('exam', 'subjects'));
    }

    public function update(UpdateExamRequest $request, Exam $exam): RedirectResponse
    {
        $exam->update($request->validated());

        return redirect()
            ->route('lecturer.exams.index')
            ->with('success', 'Exam updated successfully.');
    }

    public function destroy(Exam $exam): RedirectResponse
    {
        $exam->delete();

        return redirect()
            ->route('lecturer.exams.index')
            ->with('success', 'Exam deleted successfully.');
    }

    public function attachClass(AttachClassRequest $request, Exam $exam): RedirectResponse
    {
        $exam->classes()->attach($request->class_id);

        return back()->with('success', 'Exam assigned to class.');
    }

    public function detachClass(Exam $exam, SchoolClass $class): RedirectResponse
    {
        $exam->classes()->detach($class->id);

        return back()->with('success', 'Exam unassigned from class.');
    }
}
