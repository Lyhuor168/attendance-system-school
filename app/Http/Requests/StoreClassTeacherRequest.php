<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClassTeacherRequest extends FormRequest
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
        $schoolClass = $this->route('schoolClass');

        return [
            'teacher_id' => [
                'required',
                'exists:teachers,id',
                Rule::unique('class_teacher')->where('school_class_id', $schoolClass->id)->where('subject_id', $this->input('subject_id')),
            ],
            'subject_id' => ['required', 'exists:subjects,id'],
        ];
    }
}
