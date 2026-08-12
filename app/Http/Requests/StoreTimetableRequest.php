<?php

namespace App\Http\Requests;

use App\Enums\DayOfWeek;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTimetableRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
        return [
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'teacher_id' => ['required', 'exists:teachers,id'],
            'day_of_week' => ['required', Rule::enum(DayOfWeek::class)],
            'start_time' => [
                'required',
                'date_format:H:i',
                Rule::unique('timetables')->where('school_class_id', $this->input('school_class_id'))
                    ->where('day_of_week', $this->input('day_of_week')),
            ],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ];
    }
}
