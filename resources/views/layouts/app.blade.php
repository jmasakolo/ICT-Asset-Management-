<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'To Do')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="wrap">
        <header class="site-header">
            <a class="brand" href="{{ route('tasks.index') }}">To&nbsp;Do</a>
            @yield('header-actions')
        </header>

        @if (session('status'))
            <p class="flash" role="status">{{ session('status') }}</p>
        @endif

        <main>
            @yield('content')
        </main>

        <footer class="site-footer">
            <a class="footer-link" href="/app">Mobile app</a>
            <span aria-hidden="true">&middot;</span>
            <a class="footer-link" href="{{ asset('downloads/todo-app.apk') }}">Download Android APK</a>
            <span aria-hidden="true">&middot;</span>
            <a class="footer-link" href="{{ asset('downloads/asset-intake-app.apk') }}">Download Asset Intake APK</a>
            <span aria-hidden="true">&middot;</span>
            <a class="footer-link" href="/guide">User guide</a>
            <span aria-hidden="true">&middot;</span>
            Laravel {{ app()->version() }} &middot; PHP {{ PHP_VERSION }} &middot; PostgreSQL
        </footer>
    </div>
</body>
</html>
