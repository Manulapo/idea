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
        ]);
    }
}
