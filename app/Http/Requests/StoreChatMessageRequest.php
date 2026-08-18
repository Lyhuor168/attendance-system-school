<?php

namespace App\Http\Requests;

use App\Models\ChatMessage;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreChatMessageRequest extends FormRequest
{
    /**
     * Only a Teacher assigned to this student's class, or the student's own
     * Guardian, may send a message in this thread (see ChatPolicy::create).
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', [ChatMessage::class, $this->route('student')]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:2000'],
        ];
    }
}
