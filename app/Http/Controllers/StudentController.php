<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        return view('students.index', [
            'students' => Student::with('schoolClass')->orderBy('name')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('students.create', ['classes' => $this->classOptions()]);
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        Student::create($request->validated());

        return redirect()->route('students.index')->with('status', 'Student created.');
    }

    public function edit(Student $student): View
    {
        return view('students.edit', [
            'student' => $student,
            'classes' => $this->classOptions(),
        ]);
    }

    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        $student->update($request->validated());

        return redirect()->route('students.index')->with('status', 'Student updated.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $student->delete();

        return redirect()->route('students.index')->with('status', 'Student deleted.');
    }

    /**
     * @return Collection<int, SchoolClass>
     */
    private function classOptions(): Collection
    {
        return SchoolClass::orderBy('name')->get();
    }
}
