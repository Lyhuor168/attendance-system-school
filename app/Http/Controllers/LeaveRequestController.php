<?php

namespace App\Http\Controllers;

use App\Enums\LeaveRequestStatus;
use App\Http\Requests\RespondLeaveRequestRequest;
use App\Http\Requests\StoreLeaveRequestRequest;
use App\Models\LeaveRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    public function index(): View
    {
        $user = request()->user();

        $query = LeaveRequest::with(['student', 'schoolClass', 'teacher.user']);

        if ($user->isAdmin()) {
            // Global view: no additional scoping.
        } elseif ($user->isTeacher()) {
            $query->whereIn('school_class_id', $user->teacher?->allClassIds() ?? collect());
        } else {
            // Student: never trust request params — always resolve from the
            // authenticated user's own linked student record.
            $query->where('student_id', $user->student?->id ?? 0);
        }

        return view('leave-requests.index', [
            'leaveRequests' => $query->latest()->paginate(15),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', LeaveRequest::class);

        return view('leave-requests.create');
    }

    public function store(StoreLeaveRequestRequest $request): RedirectResponse
    {
        $student = $request->user()->student;

        LeaveRequest::create([
            ...$request->validated(),
            'student_id' => $student->id,
            'school_class_id' => $student->school_class_id,
            'teacher_id' => $student->schoolClass->homeroom_teacher_id,
            'status' => LeaveRequestStatus::Pending,
        ]);

        return redirect()->route('leave-requests.index')->with('status', 'Leave request submitted.');
    }

    public function respond(RespondLeaveRequestRequest $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        $leaveRequest->update([
            'status' => $request->validated('status'),
            'reviewed_by' => $request->user()->id,
        ]);

        return redirect()->route('leave-requests.index')->with('status', 'Leave request updated.');
    }

    public function destroy(LeaveRequest $leaveRequest): RedirectResponse
    {
        $this->authorize('delete', $leaveRequest);

        $leaveRequest->delete();

        return redirect()->route('leave-requests.index')->with('status', 'Leave request cancelled.');
    }
}
