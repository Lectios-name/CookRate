<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;700&display=swap" rel="stylesheet">

    @stack('styles')

    <title>@yield('title', 'CookRate')</title>
</head>
<body>
@include('components.header')

<div class="alert-container">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

@yield('content')

@include('components.footer')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const burgerBtn = document.getElementById('burger-menu');
        const mobileMenu = document.getElementById('mobile-menu');

        if (burgerBtn && mobileMenu) {
            burgerBtn.addEventListener('click', function(e) {
                e.stopPropagation(); // Останавливаем всплытие
                this.classList.toggle('active');
                mobileMenu.classList.toggle('open');
            });

            // Закрыть при клике вне меню
            document.addEventListener('click', function(e) {
                if (!mobileMenu.contains(e.target) && !burgerBtn.contains(e.target)) {
                    burgerBtn.classList.remove('active');
                    mobileMenu.classList.remove('open');
                }
            });
        }
        const alerts = document.querySelectorAll('.alert');
        if(alerts.length > 0) {
            setTimeout(() => {
                alerts.forEach(a => {
                    a.style.transition = "opacity 0.5s";
                    a.style.opacity = "0";
                    setTimeout(() => a.remove(), 500);
                });
            }, 4000);
        }
    });
</script>

@stack('scripts')
</body>
</html>

