<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === 'moderator';
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $prizes = collect($this->input('prizes', []))
            ->filter(fn ($prize) => is_array($prize))
            ->map(fn (array $prize) => [
                'position' => isset($prize['position']) ? (int) $prize['position'] : null,
                'prize_amount' => isset($prize['prize_amount']) ? (float) $prize['prize_amount'] : null,
            ])
            ->values()
            ->all();

        $this->merge([
            'prizes' => $prizes,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'squad_type' => ['required', Rule::in(['squad', 'duo', 'solo'])],
            'map' => ['required', 'string'],
            'rules' => ['required', 'string'],
            'entry_fee' => ['required', 'numeric', 'min:0'],
            'kill_point' => ['required', 'numeric', 'min:0'],
            'match_duration' => ['required', 'integer', 'min:1'],
            'prizes' => ['required', 'array', 'min:3'],
            'prizes.*.position' => ['required', 'integer', 'between:1,10', 'distinct'],
            'prizes.*.prize_amount' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * Get the validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'prizes.required' => 'At least the 1st, 2nd, and 3rd prizes are required.',
            'prizes.min' => 'At least the 1st, 2nd, and 3rd prizes are required.',
            'prizes.*.position.distinct' => 'Each prize position must be unique.',
        ];
    }
}
