@extends('layouts.app')
@section('title', 'Поиск рецептов')

@section('content')
    <section class="search-banner">
        <div class="container">
            <h1>Поиск рецептов</h1>
            <form class="search-form" method="GET" action="{{ route('search') }}">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Название блюда..." />
                <button type="submit">Найти</button>
            </form>
        </div>
    </section>

    <div class="container filters-section">
        <form method="GET" action="{{ route('search') }}">
            <input type="hidden" name="q" value="{{ request('q') }}">
            <div class="filters-header">
                <div>
                    <span style="font-size:18px; font-weight:bold;">Результатов: {{ $recipes->total() }}</span>
                </div>
                <div>
                    <select name="sort" onchange="this.form.submit()" style="padding:8px; border-radius:6px;">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Сначала новые</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Сначала старые</option>
                        <option value="time" {{ request('sort') == 'time' ? 'selected' : '' }}>По времени</option>
                    </select>
                </div>
            </div>
            <div class="filter-buttons">
                <button type="submit" name="category_id" value="" class="filter-btn {{ !request('category_id') ? 'active' : '' }}">Все</button>
                @foreach($categories as $category)
                    <button type="submit" name="category_id" value="{{ $category->id }}" class="filter-btn {{ request('category_id') == $category->id ? 'active' : '' }}">
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>
        </form>
    </div>

    <div class="container" style="margin-bottom: 50px;">
        <div class="recipes-grid">
            @forelse($recipes as $recipe)
                <div class="recipe-card">
                    <div class="card_header">
                        <div class="author">
                            <div class="author_avatar avatar-circle avatar-medium">
                                @if($recipe->user->avatar_url)
                                    <img src="{{ $recipe->user->avatar_url }}" alt="Av">
                                @else
                                    Ава
                                @endif
                            </div>
                            <span>{{ $recipe->user->name }}</span>
                        </div>
                        @include('components.favorite-btn', ['recipe' => $recipe])
                    </div>
                    <a href="{{ route('recipes.show', $recipe) }}" class="card-link">
                        <div class="card_image">
                            <img src="{{ asset($recipe->image_path) }}" alt="{{ $recipe->name }}">
                        </div>
                        <div class="card_body">
                            <h3 class="card_title">{{ $recipe->name }}</h3>
                            <p class="card_desc">{{ Str::limit($recipe->description, 80) }}</p>
                            <div class="card_footer">
                                <div class="meta-item">
                                    <img src="{{ asset('images/star.png') }}" alt="">
                                    <span>{{ number_format($recipe->average_rating, 1) }} ({{ $recipe->ratings_count }})</span>
                                </div>
                                <div class="meta-item">
                                    <img src="{{ asset('images/time.png') }}" alt="">
                                    <span>{{ $recipe->formatted_time ?: '—' }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <p>Ничего не найдено.</p>
            @endforelse
        </div>
        <div style="margin-top: 30px; display:flex; justify-content:center;">
            {{ $recipes->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
