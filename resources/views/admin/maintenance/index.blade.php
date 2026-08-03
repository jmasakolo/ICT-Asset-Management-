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
        <h2 class="mb-4 text-sm font-medium text-gray-500">Log maintenance</h2>

        @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-50 px-4 py-3 text-sm text-red-800">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.maintenance.store') }}" class="grid gap-4 sm:grid-cols-2">
            @csrf
            <div class="sm:col-span-2">
                <label for="asset_id" class="mb-1 block text-sm text-gray-700">Asset</label>
                <select name="asset_id" id="asset_id" required
                        class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    <option value="" disabled selected>Select an asset</option>
                    @foreach ($assets as $asset)
                        <option value="{{ $asset->id }}" @selected((int) old('asset_id') === $asset->id)>{{ $asset->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <label for="description" class="mb-1 block text-sm text-gray-700">Description</label>
                <input type="text" name="description" id="description" value="{{ old('description') }}" required
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label for="performed_at" class="mb-1 block text-sm text-gray-700">Performed on</label>
                <input type="date" name="performed_at" id="performed_at" value="{{ old('performed_at') }}" required
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label for="cost" class="mb-1 block text-sm text-gray-700">Cost ({{ setting('currency_symbol', '$') }})</label>
                <input type="number" step="0.01" min="0" name="cost" id="cost" value="{{ old('cost') }}"
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">
                    Log maintenance
                </button>
            </div>
        </form>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-200 text-gray-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Asset</th>
                    <th class="px-4 py-3 font-medium">Description</th>
                    <th class="px-4 py-3 font-medium">Performed on</th>
                    <th class="px-4 py-3 font-medium">Cost</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($records as $record)
                    <tr class="border-b border-gray-100 last:border-0">
                        <td class="px-4 py-3">{{ $record->asset->name }}</td>
                        <td class="px-4 py-3">{{ $record->description }}</td>
                        <td class="px-4 py-3">{{ $record->performed_at->format('M j, Y') }}</td>
                        <td class="px-4 py-3">{{ $record->cost !== null ? setting('currency_symbol', '$').number_format($record->cost, 2) : '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                            No maintenance logged yet. Add your first record above.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
