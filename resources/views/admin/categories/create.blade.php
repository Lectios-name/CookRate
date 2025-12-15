@extends('layouts.app')

@section('title', 'Добавить категорию')

@section('content')
    <div class="admin-form-container">
        <h2>Добавить новую категорию</h2>
        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Название категории</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <button type="submit" class="btn-primary">Сохранить</button>
            <a href="{{ route('admin.index') }}" class="btn-secondary">Отмена</a>
        </form>
    </div>
@endsection
