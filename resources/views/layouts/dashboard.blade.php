<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Dashboard')</title>
    @yield('vite')
</head>
<body class="bg-gray-50 text-gray-900">
    <header class="border-b border-gray-200 bg-white">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-6 py-4">
            <div>
                <h1 class="text-lg font-semibold">@yield('header', 'Dashboard')</h1>
                @hasSection('subheader')
                    <p class="text-sm text-gray-500">@yield('subheader')</p>
                @endif
            </div>
            <form method="POST" action="{{ route($__env->yieldContent('logoutRoute', 'logout')) }}">
                @csrf
                <button type="submit" class="text-sm text-gray-500 hover:text-gray-900">Log out</button>
            </form>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-6 py-8">
        <x-dashboard-tabs
            :active="$__env->yieldContent('active')"
            :overview-route="$__env->yieldContent('overviewRoute', 'dashboard')"
            :assets-route="$__env->yieldContent('assetsRoute', 'assets.index')"
        />

        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
