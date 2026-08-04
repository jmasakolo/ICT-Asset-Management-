@extends('layouts.admin')

@section('title', 'Maintenance')
@section('header', 'Maintenance')
@section('subheader', 'Logged in as ' . auth()->guard('admin')->user()->email)
@section('active', 'maintenance')

@section('vite')
    @vite(['resources/css/app.css'])
@endsection

@section('content')
    @if (session('status'))
        <p class="mb-6 rounded-md bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('status') }}</p>
    @endif

    <div class="mb-8 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-sm font-medium text-gray-500">{{ $editing ? 'Edit maintenance record' : 'Log maintenance' }}</h2>

        @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-50 px-4 py-3 text-sm text-red-800">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
              action="{{ $editing ? route('admin.maintenance.update', $editing) : route('admin.maintenance.store') }}"
              class="grid gap-4 sm:grid-cols-2">
            @csrf
            @if ($editing) @method('PUT') @endif
            <div class="sm:col-span-2">
                <label for="asset_id" class="mb-1 block text-sm text-gray-700">Asset</label>
                <select name="asset_id" id="asset_id" required
                        class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    <option value="" disabled {{ old('asset_id', $editing?->asset_id) ? '' : 'selected' }}>Select an asset</option>
                    @foreach ($assets as $asset)
                        <option value="{{ $asset->id }}" @selected((int) old('asset_id', $editing?->asset_id) === $asset->id)>{{ $asset->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <label for="description" class="mb-1 block text-sm text-gray-700">Description</label>
                <input type="text" name="description" id="description" value="{{ old('description', $editing?->description) }}" required
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label for="performed_at" class="mb-1 block text-sm text-gray-700">Performed on</label>
                <input type="date" name="performed_at" id="performed_at"
                       value="{{ old('performed_at', $editing?->performed_at?->toDateString()) }}" required
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label for="cost" class="mb-1 block text-sm text-gray-700">Cost ({{ setting('currency_symbol', '$') }})</label>
                <input type="number" step="0.01" min="0" name="cost" id="cost" value="{{ old('cost', $editing?->cost) }}"
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div class="sm:col-span-2 flex gap-3">
                <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">
                    {{ $editing ? 'Save changes' : 'Log maintenance' }}
                </button>
                @if ($editing)
                    <a href="{{ route('admin.maintenance.index') }}" class="rounded-md px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">
                        Cancel
                    </a>
                @endif
            </div>
        </form>
    </div>

    <form method="GET" action="{{ route('admin.maintenance.index') }}" class="mb-4 flex flex-wrap items-end gap-3">
        <div>
            <label for="search" class="mb-1 block text-sm text-gray-700">Search</label>
            <input type="text" name="search" id="search" value="{{ $search }}" placeholder="Asset or description…"
                   class="w-64 rounded-md border border-gray-300 px-3 py-2 text-sm">
        </div>
        <button type="submit" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-800 hover:bg-gray-300">
            Search
        </button>
        @if ($search)
            <a href="{{ route('admin.maintenance.index') }}" class="text-sm text-gray-600 underline hover:text-gray-900">Clear</a>
        @endif
    </form>

    <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-200 text-gray-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Asset</th>
                    <th class="px-4 py-3 font-medium">Description</th>
                    <th class="px-4 py-3 font-medium">Performed on</th>
                    <th class="px-4 py-3 font-medium">Cost</th>
                    <th class="px-4 py-3 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($records as $record)
                    <tr class="border-b border-gray-100 last:border-0">
                        <td class="px-4 py-3">{{ $record->asset->name }}</td>
                        <td class="px-4 py-3">{{ $record->description }}</td>
                        <td class="px-4 py-3">{{ $record->performed_at->format('M j, Y') }}</td>
                        <td class="px-4 py-3">{{ $record->cost !== null ? setting('currency_symbol', '$').number_format($record->cost, 2) : '—' }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.maintenance.index', ['edit' => $record->id]) }}" class="text-gray-700 underline hover:text-gray-900">Edit</a>
                            <form method="POST" action="{{ route('admin.maintenance.destroy', $record) }}" class="inline"
                                  onsubmit="return confirm('Delete this maintenance record? This cannot be undone.');">
                                @csrf @method('DELETE')
                                <button type="submit" class="ml-3 text-red-600 underline hover:text-red-800">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                            {{ $search ? 'No maintenance records match your search.' : 'No maintenance logged yet. Add your first record above.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
