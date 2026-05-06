<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionRequest extends Model
{
    protected $fillable = [
        'student_id', 'class_id', 'date', 'reason', 'status', 'teacher_note'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }
}