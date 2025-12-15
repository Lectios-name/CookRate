<div class="recipes-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
    @forelse($recipes as $recipe)
        <section class="recipe-card" style="border:1px solid #ddd; border-radius:8px; padding:15px; background:#fff;">
            <div class="card_header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                <div class="author">
                    <div class="author_avatar avatar-circle avatar-medium">
                        @if($recipe->user->avatar_url)
                            <img src="{{ $recipe->user->avatar_url }}" alt="Av">
                        @else
                            Ава
                        @endif
                    </div>
                    <span class="author_name">{{ $recipe->user->name ?? 'Автор' }}</span>
                </div>
                <div class="action-buttons">
                    <a href="{{ route('recipes.edit', $recipe) }}" class="edit-btn">
                        <img src="{{ asset('images/edit.png') }}" alt="Редактировать"> Изменить
                    </a>
                    <form action="{{ route('admin.recipes.destroy', $recipe) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="delete-btn" onclick="return confirm('Удалить рецепт?')">
                            <img src="{{ asset('images/delete.png') }}" alt="Удалить"> Удалить
                        </button>
                    </form>
                </div>
            </div>

            <a href="{{ route('recipes.show', $recipe) }}" style="text-decoration:none; color:inherit;">
                <div class="card_image" style="margin-bottom:10px;">
                    <img src="{{ asset($recipe->image_path) }}" alt="{{ $recipe->name }}" style="width:100%; height:180px; object-fit:cover; border-radius:4px;">
                </div>
                <div class="card_body">
                    <h3 class="card_title" style="font-size:1.2em; margin:0 0 8px;">{{ $recipe->name }}</h3>
                    <p class="card_desc" style="margin:0 0 10px; color:#555;">{{ Str::limit($recipe->description, 80) }}</p>
                    <div class="card_footer" style="display:flex; justify-content:space-between; font-size:0.9em;">
                        <div class="block1">
                            <span>⭐ {{ number_format($recipe->average_rating, 1) }} ({{ $recipe->ratings_count }})</span>
                        </div>
                        <div class="block1">
                            <span>{{ $recipe->formatted_time ?: '—' }}</span>
                        </div>
                    </div>
                </div>
            </a>
        </section>
    @empty
        <p>Рецепты не найдены</p>
    @endforelse
</div>

{{ $recipes->links() }}
