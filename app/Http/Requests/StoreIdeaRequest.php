<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\IdeaStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class StoreIdeaRequest extends FormRequest
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
            'team_id' => ['nullable', 'integer'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::enum(IdeaStatus::class)],
            'links' => ['nullable', 'array'],
            'links.*' => ['url', 'max:2048'],
            'steps' => ['nullable', 'array'],
            'steps.*.description' => ['required', 'string', 'max:255'],
            'steps.*.completed' => ['required', 'boolean'],
            'image' => ['nullable', 'image', 'max:5120'], // max size in kilobytes (5MB)
            'remove_image' => ['nullable', 'boolean'], // to handle image removal
            'assignee_id' => ['nullable', 'integer', 'exists:users,id'], // validate that the assignee_id exists in the users table
            'due_date' => ['nullable', 'date'], // validate that the due_date is a valid date
        ];
    }

    /**
     * Prepare data with defaults for validation.
     * It is important to prepare the data before validation to ensure that optional fields have default values, preventing validation errors for missing fields.
     */
    #[Override]
    protected function prepareForValidation(): void
    {
        $this->merge([
            'links' => $this->input('links', []),
            'steps' => $this->input('steps', []),
            'remove_image' => filter_var($this->input('remove_image', false), FILTER_VALIDATE_BOOLEAN),
            'team_id' => $this->input('team_id') ? (int) $this->input('team_id') : null,
            'assignee_id' => $this->input('assignee_id') ? (int) $this->input('assignee_id') : null,
            'due_date' => $this->input('due_date') ? (string) $this->input('due_date') : null,
        ]);
    }
}
