<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function index(): View
    {
        return view('teachers.index', [
            'teachers' => Teacher::with('user')->orderBy('employee_number')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('teachers.create');
    }

    public function store(StoreTeacherRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request): void {
            $user = User::create([
                'name' => $request->validated('name'),
                'email' => $request->validated('email'),
                'password' => Hash::make($request->validated('password')),
                'role' => Role::Teacher,
            ]);

            Teacher::create([
                'user_id' => $user->id,
                'employee_number' => $request->validated('employee_number'),
                'phone' => $request->validated('phone'),
                'hired_at' => $request->validated('hired_at'),
            ]);
        });

        return redirect()->route('teachers.index')->with('status', 'Teacher created.');
    }

    public function edit(Teacher $teacher): View
    {
        return view('teachers.edit', ['teacher' => $teacher->load('user')]);
    }

    public function update(UpdateTeacherRequest $request, Teacher $teacher): RedirectResponse
    {
        DB::transaction(function () use ($request, $teacher): void {
            $teacher->user->update([
                'name' => $request->validated('name'),
                'email' => $request->validated('email'),
            ]);

            $teacher->update([
                'employee_number' => $request->validated('employee_number'),
                'phone' => $request->validated('phone'),
                'hired_at' => $request->validated('hired_at'),
            ]);
        });

        return redirect()->route('teachers.index')->with('status', 'Teacher updated.');
    }

    public function destroy(Teacher $teacher): RedirectResponse
    {
        DB::transaction(function () use ($teacher): void {
            $teacher->delete();
            $teacher->user?->delete();
        });

        return redirect()->route('teachers.index')->with('status', 'Teacher deleted.');
    }
}
