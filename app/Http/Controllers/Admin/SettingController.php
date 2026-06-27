<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = Setting::query()
            ->whereIn('key', ['bkash_number', 'nagad_number', 'rocket_number'])
            ->pluck('value', 'key');

        return view('admin.settings.index', [
            'bkashNumber' => (string) ($settings['bkash_number'] ?? ''),
            'nagadNumber' => (string) ($settings['nagad_number'] ?? ''),
            'rocketNumber' => (string) ($settings['rocket_number'] ?? ''),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bkash_number' => ['nullable', 'string', 'max:30'],
            'nagad_number' => ['nullable', 'string', 'max:30'],
            'rocket_number' => ['nullable', 'string', 'max:30'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? '']
            );
        }

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Payment settings updated successfully.');
    }
}
