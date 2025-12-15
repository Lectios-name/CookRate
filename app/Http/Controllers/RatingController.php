<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Comment;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // Добавлено для отладки ошибок

class RatingController extends Controller
{
    // Метод для отдельной оценки (rate)
    public function rate(Request $request, $recipeId)
    {
        $request->validate(['value' => 'required|integer|min:1|max:5']);

        Rating::updateOrCreate(
            ['user_id' => auth()->id(), 'recipe_id' => $recipeId],
            ['value' => $request->value]
        );

        $recipe = Recipe::findOrFail($recipeId);
        // Обновляем модель для получения актуальных аксессоров (среднего рейтинга и счетчика)
        $recipe->refresh();

        return response()->json([
            // Форматируем до одной цифры после запятой
            'average_rating' => (float) number_format($recipe->average_rating, 1),
            'ratings_count' => $recipe->ratings_count,
        ]);
    }

    // Метод для комментария (comment)
    public function comment(Request $request, $recipeId)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Вы должны быть авторизованы'], 401);
        }
        try {
            $request->validate([
                'rating_value' => 'nullable|integer|min:1|max:5', // Оценка опциональна, но лучше обязательна
                'text' => 'nullable|string',
                'photo' => 'nullable|image|max:2048',
            ]);

            // Если нет ни текста, ни фото, ни оценки - ошибка
            if (empty($request->text) && !$request->hasFile('photo') && !isset($request->rating_value)) {
                return response()->json(['error' => 'Добавьте комментарий, фото или оценку'], 422);
            }

            $photoPath = null;
            if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                $photoPath = $request->file('photo')->store('comments', 'public');
            }

            // === 1. ОБНОВЛЕНИЕ/СОЗДАНИЕ ОЦЕНКИ (ГЛАВНОЕ ИСПРАВЛЕНИЕ) ===
            // Обновляем запись в таблице ratings, если пользователь уже оценивал.
            if ($request->filled('rating_value')) {
                Rating::updateOrCreate(
                    ['user_id' => auth()->id(), 'recipe_id' => $recipeId],
                    ['value' => $request->rating_value]
                );
            }

            // === 2. СОЗДАНИЕ КОММЕНТАРИЯ ===
            $comment = Comment::create([
                'user_id' => auth()->id(),
                'recipe_id' => $recipeId,
                'text' => $request->text,
                'rating_value' => $request->rating_value,
                'photo_path' => $photoPath,
            ]);

            $recipe = Recipe::findOrFail($recipeId);
            $recipe->refresh(); // Обновляем модель для получения актуальных аксессоров

            // Сбор данных для рендеринга
            $commentData = [
                'user' => auth()->user(),
                'text' => $comment->text,
                'rating_value' => $comment->rating_value,
                'photo_path' => $comment->photo_path,
                'created_at' => $comment->created_at->format('d.m.Y')
            ];

            return response()->json([
                'average_rating' => (float) number_format($recipe->average_rating, 1), // Форматируем
                'ratings_count' => $recipe->ratings_count, // Передаем актуальный счетчик
                'comment_html' => $this->renderComment($commentData)
            ]);
        } catch (\Exception $e) {
            Log::error('Ошибка при добавлении комментария:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json(['error' => 'Не удалось добавить отзыв. Попробуйте позже.'], 500);
        }
    }

    private function renderComment($data)
    {
        // Используем '★' вместо '⭐' для единообразия в шаблоне
        $userName = $data['user']->name ?? 'Пользователь';
        $userSurname = $data['user']->surname ?? '';

        $stars = str_repeat('★', $data['rating_value'] ?? 0);
        $photoHtml = '';
        if ($data['photo_path']) {
            $photoHtml = '<div class="photo-results">
            <img src="' . asset('storage/' . $data['photo_path']) . '" alt="Фото результата" style="max-width:200px; height:auto; margin-top:10px; border-radius:4px;">
        </div>';
        }

        $textHtml = $data['text'] ? '<p class="comment-text">' . e($data['text']) . '</p>' : '';

        return '
    <div class="comment-card">
        <div class="comment-header">
            <div class="avatar">Ава</div>
            <span class="author-name">' . e($userName . ' ' . $userSurname) . '</span>
            <div class="rating-stars">' . $stars . '</div>
            <span class="comment-date">' . $data['created_at'] . '</span>
        </div>
        ' . $textHtml . '
        ' . $photoHtml . '
    </div>';
    }
}
