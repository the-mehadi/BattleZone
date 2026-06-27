<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RoomRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'exists:categories,id'],
            'map' => ['required', 'string'],
            'match_time' => ['required', 'date', 'after:now'],
            'entry_fee' => ['required', 'numeric', 'min:0'],
            'total_prize' => ['required', 'numeric', 'min:0'],
            'room_code' => ['nullable', 'string', 'max:20'],
            'room_password' => ['nullable', 'string', 'max:20'],
            'prizes' => ['nullable', 'array'],
            'prizes.*.position' => ['required_with:prizes', 'integer', 'between:1,10', 'distinct'],
            'prizes.*.prize_amount' => ['required_with:prizes', 'numeric', 'min:0'],
        ];
    }
}
