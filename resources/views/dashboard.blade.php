@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('header', 'My Dashboard')
@section('active', 'overview')

@section('vite')
    @vite(['resources/css/app.css', 'resources/js/dashboard.js'])
@endsection

@section('content')
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <x-stat-tile label="My Assets" :value="$stats['total']" />
        <x-stat-tile label="Active" :value="$stats['byStatus']['active'] ?? 0" />
        <x-stat-tile label="Maintenance" :value="$stats['byStatus']['maintenance'] ?? 0" />
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
@endsection

@push('scripts')
    <script type="application/json" id="dashboard-stats">@json(['byStatus' => $stats['byStatus'], 'byCategory' => $stats['byCategory']])</script>
@endpush
