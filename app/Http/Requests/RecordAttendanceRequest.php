<?php

namespace App\Http\Requests;

use App\Enums\AttendanceStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RecordAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Class-level authorization is enforced by the 'can:record,schoolClass' route middleware.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $schoolClass = $this->route('schoolClass');

        return [
            'date' => ['required', 'date'],
            'attendance' => ['required', 'array'],
            'attendance.*.student_id' => [
                'required',
                Rule::exists('students', 'id')->where('school_class_id', $schoolClass->id),
            ],
            'attendance.*.status' => ['required', Rule::enum(AttendanceStatus::class)],
            'attendance.*.remarks' => ['nullable', 'string', 'max:255'],
        ];
    }
}
