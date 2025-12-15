<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecipeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'preview_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'time_minutes' => 'nullable|integer|min:1|max:1440',
            // Валидация для ингредиентов
            'ingredients' => 'required|array|min:1',
            'ingredients.*.amount' => 'required|numeric|min:0.01',
            'ingredients.*.unit' => 'required|string|max:50',
            'ingredients.*.name' => 'required|string|max:255',

            // Валидация для шагов
            'steps' => 'required|array|min:1',
            'steps.*.description' => 'required|string',
            'steps.*.photo' => 'nullable|image|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Название рецепта обязательно.',
            'category_id.required' => 'Выберите категорию рецепта.',
            'preview_image.required' => 'Главное фото рецепта обязательно.',

            'time_minutes.integer' => 'Время должно быть числом (в минутах).',
            'time_minutes.min' => 'Время должно быть не менее 1 минуты.',
            'time_minutes.max' => 'Время не может превышать 1440 минут (24 часа).',

            'ingredients.required' => 'Добавьте хотя бы один ингредиент.',
            'ingredients.*.amount.required' => 'Укажите количество ингредиента.',
            'ingredients.*.amount.numeric' => 'Количество должно быть числом.',
            'ingredients.*.unit.required' => 'Выберите единицу измерения.',
            'ingredients.*.name.required' => 'Название ингредиента обязательно.',

            'steps.required' => 'Добавьте хотя бы один шаг приготовления.',
            'steps.*.description.required' => 'Описание шага обязательно.',
            'steps.*.photo.image' => 'Фото шага должно быть изображением.',
        ];
    }
    public function prepareForValidation()
    {
        // Если это редактирование (PUT/PATCH) — удаляем требование фото
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            // Если фото не загружено — удаляем из данных, чтобы валидация не проверяла
            if (!$this->hasFile('preview_image')) {
                $this->merge([
                    'preview_image' => null,
                ]);
            }
        }
    }
}
