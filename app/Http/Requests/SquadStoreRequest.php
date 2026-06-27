<?php

namespace App\Http\Requests;

use App\Models\Room;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SquadStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === 'player';
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $players = collect($this->input('players', []))
            ->filter(fn ($player) => is_array($player))
            ->map(fn (array $player) => [
                'ingame_name' => isset($player['ingame_name']) ? trim((string) $player['ingame_name']) : null,
                'ingame_id' => isset($player['ingame_id']) ? trim((string) $player['ingame_id']) : null,
            ])
            ->values()
            ->all();

        $this->merge([
            'players' => $players,
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
        $requiredPlayers = $room->category->max_players;

        return [
            'players' => ['required', 'array', 'size:'.$requiredPlayers],
            'players.*.ingame_name' => ['required', 'string', 'max:50'],
            'players.*.ingame_id' => ['required', 'string', 'max:20'],
        ];
    }

    /**
     * Get the validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        $requiredPlayers = $this->room()->category->max_players;

        return [
            'players.size' => "Exactly {$requiredPlayers} player entries are required for this room.",
        ];
    }

    protected function room(): Room
    {
        /** @var \App\Models\Room $room */
        $room = $this->route('room');

        return $room->loadMissing('category');
    }
}
