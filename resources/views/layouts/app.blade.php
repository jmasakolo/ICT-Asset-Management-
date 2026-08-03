<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'To Do')</title>
</head>
<body>
    <header>
        <a href="{{ route('tasks.index') }}"><h1>To Do</h1></a>
    </header>

    @if (session('status'))
        <p>{{ session('status') }}</p>
    @endif

    <main>
        @yield('content')
    </main>
</body>
</html>
