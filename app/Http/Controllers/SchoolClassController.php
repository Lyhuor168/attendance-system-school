<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolClassRequest;
use App\Http\Requests\UpdateSchoolClassRequest;
use App\Models\SchoolClass;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class SchoolClassController extends Controller
{
    public function index(): View
    {
        return view('classes.index', [
            'classes' => SchoolClass::with('homeroomTeacher.user')->orderBy('name')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('classes.create', ['teachers' => $this->teacherOptions()]);
    }

    public function store(StoreSchoolClassRequest $request): RedirectResponse
    {
        SchoolClass::create($request->validated());

        return redirect()->route('classes.index')->with('status', 'Class created.');
    }

    public function edit(SchoolClass $schoolClass): View
    {
        return view('classes.edit', [
            'schoolClass' => $schoolClass,
            'teachers' => $this->teacherOptions(),
        ]);
    }

    public function update(UpdateSchoolClassRequest $request, SchoolClass $schoolClass): RedirectResponse
    {
        $schoolClass->update($request->validated());

        return redirect()->route('classes.index')->with('status', 'Class updated.');
    }

    public function destroy(SchoolClass $schoolClass): RedirectResponse
    {
        $schoolClass->delete();

        return redirect()->route('classes.index')->with('status', 'Class deleted.');
    }

    /**
     * @return Collection<int, Teacher>
     */
    private function teacherOptions(): Collection
    {
        return Teacher::with('user')->get();
    }
}
