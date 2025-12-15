<?php
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ShoppingListController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Главная страница
Route::get('/', [RecipeController::class, 'index'])->name('main');

// Авторизация и регистрация
Route::view('/registration', 'users.registration')->name('register');
Route::post('/registration', [UserController::class, 'register_post']);

Route::view('/authorization', 'users.authorization')->name('auth');
Route::post('/authorization', [UserController::class, 'auth_post']);

// Выход
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

// Поиск рецептов
Route::get('/search', [RecipeController::class, 'search'])->name('search');

// Личный кабинет и избранное
Route::middleware('auth')->group(function () {
    Route::get('/personal', [UserController::class, 'personal'])->name('personal');
    Route::put('/personal', [UserController::class, 'updatePersonal'])->name('personal.update');
    Route::delete('/avatar/remove', [UserController::class, 'removeAvatar'])->name('avatar.remove');
    // Избранное
    Route::post('/favorites/{recipe}', [FavoriteController::class, 'add'])->name('favorites.add');
    Route::delete('/favorites/{recipe}', [FavoriteController::class, 'remove'])->name('favorites.remove');

    // Рецепты
    Route::get('/recipes/create', [RecipeController::class, 'create'])->name('recipes.create');
    Route::post('/recipes', [RecipeController::class, 'store'])->name('recipes.store');
    Route::get('/recipes/{recipe}', [RecipeController::class, 'show'])->name('recipes.show');
    Route::get('/recipes/{recipe}/edit', [RecipeController::class, 'edit'])->name('recipes.edit');
    Route::put('/recipes/{recipe}', [RecipeController::class, 'update'])->name('recipes.update');
    Route::delete('/recipes/{recipe}', [RecipeController::class, 'destroy'])->name('recipes.destroy');

    // Списки покупок
    Route::get('/shopping-lists', [ShoppingListController::class, 'index'])->name('shopping-lists.index');
    Route::post('/shopping-lists', [ShoppingListController::class, 'store'])->name('shopping-lists.store');
    Route::post('/shopping-lists/add-items', [ShoppingListController::class, 'addItems'])->name('shopping-lists.add-items');
    Route::delete('/shopping-lists/{shoppingList}', [ShoppingListController::class, 'destroy'])->name('shopping-lists.destroy');
    Route::patch('/shopping-list-items/{item}', [ShoppingListController::class, 'toggleItem'])->name('shopping-list-items.toggle');

    // Оценка и комментарии
    Route::post('/recipes/{recipe}/rate', [RatingController::class, 'rate'])->name('recipes.rate');
    Route::post('/recipes/{recipe}/comment', [RatingController::class, 'comment'])->name('recipes.comment');
});

// === Панель администратора ===
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/search-users', [AdminController::class, 'searchUsers'])->name('admin.search-users');
    Route::get('/admin/search-recipes', [AdminController::class, 'searchRecipes'])->name('admin.search-recipes');
    Route::post('/admin/users/{user}/ban', [AdminController::class, 'banUser'])->name('admin.users.ban');
    Route::post('/admin/users/{user}/unban', [AdminController::class, 'unbanUser'])->name('admin.users.unban');
    Route::delete('/admin/recipes/{recipe}', [AdminController::class, 'destroyRecipe'])->name('admin.recipes.destroy');
    Route::get('/admin/categories/create', [AdminController::class, 'createCategory'])->name('admin.categories.create');
    Route::post('/admin/categories', [AdminController::class, 'storeCategory'])->name('admin.categories.store');
    Route::get('/admin/categories/{category}/edit', [AdminController::class, 'editCategory'])->name('admin.categories.edit');
    Route::put('/admin/categories/{category}', [AdminController::class, 'updateCategory'])->name('admin.categories.update');
    Route::delete('/admin/categories/{category}', [AdminController::class, 'destroyCategory'])->name('admin.categories.destroy');
});



