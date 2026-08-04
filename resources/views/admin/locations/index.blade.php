@extends('layouts.admin')

@section('title', 'Locations')
@section('header', 'Locations')
@section('subheader', 'Logged in as ' . auth()->guard('admin')->user()->email)
@section('active', 'locations')

@section('vite')
    @vite(['resources/css/app.css'])
@endsection

@section('content')
    @if (session('status'))
        <p class="mb-6 rounded-md bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('status') }}</p>
    @endif

    <div class="mb-8 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-sm font-medium text-gray-500">{{ $editing ? 'Edit location' : 'Add location' }}</h2>

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
              action="{{ $editing ? route('admin.locations.update', $editing) : route('admin.locations.store') }}"
              class="flex gap-4">
            @csrf
            @if ($editing) @method('PUT') @endif
            <div class="flex-1">
                <label for="name" class="mb-1 block text-sm text-gray-700">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $editing?->name) }}" required
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div class="flex items-end gap-3">
                <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">
                    {{ $editing ? 'Save changes' : 'Add location' }}
                </button>
                @if ($editing)
                    <a href="{{ route('admin.locations.index') }}" class="rounded-md px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">
                        Cancel
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-200 text-gray-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Name</th>
                    <th class="px-4 py-3 font-medium">Created</th>
                    <th class="px-4 py-3 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($locations as $location)
                    <tr class="border-b border-gray-100 last:border-0">
                        <td class="px-4 py-3">{{ $location->name }}</td>
                        <td class="px-4 py-3">{{ $location->created_at->format('M j, Y') }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.locations.index', ['edit' => $location->id]) }}" class="text-gray-700 underline hover:text-gray-900">Edit</a>
                            <form method="POST" action="{{ route('admin.locations.destroy', $location) }}" class="inline"
                                  onsubmit="return confirm('Delete {{ $location->name }}? Assets at this location will become unassigned from it. This cannot be undone.');">
                                @csrf @method('DELETE')
                                <button type="submit" class="ml-3 text-red-600 underline hover:text-red-800">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-gray-500">
                            No locations yet. Add your first one above.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
