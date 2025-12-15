@extends('layouts.app')

@section('title', 'Редактирование рецепта')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/style4.css') }}">
@endpush

@section('content')
    <main>
        <form action="{{ route('recipes.update', $recipe) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="add-recipe-container">
                <div class="step-header">
                    <h1 class="step-title">Информация о рецепте</h1>
                    <span class="step-indicator">Шаг 1 из 4</span>
                </div>

                <div class="form-group">
                    <label for="recipeName" class="form-label">Название рецепта<span class="required">*</span></label>
                    <input
                        type="text"
                        name="name"
                        id="recipeName"
                        class="form-input @error('name') is-invalid @enderror"
                        value="{{ old('name', $recipe->name) }}"
                        placeholder="Например: Борщ"
                    >
                    @error('name')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="recipeDesc" class="form-label">Описание</label>
                    <textarea
                        name="description"
                        id="recipeDesc"
                        class="form-input"
                        rows="3"
                        placeholder="Краткое описание блюда"
                    >{{ old('description', $recipe->description) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="time_minutes" class="form-label">Время приготовления (в минутах)</label>
                    <input
                        type="number"
                        name="time_minutes"
                        id="time_minutes"
                        class="form-input @error('time_minutes') is-invalid @enderror"
                        placeholder="Например: 45"
                        min="1"
                        max="1440"
                        value="{{ old('time_minutes', $recipe->time_minutes) }}"
                    >
                    @error('time_minutes')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="category_id" class="form-label">Категория<span class="required">*</span></label>
                    <select name="category_id" id="category_id" class="form-input">
                        <option value="">Выберите категорию</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ (old('category_id', $recipe->category_id) == $category->id) ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="add-recipe-container">
                <div class="step-header">
                    <h1 class="step-title">Фото с рецептом</h1>
                    <span class="step-indicator">Шаг 2 из 4</span>
                </div>
                <div class="form-group">
                    <label class="form-label">Текущее фото:</label>
                    @if($recipe->image_path)
                        <div class="current-image-preview">
                            <img src="{{ asset($recipe->image_path) }}" alt="Текущее фото" style="max-width:200px; height:auto;">
                        </div>
                    @else
                        <p>Фото отсутствует</p>
                    @endif
                </div>
                <div class="form-group">
                    <label class="form-label">Загрузите новое фото (оставьте пустым, чтобы оставить текущее)</label>
                    <input type="file" name="preview_image" class="form-input @error('preview_image') is-invalid @enderror" accept="image/*">
                    @error('preview_image')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="add-recipe-container">
                <div class="step-header">
                    <h1 class="step-title">Ингредиенты</h1>
                    <span class="step-indicator">Шаг 3 из 4</span>
                </div>
                <p class="step-subtitle">Перечислите все ингредиенты, необходимые для этого рецепта</p>

                <div class="ingredients-list" id="ingredients-container">
                    @php
                        $unitsList = ['Штук', 'Г', 'Кг', 'Мл', 'Л', 'Стакан', 'Ст. ложка', 'Ч. ложка', 'По вкусу'];
                    @endphp

                    @foreach(old('ingredients', $recipe->ingredients->toArray()) as $index => $ingredient)
                        <div class="ingredient-row">
                            <input
                                type="number"
                                name="ingredients[{{ $index }}][amount]"
                                class="qty-input {{ $errors->has("ingredients.{$index}.amount") ? 'is-invalid' : '' }}"
                                value="{{ old("ingredients.{$index}.amount", $ingredient['amount'] ?? 1) }}"
                                min="0.01"
                                step="0.01"
                            >
                            <select
                                name="ingredients[{{ $index }}][unit]"
                                class="unit-select {{ $errors->has("ingredients.{$index}.unit") ? 'is-invalid' : '' }}"
                            >
                                @foreach($unitsList as $unit)
                                    <option
                                        value="{{ $unit }}"
                                        {{ old("ingredients.{$index}.unit", $ingredient['unit'] ?? 'Штук') == $unit ? 'selected' : '' }}
                                    >
                                        {{ $unit }}
                                    </option>
                                @endforeach
                            </select>
                            <input
                                type="text"
                                name="ingredients[{{ $index }}][name]"
                                class="ingredient-name {{ $errors->has("ingredients.{$index}.name") ? 'is-invalid' : '' }}"
                                value="{{ old("ingredients.{$index}.name", $ingredient['name'] ?? '') }}"
                                placeholder="Например: мука"
                            >
                            <button type="button" class="btn-remove-row" onclick="this.closest('.ingredient-row').remove()">×</button>
                            @error("ingredients.{$index}.amount")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @error("ingredients.{$index}.unit")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @error("ingredients.{$index}.name")
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endforeach

                    <button type="button" class="btn-add-ingredient" id="add-ingredient-btn">+ Добавить ингредиент</button>
                </div>
            </div>

            <div class="add-recipe-container">
                <div class="step-header">
                    <h1 class="step-title">Инструкция приготовления</h1>
                    <span class="step-indicator">Шаг 4 из 4</span>
                </div>

                <div class="steps-list" id="steps-container">
                    @foreach(old('steps', $recipe->steps->toArray()) as $index => $step)
                        <div class="step-item">
                            <div class="step-header-row">
                                <span>Шаг <span class="step-num">{{ $index + 1 }}</span></span>
                                <button type="button" class="btn-remove-row" style="margin-left: auto;" onclick="this.closest('.step-item').remove(); reindexSteps();">Удалить</button>
                            </div>
                            <textarea name="steps[{{ $index }}][description]" class="step-desc" rows="3" placeholder="Опишите действие...">{{ $step['description'] ?? '' }}</textarea>
                            @if(!empty($step['photo_path']))
                                <div class="current-step-photo">
                                    <img src="{{ asset('storage/' . $step['photo_path']) }}" alt="Фото шага" style="max-width:150px; height:auto;">
                                </div>
                            @endif
                            <input type="file" name="steps[{{ $index }}][photo]" accept="image/*">
                        </div>
                    @endforeach
                </div>

                <button type="button" class="btn-add-step" id="add-step-btn" style="margin-top: 15px;">+ Добавить шаг</button>
            </div>

            <div class="save-button-wrapper">
                <button type="submit" class="btn-save-recipe">Сохранить изменения</button>
                <a href="{{ route('recipes.show', $recipe) }}" class="btn-cancel">Отмена</a>
            </div>
        </form>
    </main>

    {{-- Скрытые шаблоны для JS --}}
    <template id="ingredient-template">
        <div class="ingredient-row">
            <input type="number" class="qty-input" value="1" min="1" step="0.1">
            <select class="unit-select">
                <option>Штук</option>
                <option>Г</option>
                <option>Кг</option>
                <option>Мл</option>
                <option>Л</option>
                <option>Стакан</option>
                <option>Ст. ложка</option>
                <option>Ч. ложка</option>
                <option>По вкусу</option>
            </select>
            <input type="text" class="ingredient-name" placeholder="Ингредиент">
            <button type="button" class="btn-remove-row" onclick="this.parentElement.remove()">×</button>
        </div>
    </template>

    <template id="step-template">
        <div class="step-item">
            <div class="step-header-row">
                <span>Шаг <span class="step-num"></span></span>
                <button type="button" class="btn-remove-row" style="margin-left: auto;">Удалить</button>
            </div>
            <textarea class="step-desc" rows="3" placeholder="Опишите действие..."></textarea>
            <input type="file" accept="image/*">
        </div>
    </template>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // === ИНГРЕДИЕНТЫ ===
            const ingContainer = document.getElementById('ingredients-container');
            const addIngBtn = document.getElementById('add-ingredient-btn');
            const ingTemplate = document.getElementById('ingredient-template');
            let ingIndex = {{ count(old('ingredients', $recipe->ingredients)) }};

            addIngBtn.addEventListener('click', function() {
                const clone = ingTemplate.content.cloneNode(true);
                const row = clone.querySelector('.ingredient-row');
                row.querySelector('.qty-input').name = `ingredients[${ingIndex}][amount]`;
                row.querySelector('.unit-select').name = `ingredients[${ingIndex}][unit]`;
                row.querySelector('.ingredient-name').name = `ingredients[${ingIndex}][name]`;
                ingContainer.insertBefore(row, addIngBtn);
                ingIndex++;
            });

            // === ШАГИ ===
            const stepContainer = document.getElementById('steps-container');
            const addStepBtn = document.getElementById('add-step-btn');
            const stepTemplate = document.getElementById('step-template');

            function reindexSteps() {
                const steps = stepContainer.querySelectorAll('.step-item');
                steps.forEach((step, index) => {
                    step.querySelector('.step-num').textContent = index + 1;
                    const desc = step.querySelector('textarea');
                    const file = step.querySelector('input[type="file"]');
                    desc.name = `steps[${index}][description]`;
                    file.name = `steps[${index}][photo]`;
                });
            }

            addStepBtn.addEventListener('click', function() {
                const clone = stepTemplate.content.cloneNode(true);
                const item = clone.querySelector('.step-item');
                const removeBtn = item.querySelector('.btn-remove-row');
                removeBtn.addEventListener('click', function() {
                    item.remove();
                    reindexSteps();
                });
                stepContainer.appendChild(item);
                reindexSteps();
            });

            // Изначальная индексация шагов
            reindexSteps();
        });
    </script>
@endpush
