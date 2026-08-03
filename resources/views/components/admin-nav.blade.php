@props(['active'])

@php
    $items = [
        'dashboard' => ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
        'assets' => ['label' => 'Asset Assignments', 'route' => 'admin.assets.index'],
        'users' => ['label' => 'Users', 'route' => 'admin.users.index'],
        'departments' => ['label' => 'Departments', 'route' => 'admin.departments.index'],
        'locations' => ['label' => 'Locations', 'route' => 'admin.locations.index'],
        'maintenance' => ['label' => 'Maintenance', 'route' => 'admin.maintenance.index'],
        'reports' => ['label' => 'Reports', 'route' => 'admin.reports.index'],
        'audit-logs' => ['label' => 'Audit Logs', 'route' => 'admin.audit-logs.index'],
        'settings' => ['label' => 'Settings', 'route' => 'admin.settings.index'],
    ];
@endphp

<nav class="flex flex-col gap-1 px-3">
    @foreach ($items as $key => $item)
        <a href="{{ route($item['route']) }}"
           class="rounded-md px-3 py-2 text-sm font-medium {{ $active === $key ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
            {{ $item['label'] }}
        </a>
    @endforeach
</nav>
