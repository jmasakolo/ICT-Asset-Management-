@extends('layouts.admin')

@section('title', 'Settings')
@section('header', 'Settings')
@section('subheader', 'Logged in as ' . auth()->guard('admin')->user()->email)
@section('active', 'settings')

@section('vite')
    @vite(['resources/css/app.css'])
@endsection

@section('content')
    @if (session('status'))
        <p class="mb-6 rounded-md bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('status') }}</p>
    @endif

    <div class="max-w-lg rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-50 px-4 py-3 text-sm text-red-800">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.settings.update') }}" class="grid gap-4">
            @csrf
            @method('PUT')
            <div>
                <label for="site_name" class="mb-1 block text-sm text-gray-700">Site name</label>
                <input type="text" name="site_name" id="site_name" value="{{ old('site_name', $siteName) }}" required
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label for="currency_symbol" class="mb-1 block text-sm text-gray-700">Currency symbol</label>
                <input type="text" name="currency_symbol" id="currency_symbol" value="{{ old('currency_symbol', $currencySymbol) }}" required
                       maxlength="5" class="w-24 rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">
                    Save settings
                </button>
            </div>
        </form>
    </div>
@endsection
