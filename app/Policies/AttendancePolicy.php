<?php

namespace App\Policies;

use App\Models\AttendanceRecord;
use App\Models\User;

class AttendancePolicy
{
    /**
     * Listing is always scoped by the controller (Admin: all, Teacher:
     * assigned classes, Student: own records) — this just gates the
     * ability to hit the endpoint at all.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Admin: any record. Teacher: only records for their assigned classes
     * (homeroom + class_teacher pivot). Student: only their own record.
     */
    public function view(User $user, AttendanceRecord $attendanceRecord): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $user->teacher?->allClassIds()->contains($attendanceRecord->school_class_id) ?? false;
        }

        if ($user->isStudent()) {
            return $user->student?->id === $attendanceRecord->student_id;
        }

        return false;
    }

    /**
     * Only Admin and assigned Teachers may take/record attendance.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    /**
     * Admin: any record. Teacher: only for their assigned classes.
     * Students can never edit attendance, regardless of ownership.
     */
    public function update(User $user, AttendanceRecord $attendanceRecord): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $user->teacher?->allClassIds()->contains($attendanceRecord->school_class_id) ?? false;
        }

        return false;
    }

    /**
     * Same rule as update: Admin or the assigned Teacher for that class.
     */
    public function delete(User $user, AttendanceRecord $attendanceRecord): bool
    {
        return $this->update($user, $attendanceRecord);
    }

    public function restore(User $user, AttendanceRecord $attendanceRecord): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, AttendanceRecord $attendanceRecord): bool
    {
        return $user->isAdmin();
    }
}
