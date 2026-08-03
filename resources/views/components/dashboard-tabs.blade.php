@props(['active', 'overviewRoute' => 'dashboard', 'assetsRoute' => 'assets.index'])

<nav class="mb-8 flex gap-6 border-b border-gray-200">
    <a href="{{ route($overviewRoute) }}"
       class="border-b-2 px-1 pb-3 text-sm font-medium {{ $active === 'overview' ? 'border-gray-900 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
        Overview
    </a>
    <a href="{{ route($assetsRoute) }}"
       class="border-b-2 px-1 pb-3 text-sm font-medium {{ $active === 'assets' ? 'border-gray-900 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
        Asset Management
    </a>
</nav>
