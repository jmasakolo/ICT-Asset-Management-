<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Admin')</title>
    @yield('vite')
</head>
<body class="bg-gray-50 text-gray-900">
    <div class="flex min-h-screen">
        <aside class="w-56 shrink-0 border-r border-gray-200 bg-white py-6">
            <div class="mb-4 px-6">
                <span class="text-sm font-semibold uppercase tracking-wide text-gray-400">Admin</span>
            </div>
            <x-admin-nav :active="$__env->yieldContent('active')" />
        </aside>

        <div class="flex-1">
            <header class="border-b border-gray-200 bg-white">
                <div class="flex items-center justify-between px-6 py-4">
                    <div>
                        <h1 class="text-lg font-semibold">@yield('header', 'Admin')</h1>
                        @hasSection('subheader')
                            <p class="text-sm text-gray-500">@yield('subheader')</p>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-500 hover:text-gray-900">Log out</button>
                    </form>
                </div>
            </header>

            <main class="px-6 py-8">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
