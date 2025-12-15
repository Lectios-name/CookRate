@extends('layouts.app')

@section('title', 'Главная - CookRate')

@section('content')
    <section class="hero">
        <div class="container">
            <div class="hero_card">
                <div class="hero_image-wrapper">
                    <img src="{{ asset('images/povor.png') }}" alt="Повар" class="hero_img">
                </div>
                <div class="hero_text-content">
                    <h1 class="hero_title">Делитесь кулинарными шедеврами</h1>
                    <p class="hero_subtitle">Откройте мир вкусов вместе с нами!</p>
                </div>
            </div>
        </div>
    </section>

    <main class="container">
        <section class="big-search">
            <div class="big-search_wrapper">
                <form action="{{ route('search') }}" method="GET" style="display:flex; width:100%;">
                    <input type="text" name="q" class="big-search_input" placeholder="Что хотите приготовить?" value="{{ request('q') }}">
                    <button type="submit" class="big-search_btn">Найти</button>
                </form>
            </div>
        </section>

        <section class="categories">
            <div class="categories_grid">
                @foreach($categories as $category)
                    <a href="{{ route('search', ['category_id' => $category->id]) }}" class="category-card">{{ $category->name }}</a>
                @endforeach
            </div>
        </section>

        <section class="top-rated">
            <h2 class="section-title">Топ рейтинга</h2>
            <div class="recipes-grid">
                @forelse($topRecipes as $recipe)
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
                                        <img src="{{ asset('images/star.png') }}" alt="Star">
                                        <span>{{ number_format($recipe->average_rating, 1) }} ({{ $recipe->ratings_count }})</span>
                                    </div>
                                    <div class="meta-item">
                                        <img src="{{ asset('images/time.png') }}" alt="Time">
                                        <span>{{ $recipe->formatted_time ?: '—' }}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <p>Рецептов пока нет.</p>
                @endforelse
            </div>
        </section>
    </main>
@endsection
