<?php

namespace App\Http\Requests;

use App\Enums\LeaveRequestStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RespondLeaveRequestRequest extends FormRequest
{
    /**
     * Only Admin or the Teacher assigned to the requesting student's class
     * may approve/reject (see LeaveRequestPolicy::respond).
     */
    public function authorize(): bool
    {
        return $this->user()->can('respond', $this->route('leaveRequest'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in([LeaveRequestStatus::Approved->value, LeaveRequestStatus::Rejected->value])],
        ];
    }
}
