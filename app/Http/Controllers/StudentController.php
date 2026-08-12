<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
        $validated = $request->validated();
        $email = $validated['email'] ?? null;
        $password = $validated['password'] ?? null;
        unset($validated['email'], $validated['password']);

        DB::transaction(function () use ($validated, $email, $password): void {
            $student = Student::create($validated);

            if ($email !== null) {
                $user = User::create([
                    'name' => $student->name,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'role' => Role::Student,
                ]);

                $student->update(['user_id' => $user->id]);
            }
        });

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
        DB::transaction(function () use ($student): void {
            $student->delete();
            $student->user?->delete();
        });

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
