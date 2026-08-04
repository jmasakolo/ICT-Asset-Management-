@extends('layouts.admin')

@section('title', 'Departments')
@section('header', 'Departments')
@section('subheader', 'Logged in as ' . auth()->guard('admin')->user()->email)
@section('active', 'departments')

@section('vite')
    @vite(['resources/css/app.css'])
@endsection

@section('content')
    @if (session('status'))
        <p class="mb-6 rounded-md bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('status') }}</p>
    @endif

    <div class="mb-8 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-sm font-medium text-gray-500">{{ $editing ? 'Edit department' : 'Add department' }}</h2>

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
              action="{{ $editing ? route('admin.departments.update', $editing) : route('admin.departments.store') }}"
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
                    {{ $editing ? 'Save changes' : 'Add department' }}
                </button>
                @if ($editing)
                    <a href="{{ route('admin.departments.index') }}" class="rounded-md px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">
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
                @forelse ($departments as $department)
                    <tr class="border-b border-gray-100 last:border-0">
                        <td class="px-4 py-3">{{ $department->name }}</td>
                        <td class="px-4 py-3">{{ $department->created_at->format('M j, Y') }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.departments.index', ['edit' => $department->id]) }}" class="text-gray-700 underline hover:text-gray-900">Edit</a>
                            <form method="POST" action="{{ route('admin.departments.destroy', $department) }}" class="inline"
                                  onsubmit="return confirm('Delete {{ $department->name }}? Assets in this department will become unassigned from it. This cannot be undone.');">
                                @csrf @method('DELETE')
                                <button type="submit" class="ml-3 text-red-600 underline hover:text-red-800">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-gray-500">
                            No departments yet. Add your first one above.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
