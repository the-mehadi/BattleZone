<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->withCount(['categoryPrizes', 'rooms'])
            ->latest()
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create', [
            'category' => new Category(['status' => 'active']),
            'prizes' => $this->defaultPrizes(),
        ]);
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request): void {
            $category = Category::create([
                'name' => $request->string('name')->toString(),
                'squad_type' => $request->string('squad_type')->toString(),
                'max_players' => $this->resolveMaxPlayers($request->string('squad_type')->toString()),
                'map' => $request->string('map')->toString(),
                'rules' => $request->string('rules')->toString(),
                'entry_fee' => $request->input('entry_fee'),
                'kill_point' => $request->input('kill_point'),
                'match_duration' => $request->integer('match_duration'),
                'status' => 'active',
            ]);

            $this->syncPrizes($category, $request->validated('prizes'));
        });

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category): View
    {
        $category->load(['categoryPrizes' => fn ($query) => $query->orderBy('position')]);

        return view('admin.categories.edit', [
            'category' => $category,
            'prizes' => $category->categoryPrizes->isNotEmpty()
                ? $category->categoryPrizes
                    ->map(fn ($prize) => [
                        'position' => $prize->position,
                        'prize_amount' => (float) $prize->prize_amount,
                    ])
                    ->values()
                    ->all()
                : $this->defaultPrizes(),
        ]);
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        DB::transaction(function () use ($request, $category): void {
            $category->update([
                'name' => $request->string('name')->toString(),
                'squad_type' => $request->string('squad_type')->toString(),
                'max_players' => $this->resolveMaxPlayers($request->string('squad_type')->toString()),
                'map' => $request->string('map')->toString(),
                'rules' => $request->string('rules')->toString(),
                'entry_fee' => $request->input('entry_fee'),
                'kill_point' => $request->input('kill_point'),
                'match_duration' => $request->integer('match_duration'),
            ]);

            $this->syncPrizes($category, $request->validated('prizes'));
        });

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->rooms()->exists()) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', 'Category cannot be deleted because rooms already exist under it.');
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    public function toggleStatus(Category $category): RedirectResponse
    {
        $category->update([
            'status' => $category->status === 'active' ? 'inactive' : 'active',
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category status updated successfully.');
    }

    protected function syncPrizes(Category $category, array $prizes): void
    {
        $category->categoryPrizes()->delete();

        $category->categoryPrizes()->createMany(
            collect($prizes)
                ->sortBy('position')
                ->map(fn (array $prize) => [
                    'position' => (int) $prize['position'],
                    'prize_amount' => $prize['prize_amount'],
                ])
                ->values()
                ->all()
        );
    }

    protected function resolveMaxPlayers(string $squadType): int
    {
        return match ($squadType) {
            'solo' => 1,
            'duo' => 2,
            default => 4,
        };
    }

    protected function defaultPrizes(): array
    {
        return [
            ['position' => 1, 'prize_amount' => 0],
            ['position' => 2, 'prize_amount' => 0],
            ['position' => 3, 'prize_amount' => 0],
        ];
    }
}
