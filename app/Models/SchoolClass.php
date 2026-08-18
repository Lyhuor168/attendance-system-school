<?php

namespace App\Models;

use App\Policies\SchoolClassPolicy;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'grade_level', 'homeroom_teacher_id'])]
#[UsePolicy(SchoolClassPolicy::class)]
class SchoolClass extends Model
{
    use HasFactory;

    /**
     * @return BelongsTo<Teacher, $this>
     */
    public function homeroomTeacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'homeroom_teacher_id');
    }

    /**
     * @return HasMany<Student, $this>
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * @return HasMany<Timetable, $this>
     */
    public function timetables(): HasMany
    {
        return $this->hasMany(Timetable::class);
    }

    /**
     * @return HasMany<AttendanceRecord, $this>
     */
    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    /**
     * @return HasMany<ClassTeacher, $this>
     */
    public function classAssignments(): HasMany
    {
        return $this->hasMany(ClassTeacher::class);
    }

    /**
     * Teachers assigned to this class via the class_teacher pivot (does not
     * include the homeroom teacher unless they are also pivot-assigned).
     *
     * @return BelongsToMany<Teacher, $this>
     */
    public function assignedTeachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'class_teacher', 'school_class_id', 'teacher_id')
            ->withPivot('subject_id')
            ->withTimestamps();
    }

    /**
     * @return HasMany<LeaveRequest, $this>
     */
    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
