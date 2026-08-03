@extends('layouts.admin')

@section('title', 'Reports')
@section('header', 'Reports')
@section('subheader', 'Logged in as ' . auth()->guard('admin')->user()->email)
@section('active', 'reports')

@section('vite')
    @vite(['resources/css/app.css'])
@endsection

@section('content')
    <h2 class="mb-4 text-sm font-medium text-gray-500">Maintenance</h2>
    <div class="mb-8 grid grid-cols-3 gap-4">
        <x-stat-tile label="Records logged" :value="$maintenance['count']" />
        <x-stat-tile label="Total cost" value="{{ setting('currency_symbol', '$') }}{{ number_format($maintenance['totalCost'], 2) }}" />
        <x-stat-tile label="Average cost" value="{{ setting('currency_symbol', '$') }}{{ number_format($maintenance['averageCost'], 2) }}" />
    </div>

    <h2 class="mb-4 text-sm font-medium text-gray-500">Users</h2>
    <div class="mb-8 grid grid-cols-3 gap-4">
        <x-stat-tile label="Total users" :value="$users['total']" />
        <x-stat-tile label="With assets assigned" :value="$users['withAssets']" />
        <x-stat-tile label="Without assets assigned" :value="$users['withoutAssets']" />
    </div>

    <h2 class="mb-4 text-sm font-medium text-gray-500">Top assets by maintenance cost</h2>
    <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-200 text-gray-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Asset</th>
                    <th class="px-4 py-3 font-medium">Records</th>
                    <th class="px-4 py-3 font-medium">Total cost</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($topAssetsByCost as $asset)
                    <tr class="border-b border-gray-100 last:border-0">
                        <td class="px-4 py-3">{{ $asset->name }}</td>
                        <td class="px-4 py-3">{{ $asset->maintenance_records_count }}</td>
                        <td class="px-4 py-3">{{ setting('currency_symbol', '$') }}{{ number_format($asset->maintenance_records_sum_cost ?? 0, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-gray-500">
                            No maintenance costs logged yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
