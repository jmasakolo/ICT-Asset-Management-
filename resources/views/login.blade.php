<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log In</title>
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen items-center justify-center bg-gray-50 px-4">
    <div class="w-full max-w-sm">
        <div class="mb-8 flex flex-col items-center text-center">
            <span class="text-2xl font-semibold tracking-tight text-gray-900">ICT Asset Management</span>
            <p class="mt-4 text-sm font-medium text-blue-700">
                IT Asset Tracking &amp; Inventory Management
            </p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden p-0">
            <div class="h-1 bg-gradient-to-r from-blue-600 to-gray-800"></div>
            <div class="p-6">
                @if ($errors->any())
                    <div class="mb-4 rounded-md bg-red-50 px-4 py-3 text-sm text-red-800">
                        <ul class="list-inside list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                               autocomplete="username"
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="password" class="mb-1 block text-sm font-medium text-gray-700">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <label class="flex items-center gap-2 text-sm text-gray-600">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                        Remember me
                    </label>

                    <button type="submit"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-transparent bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-500">
                        Log in
                    </button>
                </form>
            </div>
        </div>

        <p class="mt-6 text-center text-xs text-gray-500">
            &copy; {{ date('Y') }} ICT Asset Management. All rights reserved.
        </p>
    </div>
</body>
</html>
