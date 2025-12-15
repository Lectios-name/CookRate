<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{asset('css/style5.css')}}">
    <title>Регистрация</title>
</head>
<body>
<div class="background-container">
    <div class="login-box"> <h2 class="login-title">Регистрация</h2>
        <form class="login-form" method="post">
            @csrf
            <div class="form-group">
                <label for="name">Имя</label>
                <input type="text" id="name" name="name" placeholder="Иван" class="input-field @error('name') is-invalid @enderror">
                @error('name')
                <div class="invalid-feedback">{{$message}}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="surname">Фамилия</label>
                <input type="text" id="surname" name="surname" placeholder="Иванов" class="input-field @error('surname') is-invalid @enderror">
                @error('surname')
                <div class="invalid-feedback">{{$message}}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="login" class="form-label">Логин<span class="required">*</span></label>
                <input type="text" id="login" name="login" class="input-field @error('login') is-invalid @enderror">
                @error('login')
                <div class="invalid-feedback">{{$message}}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="email-reg">E-mail<span class="required">*</span></label>
                <input type="email" id="email" name="email" placeholder="example@mail.com" class="input-field @error('email') is-invalid @enderror">
                @error('email')
                <div class="invalid-feedback">{{$message}}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="password-reg">Пароль<span class="required">*</span></label>
                <input type="password" id="password" name="password" placeholder="Пароль" class="input-field @error('password') is-invalid @enderror">
                @error('password')
                <div class="invalid-feedback">{{$message}}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="confirm-password">Подтвердите пароль<span class="required">*</span></label>
                <input type="password" id="password_repeat" name="password_repeat" placeholder="Пароль" class="input-field @error('password_repeat') is-invalid @enderror">
                @error('password_repeat')
                <div class="invalid-feedback">{{$message}}</div>
                @enderror
            </div>

            <div class="buttons-container register-buttons">
                <button type="submit" class="button-primary">Зарегистрироваться</button>
                <a href="{{route('auth')}}" class="button-link-text">У меня же есть аккаунт</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>

