<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{asset('css/style5.css')}}">
    <title>Авторизация</title>
</head>
<body>
    <div class="background-container">
        <div class="login-box">
            <h2 class="login-title">Войти</h2>
            <form class="login-form" method="post">
                @csrf
                <div class="form-group">
                    <label for="email">Логин:</label>
                    <input type="text" id="login" name="login" class="input-field @error('login') is-invalid @enderror">
                    @error('login')
                    <div class="">{{$message}}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password">Пароль:</label>
                    <input type="password" id="password" name="password" placeholder="Пароль" class="input-field @error('password') is-invalid @enderror">
                    @error('password')
                    <div class="">{{$message}}</div>
                    @enderror
                </div>

                <div class="buttons-container">
                    <button type="submit" class="button-primary">Войти</button>
                    <a href="{{route('register')}}" class="button-secondary">Зарегистрироваться</a>
                </div>
            </form>
        </div>
    </div>
</body>
