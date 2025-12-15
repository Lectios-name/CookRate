@extends('layouts.app')

@section('title', 'Редактировать категорию')

@section('content')
    <div class="admin-form-container">
        <h2>Редактировать категорию</h2>
        <form action="{{ route('admin.categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Название категории</label>
                <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
            </div>
            <button type="submit" class="btn-primary">Сохранить</button>
            <a href="{{ route('admin.index') }}" class="btn-secondary">Отмена</a>
        </form>
    </div>
@endsection
