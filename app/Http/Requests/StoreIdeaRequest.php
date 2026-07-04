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
            'steps.*' => ['string', 'max:255'],
            'image' => ['nullable', 'image', 'max:5120'], // max size in kilobytes (5MB)
        ];
    }

    /**
     * Prepare data with defaults for validation.
     */
    #[Override]
    protected function prepareForValidation(): void
    {
        $this->merge([
            'links' => $this->input('links', []),
            'steps' => $this->input('steps', []),
        ]);
    }
}
