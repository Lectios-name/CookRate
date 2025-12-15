@extends('layouts.app')
@section('title', 'Панель администратора')

@section('content')
    <div class="container" style="margin-top: 30px;">
        <div class="admin-header">
            <h1>Панель администратора</h1>
        </div>

        <div class="tab-buttons">
            <button class="tab-btn active" data-tab="users">Пользователи</button>
            <button class="tab-btn" data-tab="recipes">Рецепты</button>
            <button class="tab-btn" data-tab="categories">Категории</button>
        </div>

        <div id="categories-tab" class="tab-content">
            <a href="{{ route('admin.categories.create') }}" class="btn btn-success" style="margin-bottom:15px; display:inline-flex;">+ Добавить</a>
            <table class="admin-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                @foreach($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->name }}</td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="edit-btn">
                                    <img src="{{ asset('images/edit.png') }}" alt="Редактировать"> Изменить
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-btn" onclick="return confirm('Удалить категорию?')">
                                        <img src="{{ asset('images/delete.png') }}" alt="Удалить"> Удалить
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div id="users-tab" class="tab-content active">
            <input type="text" id="search-users" class="form-control" placeholder="Поиск пользователя..." style="margin-bottom:15px; width:300px;">
            <div id="users-list">
                @include('admin.partials.users')
            </div>
        </div>

        <div id="recipes-tab" class="tab-content">
            <input type="text" id="search-recipes" class="form-control" placeholder="Поиск рецепта..." style="margin-bottom:15px; width:300px;">
            <div id="recipes-list">
                @include('admin.partials.recipes')
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>

    </script>
@endpush
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Табы
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                    btn.classList.add('active');
                    document.getElementById(btn.dataset.tab + '-tab').classList.add('active');
                });
            });
            // (Остальной JS поиска оставляем как был, он работает)
        });
        document.addEventListener('DOMContentLoaded', () => {
            // Поиск пользователей
            const searchUsers = document.getElementById('search-users');
            let usersTimeout;
            searchUsers?.addEventListener('input', () => {
                clearTimeout(usersTimeout);
                usersTimeout = setTimeout(() => {
                    fetch("{{ route('admin.search-users') }}?q=" + encodeURIComponent(searchUsers.value))
                        .then(res => res.text())
                        .then(html => {
                            document.getElementById('users-list').innerHTML = html;
                        });
                }, 500);
            });

            // Поиск рецептов
            const searchRecipes = document.getElementById('search-recipes');
            let recipesTimeout;
            searchRecipes?.addEventListener('input', () => {
                clearTimeout(recipesTimeout);
                recipesTimeout = setTimeout(() => {
                    fetch("{{ route('admin.search-recipes') }}?q=" + encodeURIComponent(searchRecipes.value))
                        .then(res => res.text())
                        .then(html => {
                            document.getElementById('recipes-list').innerHTML = html;
                        });
                }, 500);
            });
        });
    </script>
@endpush
