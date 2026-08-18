<?php

namespace App\Policies;

use App\Enums\LeaveRequestStatus;
use App\Models\LeaveRequest;
use App\Models\User;

class LeaveRequestPolicy
{
    /**
     * Listing is always scoped by the controller (Admin: all, Teacher:
     * assigned classes, Student: own requests only).
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Admin: any request. Teacher: only requests from their assigned
     * classes. Student: only their own request.
     */
    public function view(User $user, LeaveRequest $leaveRequest): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $user->teacher?->allClassIds()->contains($leaveRequest->school_class_id) ?? false;
        }

        if ($user->isStudent()) {
            return $user->student?->id === $leaveRequest->student_id;
        }

        return false;
    }

    /**
     * Only Students may submit leave requests, and only for themselves
     * (the controller must force student_id from auth()->user()->student).
     */
    public function create(User $user): bool
    {
        return $user->isStudent() && $user->student !== null;
    }

    /**
     * A Student may edit their own request only while it is still pending.
     * Admin may edit any request. Teachers do not edit request content —
     * see respond() for approve/reject.
     */
    public function update(User $user, LeaveRequest $leaveRequest): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isStudent()) {
            return $user->student?->id === $leaveRequest->student_id
                && $leaveRequest->status === LeaveRequestStatus::Pending;
        }

        return false;
    }

    /**
     * Approve/reject is limited to Admin or the Teacher assigned to the
     * requesting student's class.
     */
    public function respond(User $user, LeaveRequest $leaveRequest): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isTeacher()
            && ($user->teacher?->allClassIds()->contains($leaveRequest->school_class_id) ?? false);
    }

    /**
     * A Student may cancel their own pending request. Admin may delete any.
     */
    public function delete(User $user, LeaveRequest $leaveRequest): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isStudent()
            && $user->student?->id === $leaveRequest->student_id
            && $leaveRequest->status === LeaveRequestStatus::Pending;
    }

    public function restore(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->isAdmin();
    }
}
