<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
use App\Http\Requests\RecordAttendanceRequest;
use App\Models\AttendanceRecord;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AttendanceRecordController extends Controller
{
    public function index(): View
    {
        $user = request()->user();

        $classes = $user->isAdmin()
            ? SchoolClass::with('homeroomTeacher.user')->orderBy('name')->get()
            : SchoolClass::whereIn('id', $user->teacher?->allClassIds() ?? collect())->orderBy('name')->get();

        return view('attendance.index', ['classes' => $classes]);
    }

    public function create(SchoolClass $schoolClass): View
    {
        $this->authorize('record', $schoolClass);

        $date = Carbon::today();

        $students = $schoolClass->students()
            ->orderBy('name')
            ->get()
            ->map(function ($student) use ($date) {
                $student->existingRecord = $student->attendanceRecords()
                    ->whereDate('date', $date)
                    ->first();

                return $student;
            });

        return view('attendance.record', [
            'schoolClass' => $schoolClass,
            'date' => $date,
            'students' => $students,
            'statuses' => AttendanceStatus::cases(),
        ]);
    }

    public function store(RecordAttendanceRequest $request, SchoolClass $schoolClass): RedirectResponse
    {
        $this->authorize('record', $schoolClass);

        $validated = $request->validated();

        DB::transaction(function () use ($validated, $schoolClass): void {
            foreach ($validated['attendance'] as $entry) {
                AttendanceRecord::updateOrCreate(
                    [
                        'student_id' => $entry['student_id'],
                        'date' => $validated['date'],
                    ],
                    [
                        'school_class_id' => $schoolClass->id,
                        'status' => $entry['status'],
                        'remarks' => $entry['remarks'] ?? null,
                        'recorded_by' => request()->user()->id,
                    ]
                );
            }
        });

        return redirect()->route('attendance.index')->with('status', 'Attendance recorded.');
    }

    public function show(SchoolClass $schoolClass, string $date): View
    {
        $this->authorize('record', $schoolClass);

        $records = AttendanceRecord::with('student')
            ->where('school_class_id', $schoolClass->id)
            ->whereDate('date', $date)
            ->get();

        return view('attendance.show', [
            'schoolClass' => $schoolClass,
            'date' => $date,
            'records' => $records,
        ]);
    }
}
