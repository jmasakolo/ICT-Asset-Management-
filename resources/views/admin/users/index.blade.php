@extends('layouts.admin')

@section('title', 'Users')
@section('header', 'Users')
@section('subheader', 'Logged in as ' . auth()->guard('admin')->user()->email)
@section('active', 'users')

@section('vite')
    @vite(['resources/css/app.css'])
@endsection

@section('content')
    @if (session('status'))
        <p class="mb-6 rounded-md bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('status') }}</p>
    @endif

    <div class="mb-8 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-sm font-medium text-gray-500">{{ $editing ? 'Edit user' : 'Add user' }}</h2>

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
              action="{{ $editing ? route('admin.users.update', $editing) : route('admin.users.store') }}"
              class="grid gap-4 sm:grid-cols-3">
            @csrf
            @if ($editing) @method('PUT') @endif
            <div>
                <label for="name" class="mb-1 block text-sm text-gray-700">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $editing?->name) }}" required
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label for="email" class="mb-1 block text-sm text-gray-700">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $editing?->email) }}" required
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label for="password" class="mb-1 block text-sm text-gray-700">
                    Password @if ($editing) <span class="text-gray-400">(leave blank to keep current)</span> @endif
                </label>
                <input type="password" name="password" id="password" {{ $editing ? '' : 'required' }}
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div class="sm:col-span-3 flex gap-3">
                <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">
                    {{ $editing ? 'Save changes' : 'Add user' }}
                </button>
                @if ($editing)
                    <a href="{{ route('admin.users.index') }}" class="rounded-md px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">
                        Cancel
                    </a>
                @endif
            </div>
        </form>
    </div>

    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-4 flex flex-wrap items-end gap-3">
        <div>
            <label for="search" class="mb-1 block text-sm text-gray-700">Search</label>
            <input type="text" name="search" id="search" value="{{ $search }}" placeholder="Name or email…"
                   class="w-64 rounded-md border border-gray-300 px-3 py-2 text-sm">
        </div>
        <button type="submit" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-800 hover:bg-gray-300">
            Search
        </button>
        @if ($search)
            <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600 underline hover:text-gray-900">Clear</a>
        @endif
    </form>

    <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-200 text-gray-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Name</th>
                    <th class="px-4 py-3 font-medium">Email</th>
                    <th class="px-4 py-3 font-medium">Assets assigned</th>
                    <th class="px-4 py-3 font-medium">Joined</th>
                    <th class="px-4 py-3 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr class="border-b border-gray-100 last:border-0">
                        <td class="px-4 py-3">{{ $user->name }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3">{{ $user->assets_count }}</td>
                        <td class="px-4 py-3">{{ $user->created_at->format('M j, Y') }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.users.index', ['edit' => $user->id]) }}" class="text-gray-700 underline hover:text-gray-900">Edit</a>
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline"
                                  onsubmit="return confirm('Delete {{ $user->name }}? Their assets will become unassigned. This cannot be undone.');">
                                @csrf @method('DELETE')
                                <button type="submit" class="ml-3 text-red-600 underline hover:text-red-800">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                            No users match{{ $search ? ' your search' : ' yet. Add your first one above.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
