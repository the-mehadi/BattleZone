<?php

namespace App\Http\Requests;

use App\Models\Room;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResultStoreRequest extends FormRequest
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
        $results = collect($this->input('results', []))
            ->filter(fn ($result) => is_array($result))
            ->map(fn (array $result) => [
                'squad_id' => isset($result['squad_id']) ? (int) $result['squad_id'] : null,
                'position' => isset($result['position']) ? (int) $result['position'] : null,
                'total_kills' => isset($result['total_kills']) ? (int) $result['total_kills'] : null,
            ])
            ->values()
            ->all();

        $this->merge([
            'results' => $results,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $room = $this->room();

        return [
            'results' => ['required', 'array', 'min:1'],
            'results.*.squad_id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('squads', 'id')->where(
                    fn ($query) => $query
                        ->where('room_id', $room->id)
                        ->where('status', 'approved')
                ),
            ],
            'results.*.position' => ['required', 'integer', 'min:1', 'distinct'],
            'results.*.total_kills' => ['required', 'integer', 'min:0'],
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
            'results.*.squad_id.exists' => 'Each submitted squad must be an approved squad in this room.',
            'results.*.squad_id.distinct' => 'Each squad can only appear once in the result list.',
            'results.*.position.distinct' => 'Duplicate positions are not allowed.',
        ];
    }

    protected function room(): Room
    {
        /** @var \App\Models\Room $room */
        $room = $this->route('room');

        return $room;
    }
}
