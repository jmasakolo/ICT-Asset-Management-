<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/dashboard.js'])
</head>
<body class="bg-gray-50 text-gray-900">
    <header class="border-b border-gray-200 bg-white">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-6 py-4">
            <div>
                <h1 class="text-lg font-semibold">Admin Dashboard</h1>
                <p class="text-sm text-gray-500">Logged in as {{ auth()->guard('admin')->user()->email }}</p>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-500 hover:text-gray-900">Log out</button>
            </form>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-6 py-8">
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
            <x-stat-tile label="Total Assets" :value="$stats['total']" />
            <x-stat-tile label="Active" :value="$stats['byStatus']['active'] ?? 0" />
            <x-stat-tile label="Unassigned" :value="$stats['unassigned']" />
            <x-stat-tile label="Total Value" value="${{ number_format($stats['totalValue'], 2) }}" />
        </div>

        <div class="mt-8 grid gap-6 sm:grid-cols-2">
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <h2 class="mb-4 text-sm font-medium text-gray-500">By Status</h2>
                <canvas id="status-chart"></canvas>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <h2 class="mb-4 text-sm font-medium text-gray-500">By Category</h2>
                <canvas id="category-chart"></canvas>
            </div>
        </div>
    </main>

    <script type="application/json" id="dashboard-stats">@json(['byStatus' => $stats['byStatus'], 'byCategory' => $stats['byCategory']])</script>
</body>
</html>
