@extends('layouts.app')
@section('title', 'Личный кабинет')

@section('content')
    <div class="container main-layout">
        <aside class="sidebar">
            <div class="user-info-sidebar">
                <div class="author_avatar avatar-circle avatar-large">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="Avatar">
                    @else
                        Ава
                    @endif
                </div>
                <h3>{{ $user->name }} {{ $user->surname }}</h3>
                <p style="color:#666;">Начинающий шеф</p>
            </div>
            <nav class="menu">
                <ul>
                    <li>
                        <a href="#profile">
                            <img src="{{ asset('images/icon_per.png') }}" alt="">
                            <span>Профиль</span>
                        </a>
                    </li>
                    <li>
                        <a href="#recipes">
                            <img src="{{ asset('images/recipe.png') }}" alt="">
                            <span>Мои рецепты</span>
                        </a>
                    </li>
                    <li>
                        <a href="#favorites">
                            <img src="{{ asset('images/fav.png') }}" alt="">
                            <span>Избранное</span>
                        </a>
                    </li>
                    <li>
                        <a href="#purchases">
                            <img src="{{ asset('images/sale.png') }}" alt="">
                            <span>Список покупок</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="personal-content">
            <section id="profile" class="section-block">
                <h2>Профиль</h2>
                <div class="profile-flex">
                    <div class="profile-form">
                        <form action="{{ route('personal.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf @method('PUT')
                            <div class="form-group">
                                <label>Имя</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label>Фамилия</label>
                                <input type="text" name="surname" value="{{ old('surname', $user->surname) }}" class="form-input">
                            </div>
                            <div class="form-group">
                                <label>Почта</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-input" required />
                            </div>
                            <div class="form-group">
                                <label>О себе</label>
                                <textarea name="bio" class="form-input">{{ old('bio', $user->bio) }}</textarea>
                            </div>
                            <input type="file" name="avatar" accept="image/*" id="avatar-input" style="display:none;">
                            <button type="submit" class="save-btn">Сохранить</button>
                        </form>
                    </div>
                    <div class="profile-avatar-edit">
                        <p>Фото профиля</p>
                        <div class="author_avatar avatar-circle avatar-large">
                            @if($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" alt="Avatar">
                            @else
                                Ава
                            @endif
                        </div>
                        <button type="button" class="btn" onclick="document.getElementById('avatar-input').click()"><img src="{{ asset('images/add.png') }}" alt="" style="width:16px; margin-right:5px;"> Загрузить новое</button>
                        @if($user->avatar_path)
                            <form action="{{ route('avatar.remove') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn">Удалить фото</button>
                            </form>
                        @endif
                    </div>
                </div>
            </section>

            <section id="recipes" class="section-block">
                <div style="display:flex; justify-content:space-between; margin-bottom:20px;">
                    <h2>Мои рецепты</h2>
                    <a href="{{ route('recipes.create') }}" class="btn" style="height:35px; font-size:14px;">+ Добавить</a>
                </div>

                <div class="recipes-grid">
                    @forelse($recipes as $recipe)
                        <div class="recipe-card">
                            <div class="card_header">
                                <div class="author">
                                    <div class="author_avatar">
                                        @if($user->avatar_url)
                                            <img src="{{ $user->avatar_url }}" alt="Av">
                                        @else
                                            Ава
                                        @endif
                                    </div>
                                    <span>Вы</span>
                                </div>
                            </div>
                            <a href="{{ route('recipes.show', $recipe) }}" class="card-link">
                                <div class="card_image"><img src="{{ asset($recipe->image_path) }}"></div>
                                <div class="card_body">
                                    <h3 class="card_title">{{ $recipe->name }}</h3>
                                    <p class="card_desc">{{ Str::limit($recipe->description, 60) }}</p>
                                    <div class="card_footer">
                                        <div class="meta-item"><img src="{{ asset('images/star.png') }}"> {{ number_format($recipe->average_rating, 1) }}</div>
                                        <div class="meta-item"><img src="{{ asset('images/time.png') }}"> {{ $recipe->formatted_time }}</div>
                                    </div>
                                </div>
                            </a>
                            <div class="card_actions">
                                <a href="{{ route('recipes.edit', $recipe) }}" class="btn-card-action btn-edit">Изменить</a>
                                <form action="{{ route('recipes.destroy', $recipe) }}" method="POST" style="flex:1;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-card-action btn-delete" onclick="return confirm('Удалить?')">Удалить</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p>Нет рецептов.</p>
                    @endforelse
                </div>
                <div style="margin-top:20px;">{{ $recipes->links() }}</div>
            </section>

            <section id="favorites" class="section-block">
                <h2>Избранное</h2>
                <div class="recipes-grid">
                    @forelse($favorites as $recipe)
                        <div class="recipe-card">
                            <div class="card_header">
                                <div class="author">
                                    <div class="author_avatar">
                                        @if($recipe->user->avatar_url)
                                            <img src="{{ $recipe->user->avatar_url }}" alt="Av">
                                        @else
                                            Ава
                                        @endif
                                    </div>
                                    <span>{{ $recipe->user->name }}</span>
                                </div>
                                <form action="{{ route('favorites.remove', $recipe) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="favorite-btn active"><img src="{{ asset('images/heart-filled.png') }}"> Убрать</button>
                                </form>
                            </div>
                            <a href="{{ route('recipes.show', $recipe) }}" class="card-link">
                                <div class="card_image"><img src="{{ asset($recipe->image_path) }}"></div>
                                <div class="card_body">
                                    <h3 class="card_title">{{ $recipe->name }}</h3>
                                    <div class="card_footer">
                                        <div class="meta-item"><img src="{{ asset('images/star.png') }}"> {{ number_format($recipe->average_rating, 1) }}</div>
                                        <div class="meta-item"><img src="{{ asset('images/time.png') }}"> {{ $recipe->formatted_time }}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @empty
                        <p>Нет избранных.</p>
                    @endforelse
                </div>
            </section>

            <section id="purchases" class="section-block">
                <div style="display:flex; justify-content:space-between; margin-bottom:20px;">
                    <h2>Списки покупок</h2>
                    <button id="create-shopping-list-btn" class="btn" style="height:35px; font-size:14px;">+ Новый список</button>
                </div>
                <div class="shopping-lists-grid" id="shopping-lists-container">
                </div>
            </section>
        </main>
    </div>
@endsection
@push('scripts')
<script>
    function escapeHtml(text) {
        if (typeof text !== 'string') return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    document.addEventListener('DOMContentLoaded', async function() {
        const container = document.getElementById('shopping-lists-container');
        const createBtn = document.getElementById('create-shopping-list-btn');

        async function loadLists() {
            try {
                const res = await fetch("/shopping-lists");
                if (!res.ok) throw new Error('Ошибка загрузки списков');
                const lists = await res.json();
                container.innerHTML = '';

                lists.forEach(list => {
                    const completed = list.items.filter(i => i.is_completed).length;
                    const total = list.items.length;
                    const progress = total ? `${completed} из ${total} пунктов` : 'Пусто';

                    // Экранируем имя списка
                    const escapedName = escapeHtml(list.name);

                    const ingredientsHtml = list.items.map(item => {
                        const checked = item.is_completed ? 'checked' : '';
                        const escapedIngredient = escapeHtml(item.ingredient_name);
                        const escapedQuantity = escapeHtml(item.quantity);
                        const itemId = escapeHtml(String(item.id)); // ID → строка

                        return `
                            <div class="ingredient-item">
                                <input
                                    type="checkbox"
                                    ${checked}
                                    data-item-id="${itemId}"
                                   >
                                <label>${escapedIngredient} (${escapedQuantity})</label>
                            </div>
                        `;
                    }).join('');

                    const card = document.createElement('div');
                    card.className = 'shopping-list-card';
                    card.innerHTML = `
                        <div class="list-header">
                            <span>${escapedName}</span>
                            <div>
                                <button class="edit-name-btn" data-list-id="${escapeHtml(String(list.id))}"><img src="{{ asset('images/edit.png') }}" alt="" style="width:25px; margin-right:5px;"></button>
                                <button class="delete-list-btn" data-list-id="${escapeHtml(String(list.id))}"><img src="{{ asset('images/delete.png') }}" alt="" style="width:25px; margin-right:5px;"></button>
                            </div>
                        </div>
                        <div class="list-content">
                            <h3>Ингредиенты</h3>
                            <div class="ingredients-list">
                                ${ingredientsHtml}
                            </div>
                            <div class="list-footer">
                                <span class="progress">${escapeHtml(progress)}</span>
                                <button class="download-btn" data-list-id="${escapeHtml(String(list.id))}">Скачать</button>
                            </div>
                        </div>
                    `;

                    container.appendChild(card);

                    card.querySelectorAll('.ingredient-item input[type="checkbox"]').forEach(checkbox => {
                        checkbox.addEventListener('change', function(e) {
                            toggleItem(
                                e.target.dataset.itemId,
                                e.target,
                                e.target.closest('.shopping-list-card')
                            );
                        });
                    });
                                // Назначаем обработчики
                    card.querySelector('.edit-name-btn').addEventListener('click', () => {
                        renameList(list.id, list.name);
                    });
                    card.querySelector('.delete-list-btn').addEventListener('click', () => {
                        deleteList(list.id);
                    });
                    card.querySelector('.download-btn').addEventListener('click', () => {
                        downloadList(list.id);
                    });
                    card.querySelectorAll('.ingredient-item input[type="checkbox"]').forEach(checkbox => {
                        checkbox.addEventListener('change', (e) => {
                            toggleItem(e.target.dataset.itemId);
                        });
                    });
                });
            } catch (error) {
                console.error('Ошибка:', error);
                container.innerHTML = '<p>Не удалось загрузить списки покупок.</p>';
            }
        }

        window.toggleItem = async (itemId, checkbox, listCard) => {
            // Сохраняем предыдущее состояние на случай ошибки
            const wasChecked = checkbox.checked;

            try {
                const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                if (!tokenMeta) throw new Error('CSRF-токен не найден');

                const res = await fetch(`/shopping-list-items/${itemId}`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': tokenMeta.getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                if (res.status === 404) {
                    alert('Элемент не найден. Возможно, он был удален.');
                    return;
                }

                if (!res.ok) {
                    throw new Error(`Ошибка сервера: ${res.status}`);
                }

                const updatedItem = await res.json();

                // Синхронизируем галочку с тем, что пришло с сервера (на всякий случай)
                checkbox.checked = Boolean(updatedItem.is_completed);

                // === ОБНОВЛЕНИЕ СЧЁТЧИКА ===
                // Вызываем функцию подсчета, передавая карточку списка
                updateListProgress(listCard);

            } catch (error) {
                console.error('Ошибка:', error);
                alert('Не удалось обновить статус. Попробуйте позже.');
                checkbox.checked = wasChecked; // Возвращаем как было
                updateListProgress(listCard); // Пересчитываем обратно
            }
        };

        // Убедитесь, что эта функция объявлена в глобальной области видимости (или внутри того же блока script)
        function updateListProgress(listCard) {
            const items = listCard.querySelectorAll('.ingredient-item input[type="checkbox"]');
            const total = items.length;
            const completed = Array.from(items).filter(cb => cb.checked).length;

            const progressText = total ? `${completed} из ${total} пунктов` : 'Пусто';

            const progressSpan = listCard.querySelector('.progress');
            if (progressSpan) {
                progressSpan.textContent = progressText;
            }
        }

        window.deleteList = async (listId) => {
            if (!confirm('Удалить список?')) return;
            try {
                await fetch(`/shopping-lists/${listId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') || '{{ csrf_token() }}' }
                });
                loadLists();
            } catch (error) {
                console.error('Ошибка удаления:', error);
            }
        };

        window.renameList = (listId, currentName) => {
            const newName = prompt('Новое название списка:', currentName);
            if (newName && newName.trim() && newName !== currentName) {
                alert('Обновление названия списка пока не реализовано.');
                // Позже: вызовите API для обновления
            }
        };

        window.downloadList = (listId) => {
            // Найти карточку списка по ID
            const listCard = document.querySelector(`.shopping-list-card`);
            // Но лучше искать по всем карточкам
            const allCards = document.querySelectorAll('.shopping-list-card');
            let targetCard = null;

            // Ищем карточку, у которой кнопка имеет data-list-id == listId
            allCards.forEach(card => {
                const btn = card.querySelector(`.download-btn[data-list-id="${listId}"]`);
                if (btn) targetCard = card;
            });

            if (!targetCard) {
                alert('Список не найден');
                return;
            }

            // Получаем название списка
            const listName = targetCard.querySelector('.list-header span')?.textContent || 'Список покупок';

            // Собираем ингредиенты
            const ingredients = [];
            targetCard.querySelectorAll('.ingredient-item').forEach(item => {
                const label = item.querySelector('label');
                const checkbox = item.querySelector('input[type="checkbox"]');
                if (label) {
                    const text = checkbox?.checked ? `[✓] ${label.textContent}` : `[ ] ${label.textContent}`;
                    ingredients.push(text);
                }
            });

            // Формируем текст
            let content = `📋 ${listName}\n\n`;
            content += ingredients.join('\n');

            // Создаём Blob и скачиваем
            const blob = new Blob([content], { type: 'text/plain;charset=utf-8' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `${listName.replace(/[^a-z0-9]/gi, '_')}.txt`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        };

        createBtn.addEventListener('click', async () => {
            const name = prompt('Название нового списка:');
            if (!name || !name.trim()) {
                alert('Введите название!');
                return;
            }

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                const res = await fetch("{{ route('shopping-lists.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({ name: name.trim() })
                });

                if (res.ok) {
                    loadLists();
                } else {
                    const errorData = await res.json().catch(() => ({ message: 'Неизвестная ошибка' }));
                    alert(`Не удалось создать список: ${errorData.message || 'Попробуйте позже.'}`);
                }
            } catch (error) {
                console.error('Ошибка:', error);
                alert('Произошла ошибка при создании списка.');
            }
        });

        // Убедитесь, что CSRF-токен доступен
        if (!document.querySelector('meta[name="csrf-token"]')) {
            document.head.insertAdjacentHTML('beforeend', '<meta name="csrf-token" content="{{ csrf_token() }}">');
        }

        loadLists();
    });

    // Обработчик для аватара (оставляем как есть)
    document.getElementById('avatar-input').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name || 'Файл не выбран';
        const button = document.querySelector('.upload-btn');
        if (fileName !== 'Файл не выбран') {
            button.textContent = fileName.length > 20 ? fileName.substring(0, 20) + '...' : fileName;
        } else {
            button.textContent = 'Загрузить новое';
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const burgerBtn = document.getElementById('burger-menu');
        const mobileMenu = document.getElementById('mobile-menu');

        burgerBtn?.addEventListener('click', function() {
            this.classList.toggle('active');
            mobileMenu.classList.toggle('open');
        });

        // Закрыть меню при клике вне его
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.header_content') && !e.target.closest('#mobile-menu')) {
                burgerBtn?.classList.remove('active');
                mobileMenu?.classList.remove('open');
            }
        });
    });
</script>
@endpush
