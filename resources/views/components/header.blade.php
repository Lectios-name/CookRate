<header class="header">
    <div class="container header_content">
        <div class="logo">
            <a href="{{ route('main') }}"><img src="{{asset('images/logo.png')}}" alt="CookRate" class="logo_img"></a>
            <a href="{{ route('search') }}" class="nav_link">Рецепты</a>
        </div>

        <div class="search_bar">
            <form action="{{ route('search') }}" method="GET">
                <input type="text" name="q" placeholder="Поиск..." class="search_input" value="{{ request('q') }}">
                <button class="search-btn" type="submit"><img src="{{asset('images/find.png')}}" alt=""></button>
            </form>
        </div>

        <div class="header_buttons">
            @auth
                @if(auth()->user()->isAdmin)
                    <a href="{{ route('admin.index') }}" class="btn">Админ</a>
                @else
                    <a href="{{ route('recipes.create') }}" class="btn"><span>Добавить рецепт</span></a>
                @endif
                <a href="{{ route('personal') }}" class="btn"><img src="{{asset('images/icon_auth.png')}}" style="width:20px;"> <span>Профиль</span></a>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn" style="background:#333;">Выйти</button>
                </form>
            @endauth
            @guest
                <a href="{{ route('auth') }}" class="btn">Войти <img src="{{asset('images/icon_auth.png')}}" style="width:20px;"></a>
            @endguest
        </div>

        <button class="burger-btn" id="burger-menu">
            <div class="burger-line"></div>
            <div class="burger-line"></div>
            <div class="burger-line"></div>
        </button>
    </div>

    <nav class="mobile-menu" id="mobile-menu">
        <ul>
            <li><a href="{{ route('main') }}">Главная</a></li>
            <li><a href="{{ route('search') }}">Рецепты</a></li>
            @auth
                <li><a href="{{ route('personal') }}">Профиль</a></li>
                @if(!auth()->user()->isAdmin)
                    <li><a href="{{ route('recipes.create') }}">Добавить рецепт</a></li>
                @endif
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit">Выйти</button>
                    </form>
                </li>
            @else
                <li><a href="{{ route('auth') }}">Войти</a></li>
                <li><a href="{{ route('register') }}">Регистрация</a></li>
            @endauth
        </ul>
    </nav>
</header>
