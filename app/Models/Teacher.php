<?php

namespace App\Models;

use App\Policies\TeacherPolicy;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

#[Fillable(['user_id', 'employee_number', 'phone', 'hired_at'])]
#[UsePolicy(TeacherPolicy::class)]
class Teacher extends Model
{
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'hired_at' => 'date:Y-m-d',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<SchoolClass, $this>
     */
    public function homeroomClasses(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'homeroom_teacher_id');
    }

    /**
     * @return HasMany<Timetable, $this>
     */
    public function timetables(): HasMany
    {
        return $this->hasMany(Timetable::class);
    }

    /**
     * @return HasMany<ClassTeacher, $this>
     */
    public function classAssignments(): HasMany
    {
        return $this->hasMany(ClassTeacher::class);
    }

    /**
     * Classes assigned to this teacher via the class_teacher pivot (may teach
     * several subjects in the same class, so this is deduplicated by class).
     *
     * @return BelongsToMany<SchoolClass, $this>
     */
    public function assignedClasses(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'class_teacher', 'teacher_id', 'school_class_id')
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

    /**
     * All classes this teacher may act on: homeroom classes plus classes
     * assigned via the class_teacher pivot. This is the authoritative scope
     * used by AttendancePolicy, LeaveRequestPolicy, and ChatPolicy.
     *
     * @return Collection<int, int>
     */
    public function allClassIds(): Collection
    {
        return $this->homeroomClasses()->pluck('id')
            ->merge($this->assignedClasses()->pluck('school_classes.id'))
            ->unique()
            ->values();
    }
}
