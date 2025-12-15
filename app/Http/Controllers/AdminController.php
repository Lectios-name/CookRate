<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use App\Models\Recipe;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        $recipes = Recipe::with(['user', 'ratings'])->paginate(12);
        $categories = Category::all(); // ← добавили

        return view('admin.index', compact('users', 'recipes', 'categories'));
    }

    // Поиск пользователей
    public function searchUsers(Request $request)
    {
        $query = $request->input('q');
        $users = User::where(function($q) use ($query) {
            $q->where('name', 'LIKE', "%$query%")
                ->orWhere('login', 'LIKE', "%$query%");
        })->paginate(10);
        $users->appends(['q_users' => $query]);
        return view('admin.partials.users', compact('users'))->render();
    }

    // Поиск рецептов
    public function searchRecipes(Request $request)
    {
        $query = $request->input('q');
        $recipes = Recipe::with('user')
            ->where('name', 'LIKE', "%$query%")
            ->paginate(12);
        $recipes->appends(['q_recipes' => $query]);
        return view('admin.partials.recipes', compact('recipes'))->render();
    }

    // Бан пользователя
    public function banUser(User $user)
    {
        $user->update(['is_banned' => true]);
        return back()->with('success', 'Пользователь забанен');
    }

    // Разбан пользователя
    public function unbanUser(User $user)
    {
        $user->update(['is_banned' => false]);
        return back()->with('success', 'Пользователь разбанен');
    }

    // Удаление рецепта
    public function destroyRecipe(Recipe $recipe)
    {
        // Удалить фото
        if ($recipe->image_path && file_exists(public_path($recipe->image_path))) {
            unlink(public_path($recipe->image_path));
        }
        foreach ($recipe->steps as $step) {
            if ($step->photo_path && file_exists(public_path($step->photo_path))) {
                unlink(public_path($step->photo_path));
            }
        }
        $recipe->delete();
        return back()->with('success', 'Рецепт удалён');
    }

    public function createCategory()
    {
        return view('admin.categories.create');
    }

// Сохранить новую категорию
    public function storeCategory(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:categories']);
        Category::create($request->only('name'));
        return redirect()->route('admin.index')->with('success', 'Категория добавлена');
    }

// Показать форму редактирования
    public function editCategory(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

// Обновить категорию
    public function updateCategory(Request $request, Category $category)
    {
        $request->validate(['name' => 'required|string|max:255|unique:categories,name,' . $category->id]);
        $category->update($request->only('name'));
        return redirect()->route('admin.index')->with('success', 'Категория обновлена');
    }

// Удалить категорию
    public function destroyCategory(Category $category)
    {
        $category->delete();
        return back()->with('success', 'Категория удалена');
    }
}
