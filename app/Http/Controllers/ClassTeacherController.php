<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClassTeacherRequest;
use App\Models\ClassTeacher;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClassTeacherController extends Controller
{
    public function index(SchoolClass $schoolClass): View
    {
        return view('class-teachers.index', [
            'schoolClass' => $schoolClass,
            'assignments' => $schoolClass->classAssignments()->with(['teacher.user', 'subject'])->get(),
            'teachers' => Teacher::with('user')->orderBy('id')->get(),
            'subjects' => Subject::orderBy('name')->get(),
        ]);
    }

    public function store(StoreClassTeacherRequest $request, SchoolClass $schoolClass): RedirectResponse
    {
        ClassTeacher::create([
            ...$request->validated(),
            'school_class_id' => $schoolClass->id,
        ]);

        return redirect()->route('class-teachers.index', $schoolClass)->with('status', 'Teacher assigned to class.');
    }

    public function destroy(ClassTeacher $classTeacher): RedirectResponse
    {
        $schoolClass = $classTeacher->schoolClass;
        $classTeacher->delete();

        return redirect()->route('class-teachers.index', $schoolClass)->with('status', 'Assignment removed.');
    }
}
