@extends('layouts.app')
@section('title', $recipe->name)

@section('content')
    <div class="recipe-hero-banner" style="background-image: url('{{ asset($recipe->image_path) }}');">
        <div class="recipe-hero-overlay">
            <div class="container">
                <h1 class="recipe-hero-title">{{ $recipe->name }}</h1>
                <div class="recipe-hero-meta">
                    <div class="recipe-hero-author">
                        <div class="author_avatar">
                            @if($recipe->user->avatar_url)
                                <img src="{{ $recipe->user->avatar_url }}" alt="Avatar">
                            @else
                                Ава
                            @endif
                        </div>
                        <span>Автор: {{ $recipe->user->name }} {{ $recipe->user->surname }}</span>
                    </div>

                    <div class="recipe-hero-rating">
                        <img src="{{ asset('images/star.png') }}" alt="Star">
                        <span id="average-rating">{{ number_format($recipe->average_rating, 1) }}</span>
                        <span id="ratings-count">({{ $recipe->ratings_count }} отзывов)</span>
                    </div>

                    <div class="recipe-hero-rating" style="background: rgba(255,255,255,0.2);">
                        <img src="{{ asset('images/time.png') }}" alt="Time">
                        <span>{{ $recipe->formatted_time ?: 'Не указано' }}</span>
                    </div>

                    @auth
                        @php
                            $isOwn = ($recipe->user_id === auth()->id());
                            $isFav = auth()->user()->favorites->contains($recipe->id);
                        @endphp
                        @if(!$isOwn)
                            @if($isFav)
                                <form action="{{ route('favorites.remove', $recipe) }}" method="POST" style="margin-left:auto;">
                                    @csrf @method('DELETE')
                                    <button class="btn" style="background:white; color:var(--primary);">
                                        <img src="{{ asset('images/heart-filled.png') }}"> В избранном
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('favorites.add', $recipe) }}" method="POST" style="margin-left:auto;">
                                    @csrf
                                    <button class="btn">
                                        <img src="{{ asset('images/heart.png') }}" style="filter: brightness(0) invert(1);"> В избранное
                                    </button>
                                </form>
                            @endif
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <div class="container recipe-content-container">
        <aside class="ingredients-sidebar">
            <h3>Ингредиенты</h3>
            <p style="font-size:14px; color:#666; margin-bottom:15px;">Отметьте, что нужно купить:</p>

            <div class="ingredients-list-check">
                @foreach ($recipe->ingredients as $ing)
                    <div class="ing-item">
                        <input type="checkbox" id="ing-{{ $ing->id }}"
                               data-name="{{ $ing->name }}"
                               data-quantity="{{ $ing->quantity }}" checked>
                        <label for="ing-{{ $ing->id }}">{{ $ing->name }} <b>{{ $ing->quantity }}</b></label>
                    </div>
                @endforeach
            </div>

            <div class="shopping-controls">
                <div class="shop-select-row">
                    <select id="shopping-list-select" class="shop-select">
                        <option value="">Загрузка...</option>
                    </select>
                    <button type="button" id="create-new-list-btn" class="btn-create-list">+</button>
                </div>
                <button id="add-to-shopping-list-btn" class="btn-add-shopping">Добавить в список</button>
            </div>

            @if($recipe->description)
                <div style="margin-top:30px; padding-top:20px; border-top:1px solid #eee;">
                    <h4>Описание</h4>
                    <p style="font-size:15px; color:#555; margin-top:5px;">{{ $recipe->description }}</p>
                </div>
            @endif
        </aside>

        <div class="steps-main">
            <h2 style="margin-bottom:20px;">Пошаговое приготовление</h2>

            @foreach ($recipe->steps as $step)
                <div class="step-block">
                    <div class="step-header-new">
                        <div class="step-num-big">{{ $step->step_number }}.</div>
                        <div class="step-desc-text">{{ $step->description }}</div>
                    </div>
                    @if($step->photo_path)
                        <div class="step-img-new">
                            <img src="{{ asset($step->photo_path) }}" alt="Шаг {{ $step->step_number }}">
                        </div>
                    @endif
                </div>
            @endforeach

            <section class="reviews-section">
                <div class="reviews-header">
                    <h3>Отзывы</h3>
                    <button class="btn-show-review" id="show-review-form">Оставить отзыв</button>
                </div>

                <div id="review-form" class="review-form-box" style="display:none;">
                    <h4>Ваша оценка</h4>
                    <div class="star-rating-input">
                        @foreach([1,2,3,4,5] as $rate)
                            <input type="radio" id="st{{$rate}}" name="rating" value="{{$rate}}" hidden>
                            <label for="st{{$rate}}"><img src="{{asset('images/star.png')}}" width="16"> {{$rate}}</label>
                        @endforeach
                    </div>

                    <div id="comment-fields" style="display:none;">
                        <textarea id="comment_text" class="comment-textarea" placeholder="Расскажите, как получилось..."></textarea>
                        <input type="file" id="comment_photo" accept="image/*" style="margin-bottom:15px;">

                        <div class="review-actions">
                            <button type="button" id="submit-comment-btn" class="btn-submit-review">Отправить отзыв</button>
                            <button type="button" id="cancel-comment-btn" class="btn-cancel-review">Отмена</button>
                        </div>
                    </div>

                    <div id="rating-only-actions">
                        <button type="button" id="submit-rating-btn" class="btn-submit-review">Оценить</button>
                        <button type="button" id="add-comment-btn" style="background:#ddd; padding:10px 20px; border-radius:6px;">Написать текст</button>
                    </div>
                </div>

                <div id="comments-list">
                    @foreach($recipe->comments as $comment)
                        <div class="comment-card">
                            <div style="display:flex; justify-content:space-between;">
                                <div class="author" style="margin-bottom:10px;">
                                    <div class="author_avatar">
                                        @if($comment->user->avatar_url)
                                            <img src="{{ $comment->user->avatar_url }}" alt="Av">
                                        @else
                                            Ава
                                        @endif
                                    </div>
                                    <span style="font-weight:bold;">{{ $comment->user->name }}</span>
                                </div>
                                <div class="meta-item">
                                    <img src="{{ asset('images/star.png') }}"> <b>{{ $comment->rating_value }}</b>
                                </div>
                            </div>

                            @if($comment->text)
                                <p style="margin-bottom:10px;">{{ $comment->text }}</p>
                            @endif

                            @if($comment->photo_path)
                                <img src="{{ asset('storage/' . $comment->photo_path) }}" style="max-width:200px; border-radius:6px;">
                            @endif
                            <div style="font-size:12px; color:#999; margin-top:5px;">{{ $comment->created_at->format('d.m.Y') }}</div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', async function() {

            // --- ПОЛУЧЕНИЕ CSRF ТОКЕНА ---
            const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            // ==========================================
            // 1. ЛОГИКА ОТЗЫВОВ И РЕЙТИНГА
            // ==========================================
            const showReviewBtn = document.getElementById('show-review-form');
            const reviewForm = document.getElementById('review-form');

            // Кнопки внутри формы
            const addCommentBtn = document.getElementById('add-comment-btn'); // Кнопка "Написать текст"
            const ratingActions = document.getElementById('rating-only-actions'); // Блок с кнопками "Оценить" и "Написать текст"
            const commentFields = document.getElementById('comment-fields'); // Поля ввода текста и фото
            const cancelBtn = document.getElementById('cancel-comment-btn'); // Отмена написания текста

            const submitRatingBtn = document.getElementById('submit-rating-btn'); // Отправить только оценку
            const submitCommentBtn = document.getElementById('submit-comment-btn'); // Отправить полный отзыв

            // Показать форму
            if(showReviewBtn) {
                showReviewBtn.addEventListener('click', () => {
                    reviewForm.style.display = 'block';
                    showReviewBtn.style.display = 'none';
                });
            }

            // Переключиться на ввод текста
            if(addCommentBtn) {
                addCommentBtn.addEventListener('click', () => {
                    ratingActions.style.display = 'none';
                    commentFields.style.display = 'block';
                });
            }

            // Отмена ввода текста (вернуться к выбору оценки)
            if(cancelBtn) {
                cancelBtn.addEventListener('click', () => {
                    commentFields.style.display = 'none';
                    ratingActions.style.display = 'flex'; // flex, так как там кнопки в ряд
                    document.getElementById('comment_text').value = '';
                    document.getElementById('comment_photo').value = '';
                });
            }

            // Функция обновления звезд на странице
            function updatePageRating(avgRating, count) {
                // Обновляем текст
                const avgEl = document.getElementById('average-rating');
                const countEl = document.getElementById('ratings-count');
                if(avgEl) avgEl.textContent = avgRating;
                if(countEl) countEl.textContent = `(${count} отзывов)`;

                // Можем обновить и херо-баннер, если там есть классы
                // (в коде выше мы использовали те же ID)
            }

            // ОТПРАВКА ТОЛЬКО ОЦЕНКИ
            if(submitRatingBtn) {
                submitRatingBtn.addEventListener('click', async () => {
                    const rating = document.querySelector('input[name="rating"]:checked')?.value;
                    if(!rating) return alert('Пожалуйста, выберите количество звезд!');

                    try {
                        const res = await fetch("{{ route('recipes.rate', $recipe) }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': getCsrfToken(),
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `value=${rating}`
                        });

                        if(res.ok) {
                            const data = await res.json();
                            updatePageRating(data.average_rating, data.ratings_count);
                            alert('Ваша оценка принята!');
                            reviewForm.style.display = 'none'; // Скрываем форму
                            showReviewBtn.style.display = 'none'; // Кнопку "оставить отзыв" тоже можно скрыть или поменять текст
                        } else {
                            const err = await res.json();
                            alert(err.error || 'Ошибка при отправке оценки');
                        }
                    } catch (e) {
                        console.error(e);
                        alert('Ошибка сети');
                    }
                });
            }

            // ОТПРАВКА ПОЛНОГО ОТЗЫВА
            if(submitCommentBtn) {
                submitCommentBtn.addEventListener('click', async () => {
                    const rating = document.querySelector('input[name="rating"]:checked')?.value;
                    const text = document.getElementById('comment_text').value;
                    const photo = document.getElementById('comment_photo').files[0];

                    if(!rating) return alert('Поставьте оценку!');
                    if(!text && !photo) return alert('Напишите отзыв или прикрепите фото!');

                    const formData = new FormData();
                    formData.append('rating_value', rating);
                    if(text) formData.append('text', text);
                    if(photo) formData.append('photo', photo);

                    try {
                        const res = await fetch("{{ route('recipes.comment', $recipe) }}", {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': getCsrfToken() },
                            body: formData
                        });

                        if(res.ok) {
                            const data = await res.json();
                            updatePageRating(data.average_rating, data.ratings_count);

                            // Добавляем комментарий в список
                            const list = document.getElementById('comments-list');
                            if(list && data.comment_html) {
                                list.insertAdjacentHTML('afterbegin', data.comment_html);
                            }

                            alert('Отзыв опубликован!');
                            reviewForm.style.display = 'none';

                            // Очистка
                            document.getElementById('comment_text').value = '';
                            document.getElementById('comment_photo').value = '';
                            const checkedStar = document.querySelector('input[name="rating"]:checked');
                            if(checkedStar) checkedStar.checked = false;

                        } else {
                            const err = await res.json();
                            alert(err.error || 'Ошибка при отправке отзыва');
                        }
                    } catch (e) {
                        console.error(e);
                        alert('Ошибка сети');
                    }
                });
            }


            // ==========================================
            // 2. СПИСОК ПОКУПОК
            // ==========================================
            const listSelect = document.getElementById('shopping-list-select');
            const createListBtn = document.getElementById('create-new-list-btn');
            const addToListBtn = document.getElementById('add-to-shopping-list-btn');

            // Загрузка списков при старте
            async function loadShoppingLists() {
                if(!listSelect) return;
                try {
                    const res = await fetch("{{ route('shopping-lists.index') }}");
                    if(res.ok) {
                        const lists = await res.json();
                        listSelect.innerHTML = '<option value="">Выберите список...</option>';
                        lists.forEach(list => {
                            const opt = document.createElement('option');
                            opt.value = list.id;
                            opt.textContent = list.name;
                            listSelect.appendChild(opt);
                        });
                    }
                } catch (e) {
                    console.error('Не удалось загрузить списки покупок', e);
                }
            }

            // Инициализация загрузки списков
            if(listSelect) loadShoppingLists();

            // Создание нового списка
            if(createListBtn) {
                createListBtn.addEventListener('click', async () => {
                    const name = prompt('Введите название нового списка:');
                    if(!name) return;

                    try {
                        const res = await fetch("{{ route('shopping-lists.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': getCsrfToken()
                            },
                            body: JSON.stringify({ name: name })
                        });

                        if(res.ok) {
                            await loadShoppingLists(); // Перезагружаем списки
                            alert('Список создан!');
                        } else {
                            alert('Ошибка при создании списка');
                        }
                    } catch (e) {
                        console.error(e);
                        alert('Ошибка сети');
                    }
                });
            }

            // Добавление ингредиентов
            if(addToListBtn) {
                addToListBtn.addEventListener('click', async () => {
                    const listId = listSelect.value;
                    if(!listId) return alert('Сначала выберите список покупок!');

                    // Собираем отмеченные ингредиенты
                    const checkboxes = document.querySelectorAll('.ing-item input[type="checkbox"]:checked');
                    if(checkboxes.length === 0) return alert('Выберите хотя бы один ингредиент!');

                    const ingredients = [];
                    checkboxes.forEach(cb => {
                        ingredients.push({
                            name: cb.dataset.name,
                            quantity: cb.dataset.quantity
                        });
                    });

                    try {
                        const res = await fetch("{{ route('shopping-lists.add-items') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': getCsrfToken()
                            },
                            body: JSON.stringify({
                                list_id: listId,
                                ingredients: ingredients
                            })
                        });

                        if(res.ok) {
                            alert('Ингредиенты добавлены в список!');
                        } else {
                            alert('Ошибка при добавлении');
                        }
                    } catch (e) {
                        console.error(e);
                        alert('Ошибка сети');
                    }
                });
            }
        });
    </script>
@endpush
