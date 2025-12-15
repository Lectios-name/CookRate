@auth
    @php
        $isOwn = ($recipe->user_id === auth()->id());
        $isFav = auth()->user()->favorites->contains($recipe->id);
    @endphp

    @if($isOwn)
        <span class="favorite-btn" style="cursor:default; opacity:0.5;">Своё</span>
    @elseif($isFav)
        <form action="{{ route('favorites.remove', $recipe) }}" method="POST">
            @csrf @method('DELETE')
            <button type="submit" class="favorite-btn active">
                <img src="{{ asset('images/heart-filled.png') }}" alt=""> В избранном
            </button>
        </form>
    @else
        <form action="{{ route('favorites.add', $recipe) }}" method="POST">
            @csrf
            <button type="submit" class="favorite-btn">
                <img src="{{ asset('images/heart.png') }}" alt=""> В избранное
            </button>
        </form>
    @endif
@else
    <a href="{{ route('auth') }}" class="favorite-btn">
        <img src="{{ asset('images/heart.png') }}" alt=""> В избранное
    </a>
@endauth
