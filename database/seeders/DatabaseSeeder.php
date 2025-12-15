<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::create([
            'name' => 'admin',
            'login' => 'admin',
            'surname' => 'admin',
            'email' => 'admin@mail.ru',
            'bio' => '',
            'avatar_path' => null,
            'password' => Hash::make('admin11'),
            'isAdmin' => 1,
        ]);

        $user = User::create([
            'name' => 'Владимир',
            'login' => 'Lectio',
            'surname' => 'Пономарев',
            'email' => 'vla@mail.ru',
            'bio' => '',
            'avatar_path' => null,
            'password' => Hash::make('123456'),
            'isAdmin' => 0,
        ]);

        // === Категории ===
        $breakfast = Category::create(['name' => 'Завтраки']);
        $lunch = Category::create(['name' => 'Обеды']);
        $dinner = Category::create(['name' => 'Ужины']);
        $dessert = Category::create(['name' => 'Десерты']);
        $drinks = Category::create(['name' => 'Напитки']);

        // === Пример рецепта ===
        $recipe = Recipe::create([
            'user_id' => $user->id,
            'category_id' => $breakfast->id,
            'name' => 'Омлет классический',
            'description' => 'Простой и вкусный омлет на завтрак.',
            'image_path' => 'uploads/recipes/omelette.jpg', // ← нужно положить файл в public/uploads/recipes/
            'time_minutes' => 15,
        ]);

        // Ингредиенты
        $recipe->ingredients()->createMany([
            ['name' => 'Яйца', 'quantity' => '3 шт'],
            ['name' => 'Молоко', 'quantity' => '50 мл'],
            ['name' => 'Соль', 'quantity' => 'по вкусу'],
            ['name' => 'Масло сливочное', 'quantity' => '10 г'],
        ]);

        // Шаги
        $recipe->steps()->createMany([
            [
                'step_number' => 1,
                'description' => 'Разбейте яйца в миску, добавьте молоко и соль. Взбейте вилкой до однородности.',
                'photo_path' => null, // или 'steps/step1_omelette.jpg'
            ],
            [
                'step_number' => 2,
                'description' => 'Разогрейте сковороду, растопите сливочное масло.',
                'photo_path' => null,
            ],
            [
                'step_number' => 3,
                'description' => 'Вылейте яичную смесь на сковороду и готовьте на среднем огне 3–4 минуты до загустения.',
                'photo_path' => null,
            ],
        ]);

        // === Ещё один рецепт ===
        $recipe2 = Recipe::create([
            'user_id' => $admin->id,
            'category_id' => $dessert->id,
            'name' => 'Шоколадный кекс',
            'description' => 'Быстрый и сочный кекс для сладкоежек.',
            'image_path' => 'uploads/recipes/chocolate_cake.jpg',
            'time_minutes' => 40,
        ]);

        $recipe2->ingredients()->createMany([
            ['name' => 'Мука', 'quantity' => '200 г'],
            ['name' => 'Какао-порошок', 'quantity' => '50 г'],
            ['name' => 'Сахар', 'quantity' => '150 г'],
            ['name' => 'Яйца', 'quantity' => '2 шт'],
            ['name' => 'Масло растительное', 'quantity' => '100 мл'],
            ['name' => 'Разрыхлитель', 'quantity' => '1 ч. ложка'],
        ]);

        $recipe2->steps()->createMany([
            [
                'step_number' => 1,
                'description' => 'Смешайте сухие ингредиенты: муку, какао, сахар и разрыхлитель.',
                'photo_path' => null,
            ],
            [
                'step_number' => 2,
                'description' => 'Добавьте яйца и растительное масло. Хорошо перемешайте до однородной массы.',
                'photo_path' => null,
            ],
            [
                'step_number' => 3,
                'description' => 'Вылейте тесто в форму и выпекайте при 180°C 30–35 минут.',
                'photo_path' => null,
            ],
        ]);
    }

}
