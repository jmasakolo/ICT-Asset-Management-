@extends('layouts.admin')

@section('title', 'Asset Assignments')
@section('header', 'Asset Assignments')
@section('subheader', 'Logged in as ' . auth()->guard('admin')->user()->email)
@section('active', 'assets')

@section('vite')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('content')
    @if (session('status'))
        <p class="mb-6 rounded-md bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('status') }}</p>
    @endif

    <div class="mb-8 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-sm font-medium text-gray-500">Add asset</h2>

        @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-50 px-4 py-3 text-sm text-red-800">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.assets.store') }}" class="grid gap-4 sm:grid-cols-2">
            @csrf
            <div>
                <label for="name" class="mb-1 block text-sm text-gray-700">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label for="category" class="mb-1 block text-sm text-gray-700">Category</label>
                <input type="text" name="category" id="category" value="{{ old('category') }}" required
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label for="status" class="mb-1 block text-sm text-gray-700">Status</label>
                <select name="status" id="status" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    @foreach (\App\Models\Asset::STATUSES as $status)
                        <option value="{{ $status }}" @selected(old('status', 'active') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="value" class="mb-1 block text-sm text-gray-700">Value ($)</label>
                <input type="number" step="0.01" min="0" name="value" id="value" value="{{ old('value') }}" required
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">
                    Add asset
                </button>
            </div>
        </form>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-200 text-gray-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Name</th>
                    <th class="px-4 py-3 font-medium">Category</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium">Value</th>
                    <th class="px-4 py-3 font-medium">Assigned to</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($assets as $asset)
                    <tr class="border-b border-gray-100 last:border-0">
                        <td class="px-4 py-3">{{ $asset->name }}</td>
                        <td class="px-4 py-3">{{ $asset->category }}</td>
                        <td class="px-4 py-3 capitalize">{{ $asset->status }}</td>
                        <td class="px-4 py-3">${{ number_format($asset->value, 2) }}</td>
                        <td class="px-4 py-3">{{ $asset->assignedUser?->name ?? 'Unassigned' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                            No assets yet. Add your first one above.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
