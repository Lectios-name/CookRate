@extends('layouts.app')

@section('title', 'Добавление рецепта')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/style4.css') }}">
@endpush

@section('content')
    <main>
        {{-- Указываем маршрут для сохранения (recipes.store) --}}
        <form action="{{ route('recipes.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

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
                        value="{{ old('name') }}"
                        class="form-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                        placeholder="Например: Борщ"
                    >
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="recipeDesc" class="form-label">Описание</label>
                    <textarea name="description" id="recipeDesc" class="form-input" rows="3" placeholder="Краткое описание блюда"></textarea>
                </div>

                <div class="form-group">
                    <label for="category_id" class="form-label">Категория<span class="required">*</span></label>
                    <select name="category_id" class="form-input {{ $errors->has('category_id') ? 'is-invalid' : '' }}">
                        <option value="">Выберите категорию</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="time_minutes" class="form-label">Время приготовления (в минутах)</label>
                    <input
                        type="number"
                        name="time_minutes"
                        value="{{ old('time_minutes') }}"
                        id="time_minutes"
                        class="form-input {{ $errors->has('time_minutes') ? 'is-invalid' : '' }}"
                        min="1"
                        max="1440"
                        placeholder="Например: 45"
                    >
                    @error('time_minutes')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="add-recipe-container">
                <div class="step-header">
                    <h1 class="step-title">Фото с рецептом</h1>
                    <span class="step-indicator">Шаг 2 из 4</span>
                </div>
                <div class="form-group">
                    <label class="form-label">Загрузите главное фото блюда</label>
                    <input
                        type="file"
                        name="preview_image"
                        class="form-input {{ $errors->has('preview_image') ? 'is-invalid' : '' }}"
                        accept="image/*"
                    >
                    @error('preview_image')
                    <div class="invalid-feedback">{{ $message }}</div>
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
                        $ingredients = old('ingredients', []);
                        // Если форма отправлялась впервые — добавим 2 пустых ингредиента
                        if (empty($ingredients)) {
                            $ingredients = [
                                ['amount' => 1, 'unit' => 'Штук', 'name' => ''],
                                ['amount' => 1, 'unit' => 'Штук', 'name' => ''],
                            ];
                        }
                    @endphp

                    @foreach($ingredients as $index => $ingredient)
                        <div class="ingredient-row">
                            <input
                                type="number"
                                name="ingredients[{{ $index }}][amount]"
                                class="qty-input {{ $errors->has("ingredients.{$index}.amount") ? 'is-invalid' : '' }}"
                                value="{{ $ingredient['amount'] ?? 1 }}"
                                min="0.01"
                                step="0.01"
                                placeholder="0.01"
                            >
                            <select name="ingredients[{{ $index }}][unit]" class="unit-select {{ $errors->has("ingredients.{$index}.unit") ? 'is-invalid' : '' }}">
                                @foreach(['Штук', 'Г', 'Кг', 'Мл', 'Л', 'Стакан', 'Ст. ложка', 'Ч. ложка', 'По вкусу'] as $unit)
                                    <option value="{{ $unit }}" {{ ($ingredient['unit'] ?? 'Штук') == $unit ? 'selected' : '' }}>{{ $unit }}</option>
                                @endforeach
                            </select>
                            <input
                                type="text"
                                name="ingredients[{{ $index }}][name]"
                                class="ingredient-name {{ $errors->has("ingredients.{$index}.name") ? 'is-invalid' : '' }}"
                                value="{{ $ingredient['name'] ?? '' }}"
                                placeholder="Например: мука"
                            >
                            <button type="button" class="btn-remove-row" onclick="this.closest('.ingredient-row').remove()">×</button>

                            <!-- Сообщения об ошибках -->
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
                    @php
                        $steps = old('steps', []);
                        if (empty($steps)) {
                            $steps = [
                                ['description' => '', 'photo' => null],
                                ['description' => '', 'photo' => null],
                            ];
                        }
                    @endphp

                    @foreach($steps as $index => $step)
                        <div class="step-item">
                            <div class="step-header-row">
                                <span>Шаг <span class="step-num">{{ $index + 1 }}</span></span>
                                <button type="button" class="btn-remove-row" style="margin-left: auto;" onclick="this.closest('.step-item').remove(); reindexSteps();">Удалить</button>
                            </div>
                            <textarea
                                name="steps[{{ $index }}][description]"
                                class="step-desc {{ $errors->has("steps.{$index}.description") ? 'is-invalid' : '' }}"
                                rows="3"
                                placeholder="Опишите действие..."
                            >{{ $step['description'] ?? '' }}</textarea>

                            <input
                                type="file"
                                name="steps[{{ $index }}][photo]"
                                accept="image/*"
                                class="{{ $errors->has("steps.{$index}.photo") ? 'is-invalid' : '' }}"
                            >

                            @error("steps.{$index}.description")
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @error("steps.{$index}.photo")
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endforeach
                </div>

                <button type="button" class="btn-add-step" id="add-step-btn" style="margin-top: 15px;">+ Добавить шаг</button>
            </div>

            <div class="save-button-wrapper">
                <button type="submit" class="btn-save-recipe">Сохранить рецепт</button>
            </div>
        </form>
    </main>

    {{-- Скрытый шаблон для JS (чтобы копировать HTML структуру) --}}
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

            addIngBtn.addEventListener('click', function() {
                const clone = ingTemplate.content.cloneNode(true);
                const row = clone.querySelector('.ingredient-row');

                // Считаем, сколько уже строк (без кнопки)
                const currentRows = ingContainer.querySelectorAll('.ingredient-row').length;
                row.querySelector('.qty-input').name = `ingredients[${currentRows}][amount]`;
                row.querySelector('.unit-select').name = `ingredients[${currentRows}][unit]`;
                row.querySelector('.ingredient-name').name = `ingredients[${currentRows}][name]`;

                ingContainer.insertBefore(row, addIngBtn);
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
