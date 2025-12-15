<?php
namespace App\Http\Controllers;

use App\Http\Requests\RecipeRequest;
use App\Models\Category;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RecipeController extends Controller
{
    public function index()
    {
        $topRecipes = Recipe::with(['user' => function($q) {
            $q->select('id', 'name', 'surname', 'avatar_path');
        }])->withAvg('ratings', 'value')->orderBy('ratings_avg_value', 'desc')->take(6)->get();

        $categories = Category::all();
        return view('welcome', compact('topRecipes', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('recipes.create', compact('categories'));
    }

    // Сохранить рецепт
    public function store(RecipeRequest $request)
    {
        $validatedData = $request->validated();

        // === 1. Сохраняем главное фото в public/recipes ===
        $previewImage = $request->file('preview_image');
        $imageFilename = time() . '_' . $previewImage->getClientOriginalName();
        $pathName = 'uploads/recipes'; // Новая папка
        $previewImage->move(public_path($pathName), $imageFilename);
        $imagePath = $pathName . '/' . $imageFilename;

        // === 2. Создаём рецепт ===
        $recipe = Recipe::create([
            'user_id' => Auth::id(),
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description ?? '',
            'image_path' => $imagePath,
            'time_minutes' => $request->integer('time_minutes'),
        ]);

        // === 3. Сохраняем ингредиенты ===
        foreach ($request->ingredients as $ing) {
            if (!empty($ing['name'])) {
                $fullQuantity = $ing['amount'] . ' ' . $ing['unit'];
                $recipe->ingredients()->create([
                    'name' => $ing['name'],
                    'quantity' => $fullQuantity,
                ]);
            }
        }

        // === 4. Сохраняем шаги с фото ===
        foreach ($request->steps as $index => $step) {
            $stepPhotoPath = null;

            if (isset($step['photo']) && $step['photo']->isValid()) {
                $photoFilename = time() . '_' . ($index + 1) . '_' . $step['photo']->getClientOriginalName();
                $step['photo']->move(public_path('steps'), $photoFilename);
                $stepPhotoPath = 'steps/' . $photoFilename;
            }

            $recipe->steps()->create([
                'step_number' => $index + 1,
                'description' => $step['description'],
                'photo_path' => $stepPhotoPath,
            ]);
        }

        return redirect()->route('main')->with('success', 'Рецепт успешно добавлен!');
    }


    public function show(Recipe $recipe)
    {
        $recipe->load([
            'user' => function($q) {
                $q->select('id', 'name', 'surname', 'avatar_path');
            },
            'ingredients',
            'steps',
            'comments.user:id,name,surname,avatar_path',
            'favoritedBy'
        ]);
        return view('recipes.show', compact('recipe'));
    }

    public function search(Request $request)
    {
        $query = Recipe::with(['user' => function($q) {
            $q->select('id', 'name', 'surname', 'avatar_path');
        }, 'category']);

        // Поиск по названию
        if ($request->filled('q')) {
            $query->where('name', 'LIKE', '%' . $request->q . '%');
        }

        // Фильтр по категории
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Сортировка
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'rating':
                // Пока не реализовано — пропустим
                break;
            case 'time':
                $query->orderBy('time_minutes', 'asc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'latest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $recipes = $query->paginate(12);
        $categories = Category::all();
        return view('recipes.search', compact('recipes', 'categories'));
    }
    public function edit(Recipe $recipe)
    {
        $this->authorizeUser($recipe); // Проверка владельца
        $categories = \App\Models\Category::all();
        return view('recipes.edit', compact('recipe', 'categories'));
    }

    public function update(RecipeRequest $request, Recipe $recipe)
    {
        $this->authorizeUser($recipe);

        $validatedData = $request->validated();

        // Обновление главного фото
        if ($request->hasFile('preview_image')) {
            // Удалить старое фото (если оно в public/uploads/...)
            if ($recipe->image_path && file_exists(public_path($recipe->image_path))) {
                unlink(public_path($recipe->image_path));
            }

            $previewImage = $request->file('preview_image');
            $imageFilename = time() . '_' . $previewImage->getClientOriginalName();
            $pathName = 'uploads/recipes';
            $previewImage->move(public_path($pathName), $imageFilename);
            $imagePath = $pathName . '/' . $imageFilename;
        } else {
            $imagePath = $recipe->image_path; // оставить старое
        }

        $recipe->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description ?? '',
            'image_path' => $imagePath,
            'time_minutes' => $request->integer('time_minutes'),
        ]);

        // Ингредиенты: удалим старые и создадим новые
        $recipe->ingredients()->delete();
        foreach ($request->ingredients as $ing) {
            if (!empty($ing['name'])) {
                $fullQuantity = $ing['amount'] . ' ' . $ing['unit'];
                $recipe->ingredients()->create([
                    'name' => $ing['name'],
                    'quantity' => $fullQuantity,
                ]);
            }
        }

        // Шаги: удалим старые и создадим новые
        $recipe->steps()->delete();
        foreach ($request->steps as $index => $step) {
            $stepPhotoPath = null;

            if (isset($step['photo']) && $step['photo']->isValid()) {
                $photoFilename = time() . '_' . ($index + 1) . '_' . $step['photo']->getClientOriginalName();
                $step['photo']->move(public_path('steps'), $photoFilename);
                $stepPhotoPath = 'steps/' . $photoFilename;
            }

            $recipe->steps()->create([
                'step_number' => $index + 1,
                'description' => $step['description'],
                'photo_path' => $stepPhotoPath,
            ]);
        }

        return redirect()->route('recipes.show', $recipe)->with('success', 'Рецепт обновлён!');
    }

    public function destroy(Recipe $recipe)
    {
        $this->authorizeUser($recipe);

        // Удалить главное фото
        if ($recipe->image_path && file_exists(public_path($recipe->image_path))) {
            unlink(public_path($recipe->image_path));
        }

        // Удалить фото шагов
        foreach ($recipe->steps as $step) {
            if ($step->photo_path && file_exists(public_path($step->photo_path))) {
                unlink(public_path($step->photo_path));
            }
        }

        $recipe->delete();
        return redirect()->route('personal')->with('success', 'Рецепт удалён!');
    }

    private function authorizeUser($recipe)
    {
        if ($recipe->user_id !== auth()->id() && !auth()->user()->isAdmin) {
            abort(403);
        }
    }
}
